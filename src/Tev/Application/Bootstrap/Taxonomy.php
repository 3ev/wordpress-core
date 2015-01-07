<?php
namespace Tev\Application\Bootstrap;

use Tev\Application\Application,
    Tev\Contracts\BootstrapperInterface,
    Tev\Taxonomy\Factory,
    Tev\Taxonomy\Repository\TaxonomyRepository;

/**
 * Bootstrap the taxonomy library.
 */
class Taxonomy implements BootstrapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application $app)
    {
        // Bind a taxonomy factory instance

        $app->bind('taxonomy_factory', function ($app) {
            return new Factory($app->fetch('term_factory'));
        });

        // Bind a taxonomy repository instance

        $app->bind('taxonomy_repo', function ($app) {
            return new TaxonomyRepository($app->fetch('taxonomy_factory'));
        });
    }
}
