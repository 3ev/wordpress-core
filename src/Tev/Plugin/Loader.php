<?php
namespace Tev\Plugin;

use Closure;
use wpdb;
use Tev\Application\Application,
    Tev\View\Renderer;

/**
 * Simple plugin loading utility.
 *
 * Provides framework for loading plugin content by convention, as follows:
 *
 * - Custom post types are loaded from an array, defined in `config/post_types.php`
 *   in the plugin's directory. The array is a set of key value pairs, where
 *   the key is the post type identifier and the value is the config to pass
 *   to `register_post_type`
 *
 * - Custom field groups are loaded from an array, in `config/field_groups.php`
 *   in the plugin's directory. The array is a set of arrays, defining each
 *   field group config to pass to `register_field_group`
 *
 * - ACF JSON config is loaded from a directory, at `config/acf-json/`
 *   in the plugin's directory. See http://www.advancedcustomfields.com/resources/local-json/
 *   for more information on this
 *
 * - Action callbacks are loaded from an array, in `config/actions.php` in the
 *   plugin's directory. The array is a set of key-value pairs, of action names
 *   to closure or action provider classes
 *
 * - Shortcodes are loaded from an array, in `config/shortcodes.php` in the
 *   plugin's directory. The array is a set of key value pairs, where
 *   the key is the shortcode name and the value is the config to pass
 *   to `add_shortcode`
 *
 * - Custom WP CLI commands are loaded from an array, defined in `config/commands.php`
 *   in the plugin's directory. The array is a set of key value pairs, where the
 *   key is the command indentifier and the value is the fully-qualified class
 *   name
 *
 * - Custom database table installers are loaded from an array, defined in
 *   `config/tables.php` in the plugin's directory. The array is a set of
 *   fully-qualified installer class names
 *
 * Usage:
 *
 * ```php
 * // Add the following to your plugin's config file
 *
 * tev_fetch('plugin_loader')->load(__DIR__);
 * ```
 *
 */
class Loader
{
    /**
     * Application.
     *
     * @var \Tev\Application\Application
     */
    protected $app;

    /**
     * Plugin base path.
     *
     * @var string
     */
    protected $basePath;

    /**
     * View renderer.
     *
     * @var \Tev\View\Renderer
     */
    protected $renderer;

    /**
     * Constructor.
     *
     * Inject dependencies.
     *
     * @param  \Tev\Application\Application $app Application
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Load all plugin configuration.
     *
     * @param  string             $basePath Plugin path
     * @return \Tev\Plugin\Loader           This, for chaining
     */
    public function load($basePath)
    {
        $this->basePath = $basePath;
        $this->renderer = new Renderer($this->getViewsPath());

        return
            $this
                ->loadCustomTables()
                ->loadPostTypes()
                ->loadFieldGroups()
                ->loadAcfJson()
                ->loadActions()
                ->loadShortCodes()
                ->loadOptionScreens()
                ->loadCliCommands();
    }

    /**
     * Load custom database table installers from configuration files.
     *
     * @return \Tev\Plugin\Loader This, for chaining
     */
    protected function loadCustomTables()
    {
        if ($config = $this->loadConfigFile('tables.php')) {
            $app = $this->app;

            foreach ($config as $installerClass) {
                if (is_string($installerClass) && is_subclass_of($installerClass, 'Tev\Database\CustomTables\AbstractInstaller')) {
                    register_activation_hook($this->getPluginFile(), function () use ($installerClass, $app) {
                        global $wpdb;
                        $installer = new $installerClass($wpdb, $app);
                        $installer->install();
                    });

                    add_action('plugins_loaded', function () use ($installerClass, $app) {
                        global $wpdb;
                        $installer = new $installerClass($wpdb, $app);
                        $installer->update();
                    });
                }
            }
        }

        return $this;
    }

    /**
     * Load custom post types from configuration files.
     *
     * @return \Tev\Plugin\Loader This, for chaining
     */
    protected function loadPostTypes()
    {
        if ($config = $this->loadConfigFile('post_types.php')) {
            $callbacks = array();

            // Create one callback for each post type, and register in
            // init action

            foreach ($config as $postTypeName => $args) {
                $callbacks[] = $cb = function () use ($postTypeName, $args) {
                    register_post_type($postTypeName, $args);
                };

                add_action('init', $cb, 0);
            }

            // Flush URL caches for (you need to register custom post types
            // first)

            register_activation_hook($this->getPluginFile(), function () use ($callbacks) {
                foreach ($callbacks as $cb) {
                    $cb();
                }

                flush_rewrite_rules();
            });
        }

        return $this;
    }

    /**
     * Load custom field groups from configuration files.
     *
     * @return \Tev\Plugin\Loader This, for chaining
     */
    protected function loadFieldGroups()
    {
        if (function_exists('register_field_group') && ($config = $this->loadConfigFile('field_groups.php'))) {
            foreach ($config as $fieldGroupConfig) {
                register_field_group($fieldGroupConfig);
            }
        }

        return $this;
    }

    /**
     * Load actions from configuration providers.
     *
     * @return \Tev\Plugin\Loader This, for chaining
     */
    protected function loadActions()
    {
        if ($config = $this->loadConfigFile('actions.php')) {
            $app      = $this->app;
            $renderer = $this->renderer;

            foreach ($config as $actionName => $provider) {
                if (is_string($provider) && is_subclass_of($provider, 'Tev\Plugin\Action\AbstractProvider')) {
                    $ap = new $provider($this->app, $this->renderer);

                    add_action($actionName, function () use ($ap) {
                        return call_user_func_array(array($ap, 'action'), func_get_args());
                    }, $ap->priority(), $ap->numArgs());
                } elseif ($provider instanceof Closure) {
                    add_action($actionName, function () use ($provider)
                    {
                        return call_user_func($provider, func_get_args());
                    });
                }
            }
        }

        return $this;
    }

    /**
     * Load ACF JSON config if supplied.
     *
     * @return \Tev\Plugin\Loader This, for chaining
     */
    protected function loadAcfJson()
    {
        $config = $this->getConfigPath() . '/acf-json';

        if (file_exists($config)) {
            add_filter('acf/settings/load_json', function ($paths) use ($config) {
                $paths[] = $config;
                return $paths;
            });
        }

        return $this;
    }

    /**
     * Load shortcodes from configuration files.
     *
     * @return \Tev\Plugin\Loader This, for chaining
     */
    protected function loadShortCodes()
    {
        if ($config = $this->loadConfigFile('shortcodes.php')) {
            $renderer = $this->renderer;
            $app      = $this->app;

            foreach ($config as $shortcode => $provider) {
                add_shortcode($shortcode, function ($attrs, $content) use ($app, $provider, $renderer)
                {
                    if (is_string($provider) && is_subclass_of($provider, 'Tev\Plugin\Shortcode\AbstractProvider')) {
                        $sp = new $provider($app, $renderer);
                        return $sp->shortcode($attrs, $content);
                    } elseif ($provider instanceof Closure) {
                        return $provider($attrs, $content, $renderer);
                    }
                });
            }
        }

        return $this;
    }

    /**
     * Load custom option screens from configuration files.
     *
     * @return \Tev\Plugin\Loader This, for chaining
     */
    protected function loadOptionScreens()
    {
        if (function_exists('acf_add_options_page') && ($config = $this->loadConfigFile('option_screens.php'))) {
            foreach ($config as $optionScreenConfig) {
                acf_add_options_page($optionScreenConfig);
            }
        }

        return $this;
    }

    /**
     * Load custom WP CLI commands from configuration files.
     *
     * @return \Tev\Plugin\Loader This, for chaining
     */
    protected function loadCliCommands()
    {
        if (defined('WP_CLI') && WP_CLI && ($config = $this->loadConfigFile('commands.php'))) {
            foreach ($config as $command => $className) {
                \WP_CLI::add_command($command, $className);
            }
        }

        return $this;
    }

    /**
     * Get the path to the config directory.
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return $this->basePath . '/config';
    }

    /**
     * Get the path to the config directory.
     *
     * @return string
     */
    protected function getSrcPath()
    {
        return $this->basePath . '/src';
    }

    /**
     * Get the path to the config directory.
     *
     * @return string
     */
    protected function getViewsPath()
    {
        return $this->basePath . '/src/views';
    }

    /**
     * Get the full path and filename of the plugin's registration file.
     *
     * @return string
     */
    protected function getPluginFile()
    {
        return $this->basePath . '/' . strtolower(basename($this->basePath)) . '.php';
    }

    /**
     * Load a config file from the config directory.
     *
     * @param  string     $file Filename
     * @return array|null       Array config or null if not found
     */
    protected function loadConfigFile($file)
    {
        $path = $this->getConfigPath() . '/' . $file;

        if (file_exists($path)) {
            $config = include $path;
            return is_array($config) ? $config : null;
        }

        return null;
    }
}
