<?php
namespace Tev\Field\Model;

use WP_Post;
use Tev\Post\Factory as PostFactory;

/**
 * Post field.
 *
 * Returns a single Post object or array of Post objects.
 */
class PostField extends AbstractField
{
    /**
     * Post factory.
     *
     * @var \Tev\Post\Factory
     */
    private $postFactory;

    /**
     * Constructor.
     *
     * @param  array             $base        Base field data
     * @param  \Tev\Post\Factory $postFactory Post factory
     * @return void
     */
    public function __construct(array $base, PostFactory $postFactory)
    {
        parent::__construct($base);

        $this->postFactory = $postFactory;
    }

    /**
     * Get a single post object or array of post objects.
     *
     * If no posts are configured, returned will result will be an empty array
     * if this is a mutli-select, or null if not.
     *
     * @return \Tev\Post\Model\Post|\Tev\Post\Model\Post[]|null
     */
    public function getValue()
    {
        $val = $this->base['value'];

        if (is_array($val)) {
            $posts = array();

            foreach ($val as $p) {
                $posts[] = $this->postFactory->create($this->getPostObject($p));
            }

            return $posts;
        } elseif (strlen($val)) {
            return $this->postFactory->create($this->getPostObject($val));
        } else {
            if (isset($this->base['multiple']) && $this->base['multiple']) {
                return array();
            } else {
                return null;
            }
        }
    }

    /**
     * Get a string representation of this field.
     *
     * Post title or titles, comma-space separated.
     *
     * @return string
     */
    public function __toString()
    {
        $post = $this->getValue();

        if (is_array($post)) {
            return array_reduce($post, function ($string, $p) {
                return $string . (strlen($string) ? ', ' : '') . $p->getTitle();
            }, '');
        } elseif ($post !== null) {
            return $post->getTitle();
        } else {
            return '';
        }
    }

    /**
     * If given $post is an ID, get a WP_Post object from it.
     *
     * @param  int|\WP_Post $post Post ID or object
     * @return \WP_Post           Post object
     */
    private function getPostObject($post)
    {
        if (!($post instanceof WP_Post)) {
            return get_post($post);
        }

        return $post;
    }
}
