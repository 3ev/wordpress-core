<?php
namespace Tev\Application\Bootstrap;

use Tev\Application\Application,
    Tev\Contracts\BootstrapperInterface,
    Tev\Term\Factory,
    Tev\Term\Repository\TermRepository;

/**
 * Bootstrap the term library.
 */
class Term implements BootstrapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application $app)
    {
        // Bind a term factory instance

        $app->bind('term_factory', function ($app) {
            return new Factory;
        });

        // Bind a term repository instance

        $app->bind('term_repo', function ($app) {
            return new TermRepository(
                $app->fetch('taxonomy_factory'),
                $app->fetch('term_factory')
            );
        });
    }
}
