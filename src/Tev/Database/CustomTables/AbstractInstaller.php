<?php
namespace Tev\Database\CustomTables;

use wpdb;
use Tev\Application\Application;

/**
 * Run install scripts for custom database tables.
 *
 * See: http://codex.wordpress.org/Creating_Tables_with_Plugins for more
 * information.
 */
abstract class AbstractInstaller
{
    /**
     * WPDB API.
     *
     * @var \wpdb
     */
    protected $wpdb;

    /**
     * Application container.
     *
     * @var \Tev\Application\Application
     */
    protected $app;

    /**
     * Constructor.
     *
     * Inject dependencies.
     *
     * @param  \wpdb                        $wpdb WPDB API
     * @param  \Tev\Application\Application $app  Application container
     * @return void
     */
    public function __construct(wpdb $wpdb, Application $app)
    {
        $this->wpdb = $wpdb;
        $this->app = $app;
    }

    /**
     * Return the SQL statement that will install/update the database for
     * this installer.
     *
     * See http://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table
     * for SQL idiosyncrasies in Wordpress.
     *
     * @return string SQL string
     */
    abstract protected function getSql();

    /**
     * Get the current database version.
     *
     * Should be updated every time the database needs to be updated.
     *
     * @return string Semver string, like 1.0.0
     */
    abstract protected function getVersion();

    /**
     * Install the database for this first.
     *
     * @return void
     */
    public function install()
    {
        $this->run();
    }

    /**
     * Update the database if it's out of date.
     *
     * @return void
     */
    public function update()
    {
        if ($this->getVersion() !== $this->getCurrentVersion()) {
            $this->run();
        }
    }

    /**
     * Run database scripts.
     *
     * See http://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table
     * for SQL idiosyncrasies in Wordpress.
     *
     * @return void
     */
    protected function run()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($this->getSql());

        $this->setCurrentVersion();
    }

    /**
     * Get the current installed database version.
     *
     * @return string|null
     */
    private function getCurrentVersion()
    {
        return get_option($this->getVersionOptionKey(), null);
    }

    /**
     * Set the current installed database version.
     *
     * @return \Lsg\Database\LocationsInstaller This, for chaining
     */
    private function setCurrentVersion()
    {
        if ($this->getCurrentVersion() !== null) {
            update_option($this->getVersionOptionKey(), $this->getVersion());
        } else {
            add_option($this->getVersionOptionKey(), $this->getVersion());
        }

        return $this;
    }

    /**
     * Get the option key to store the database version in.
     *
     * @return string
     */
    private function getVersionOptionKey()
    {
        return strtolower(str_replace('\\', '_', get_class($this))) . '_version';
    }
}
