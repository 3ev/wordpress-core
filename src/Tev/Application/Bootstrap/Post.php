<?php
namespace Tev\Application\Bootstrap;

use Tev\Application\Application,
    Tev\Contracts\BootstrapperInterface,
    Tev\Post\Factory,
    Tev\Post\Repository\PostRepository;

/**
 * Bootstrap the post library.
 */
class Post implements BootstrapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(Application $app)
    {
        // Bind a post factory instance

        $app->bind('post_factory', function ($app) {
            $f = new Factory(
                $app->fetch('author_factory'),
                $app->fetch('taxonomy_factory'),
                $app->fetch('field_factory')
            );

            // Register defaut post types

            return $f
                ->register('post',       'Tev\Post\Model\Post')
                ->register('page',       'Tev\Post\Model\Page')
                ->register('attachment', 'Tev\Post\Model\Attachment');
        });

        // Bind a post repository instance

        $app->bind('post_repo', function ($app) {
            return new PostRepository($app->fetch('post_factory'));
        });
    }
}
