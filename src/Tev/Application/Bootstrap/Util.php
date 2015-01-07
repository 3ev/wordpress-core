<?php
namespace Tev\Application\Bootstrap;

use Tev\Application\Application,
    Tev\Contracts\BootstrapperInterface,
    Tev\Util\TemplateExtras;

/**
 * Bootstrap util classes.
 */
class Util implements BootstrapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application $app)
    {
        $app->bind('template_extras', function ($app) {
            return new TemplateExtras($app->fetch('post_factory'));
        });
    }
}
