<?php
namespace Tev\Application\Bootstrap;

use Tev\Application\Application,
    Tev\Contracts\BootstrapperInterface,
    Tev\Plugin\Loader;

/**
 * Bootstrap the plugin loader.
 */
class PluginLoader implements BootstrapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application $app)
    {
        $app->bind('plugin_loader', function ($app) {
            return new Loader($app);
        });
    }
}
