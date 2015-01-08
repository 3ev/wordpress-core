<?php
namespace Tev\Author;

use Exception;
use WP_User;
use Tev\Author\Model\Author;

/**
 * Author entity factory.
 *
 * Allows for easy instantiation of author entity objects.
 */
class Factory
{
    /**
     * Create an author from a Wordpress user ID.
     *
     * If in the loop and `$id` is omitted, will attempt to get the current
     * post author.
     *
     * @param  int                      $id Optional. User ID. Will use ID from The Loop if omitted
     * @return \Tev\Author\Model\Author     Create author
     *
     * @throws \Exception If `$id` not supplied and not within the loop
     */
    public function create($id = null)
    {
        if ($id === null) {
            if ($id = get_the_author_meta('ID')) {
                return new Author(new WP_User($id));
            } else {
                throw new Exception('Please supply an author ID when not within The Loop');
            }
        }

        return new Author(new WP_User($id));
    }
}
