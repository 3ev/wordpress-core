<?php
namespace Tev\Application;

use Closure;
use Pimple\Container;
use Tev\Contracts\BootstrapperInterface;

/**
 * Tev plugin application.
 *
 * Service locator for all application services.
 */
class Application
{
    /**
     * Application instance.
     *
     * @var \Tev\Application\Application
     */
    private static $instance = null;

    /**
     * DI container.
     *
     * @var \Pimple\Container
     */
    private $container;

    /**
     * Array of default services to bootstrap.
     *
     * @var array
     */
    protected $bootstraps = array(
        'Tev\Application\Bootstrap\PluginLoader',
        'Tev\Application\Bootstrap\AuthorFactory',
        'Tev\Application\Bootstrap\Term',
        'Tev\Application\Bootstrap\Taxonomy',
        'Tev\Application\Bootstrap\FieldFactory',
        'Tev\Application\Bootstrap\Post',
        'Tev\Application\Bootstrap\Util'
    );

    /**
     * Get the singleton application instance.
     *
     * @return \Tev\Application\Application
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static;
            self::$instance->bootstrap();
        }

        return self::$instance;
    }

    /**
     * Constructor.
     *
     * Private for singleton. Constructs container.
     *
     * @return void
     */
    private function __construct()
    {
        $this->container = new Container;
    }

    /**
     * Bind a service into the container.
     *
     * @param  string                       $name Service name
     * @param  \Closure                     $bind Service location closure. Receives \Tev\Application\Application as argument
     * @return \Tev\Application\Application       This, for chaining
     */
    public function bind($name, Closure $bind)
    {
        $app = $this;

        $this->container[$name] = function ($c) use ($app, $bind) {
            return $bind($app);
        };

        return $this;
    }

    /**
     * Fetch a service from the container.
     *
     * @param  string $name Service name
     * @return mixed        Bound service
     */
    public function fetch($name)
    {
        return $this->container[$name];
    }

    /**
     * Bootstrap default services into the container.
     *
     * @return \Tev\Application\Application This, for chaining
     */
    protected function bootstrap()
    {
        foreach ($this->bootstraps as $bClass) {
            $b = new $bClass;

            if ($b instanceof BootstrapperInterface) {
                $b->bootstrap($this);
            }
        }

        return $this;
    }
}
