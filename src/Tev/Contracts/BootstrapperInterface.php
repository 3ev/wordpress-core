<?php
namespace Tev\Contracts;

use Tev\Application\Application;

/**
 * Classes that implement this interface should provide a bootstrap method,
 * into which they should bootstrap application services.
 */
interface BootstrapperInterface
{
    /**
     * Bootstrap application services.
     *
     * @param  \Tev\Application\Application $app Application instance
     * @return void
     */
    public function bootstrap(Application $app);
}
