<?php
namespace Tev\Author\Model;

use WP_User;
use Tev\Contracts\WordpressWrapperInterface;

/**
 * Author entity class.
 *
 * Provides a nicely object-oriented interface to a Wordpress post author.
 */
class Author implements WordpressWrapperInterface
{
    /**
     * Base `WP_User` object.
     *
     * @var \WP_User
     */
    private $base;

    /**
     * Constructor.
     *
     * Inject dependencies.
     *
     * @param  \WP_User $base Base `WP_User` object
     * @return void
     */
    public function __construct(WP_User $base)
    {
        $this->base = $base;
    }

    /**
     * Get the author ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->base->ID;
    }

    /**
     * Get the author first name.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->base->first_name;
    }

    /**
     * Get the author last first name.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->base->last_name;
    }

    /**
     * Get the author nice name.
     *
     * @return string
     */
    public function getNiceName()
    {
        return $this->base->user_nicename;
    }

    /**
     * Get the author display name.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->base->display_name;
    }

    /**
     * Get the author email address.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->base->user_email;
    }

    /**
     * Get the author URL (link to their own blog or website).
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->base->user_url;
    }

    /**
     * Get the URL to the author's posts on this app.
     *
     * @return string
     */
    public function getPostsUrl()
    {
        return get_author_posts_url($this->getId());
    }

    /**
     * Get the total number of published posts the author has.
     *
     * @return int
     */
    public function getPostCount()
    {
        return count_user_posts($this->getId());
    }

    /**
     * Get a meta field value for this author.
     *
     * @param  string $field Field name
     * @return string        Field data. Empty string if field does not exist
     */
    public function getMeta($field)
    {
        return get_the_author_meta($field, $this->getId());
    }

    /**
     * Get an image tag for this author's avatar.
     *
     * @param  int    $size Avatar size. Max 512, default 96
     * @param  string $alt  Alt text. Defaults to display name
     * @return string
     */
    public function getAvatarTag($size = 96, $alt = null)
    {
        return get_avatar($this->getId(), $size, null, $alt ?: $this->getDisplayName());
    }

    /**
     * Get the underlying `WP_User` object.
     *
     * @return \WP_User
     */
    public function getBaseObject()
    {
        return $this->base;
    }
}
