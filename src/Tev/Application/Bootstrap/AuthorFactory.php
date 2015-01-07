<?php
namespace Tev\Application\Bootstrap;

use Tev\Application\Application,
    Tev\Contracts\BootstrapperInterface,
    Tev\Author\Factory;

/**
 * Bootstrap the author factory.
 */
class AuthorFactory implements BootstrapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application $app)
    {
        $app->bind('author_factory', function ($app) {
            return new Factory;
        });
    }
}
