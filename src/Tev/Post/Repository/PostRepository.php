<?php
namespace Tev\Post\Repository;

use WP_Query;
use Tev\Post\Factory as PostFactory;

/**
 * Post repository class.
 *
 * Provides methods for retrieving posts from the database.
 */
class PostRepository
{
    /**
     * Post factory.
     *
     * @var \Tev\Post\Factory
     */
    protected $postFactory;

    /**
     * Constructor.
     *
     * Inject dependencies.
     *
     * @param  \Tev\Post\Factory $postFactory Post factory
     * @return void
     */
    public function __construct(PostFactory $postFactory)
    {
        $this->postFactory = $postFactory;
    }

    /**
     * Get all posts of the given type.
     *
     * @param  string                         $type      Post type
     * @param  array                          $queryVars Extra query vars. See the params passed to WP_Query
     * @return \Tev\Post\Model\AbstractPost[]            Post objects
     */
    public function getAllByPostType($type, array $queryVars = array())
    {
        $q = new WP_Query(array_merge(array(
            'post_type' => $type,
            'nopaging'  => true
        ), $queryVars));

        $posts = array();

        foreach ($q->get_posts() as $p) {
            $posts[] = $this->postFactory->create($p);
        }

        return $posts;
    }

    /**
     * Get array of post years, in descending order.
     *
     * @return array
     */
    public function getYears()
    {
        $years = array();

        foreach ($this->getAllByPostType('post') as $post) {
            $year = (int) $post->getPublishedDate()->format('Y');

            if (!in_array($year, $years)) {
                $years[] = $year;
            }
        }

        rsort($years);

        return $years;
    }
}
