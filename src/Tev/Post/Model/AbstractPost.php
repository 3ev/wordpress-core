<?php
namespace Tev\Post\Model;

use WP_Post;
use Carbon\Carbon;

use Tev\Contracts\WordpressWrapperInterface,
    Tev\Author\Factory as AuthorFactory,
    Tev\Taxonomy\Factory as TaxonomyFactory,
    Tev\Field\Factory as FieldFactory,
    Tev\Post\Factory as PostFactory,
    Tev\Taxonomy\Model\Taxonomy;

/**
 * Abstract post entity class.
 *
 * Provides a nicely object-oriented interface to a Wordpress post.
 */
abstract class AbstractPost implements WordpressWrapperInterface
{
    /**
     * Underlying `WP_Post` object.
     *
     * @var \WP_Post
     */
    private $base;

    /**
     * Author entity factory.
     *
     * @var \Tev\Author\Factory
     */
    private $authorFactory;

    /**
     * Taxonomy entity factory.
     *
     * @var \Tev\Taxonomy\Factory
     */
    private $taxonomyFactory;

    /**
     * Field entity factory.
     *
     * @var \Tev\Field\Factory
     */
    private $fieldFactory;

    /**
     * Post entity factory.
     *
     * @var \Tev\Post\Factory
     */
    private $postFactory;

    /**
     * Post author.
     *
     * @var \Tev\Author\Model\Author
     */
    private $author;

    /**
     * Featured image post.
     *
     * Will be false if no featured image exists for the post
     *
     * @var \Tev\Post\Model\Attachment|false
     */
    private $featuredImage;

    /**
     * Post type object.
     *
     * Contains information about the post type of this post.
     *
     * @var \stdClass
     */
    private $postTypeObj;

    /**
     * Parent post.
     *
     * @var \Tev\Post\Model\AbstractPost|false
     */
    private $parent;

    /**
     * Constructor.
     *
     * Inject dependencies.
     *
     * @param  \WP_Post              $base            Base Wordpress post
     * @param  \Tev\Author\Factory   $authorFactory   Author factory
     * @param  \Tev\Taxonomy\Factory $taxonomyFactory Taxonomy entity factory
     * @param  \Tev\Field\Factory    $fieldFactory    Field entity factory
     * @param  \Tev\Post\Factory     $postFactory     Post entity factory
     * @return void
     */
    public function __construct(WP_Post $base,
                                AuthorFactory $authorFactory,
                                TaxonomyFactory $taxonomyFactory,
                                FieldFactory $fieldFactory,
                                PostFactory $postFactory)
    {
        $this->base            = $base;
        $this->authorFactory   = $authorFactory;
        $this->taxonomyFactory = $taxonomyFactory;
        $this->fieldFactory    = $fieldFactory;
        $this->postFactory     = $postFactory;
        $this->author          = null;
        $this->featuredImage   = null;
        $this->postTypeObj     = null;
        $this->parent          = null;
    }

    /**
     * Get the post ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->base->ID;
    }

    /**
     * Get the post type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->base->post_type;
    }

    /**
     * Get the singular name for the post type of this post.
     *
     * @return string
     */
    public function getTypeName()
    {
        return $this->getTypeLabels()->singular_name;
    }

    /**
     * Get the post type labels for this post.
     *
     * See:
     *
     * http://codex.wordpress.org/Function_Reference/get_post_type_object
     *
     * for the list of labels returned.
     *
     * @return \stdClass
     */
    public function getTypeLabels()
    {
        return $this->getPostTypeObject()->labels;
    }

    /**
     * Get the post slug.
     *
     * @return string
     */
    public function getName()
    {
        return $this->base->post_name;
    }

    /**
     * Get the post status. One of:
     *
     * - publish
     * - pending
     * - draft
     * - auto-draft
     * - future
     * - private
     * - inherit
     * - trash
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->base->post_status;
    }

    /**
     * Get the post title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->base->post_title;
    }

    /**
     * Get the post content, formatted as HTML.
     *
     * @return string
     */
    public function getContent()
    {
        $content = $this->getRawContent();
        $content = apply_filters('the_content', $content);
        $content = str_replace( ']]>', ']]&gt;', $content);

        return $content;
    }

    /**
     * Get the raw post content.
     *
     * @return string
     */
    public function getRawContent()
    {
        return $this->base->post_content;
    }

    /**
     * Check if this post has a manually set excerpt.
     *
     * @return boolean True if has excerpt, false if not
     */
    public function hasExcerpt()
    {
        return (boolean) strlen($this->base->post_excerpt);
    }

    /**
     * Get the post excerpt.
     *
     * Adapted from wp_trim_excerpt() in wp-includes/formatting.php.
     *
     * @param  string|null $content Optional. Base content to use for excerpt
     *                              if post excerpt isn't defined. Defaults to
     *                              post content
     * @return string
     */
    public function getExcerpt($content = null)
    {
        $raw = $exc = $this->base->post_excerpt;

        if (!strlen($exc)) {
            $exc = $content === null ? $this->getRawContent() : $content;
            $exc = strip_shortcodes($exc);
            $exc = apply_filters('the_content', $exc);
            $exc = str_replace(']]>', ']]&gt;', $exc);
            $exc = wp_trim_words(
                $exc,
                apply_filters('excerpt_length', 55),
                apply_filters('excerpt_more', ' ' . '[&hellip;]')
            );
        }

        return apply_filters('wp_trim_excerpt', $exc, $raw);
    }

    /**
     * Get post URL.
     *
     * @param  array  $query Query string args. Key value pairs
     * @return string
     */
    public function getUrl($query = array())
    {
        return add_query_arg($query, get_permalink($this->getId()));
    }

    /**
     * Get this Post's featured image.
     *
     * @return \Tev\Post\Model\Attachment|false Featured image object, or false if not
     *                                          set
     */
    public function getFeaturedImage()
    {
        if ($this->featuredImage === null) {
            if ($imgId = get_post_thumbnail_id((int) $this->getId())) {
                $this->featuredImage = $this->postFactory->create(get_post($imgId));
            } else {
                $this->featuredImage = false;
            }
        }

        return $this->featuredImage;
    }

    /**
     * Get Post featured image URL.
     *
     * Convenient alias for:
     *
     * ```
     * $pos->getFeaturedImage()->getImageUrl($size);
     * ```
     *
     * @param  mixed       $size Arguments accepted by second param of wp_get_attachment_image_src()
     * @return string|null       Image URL, or null if no image set
     */
    public function getFeaturedImageUrl($size = 'thumbnail')
    {
        if ($img = $this->getFeaturedImage()) {
            return $img->getImageUrl($size);
        }

        return null;
    }

    /**
     * Get the post author ID.
     *
     * @return string
     */
    public function getAuthorId()
    {
        return $this->base->post_author;
    }

    /**
     * Get post author.
     *
     * @return \Tev\Author\Model\Author
     */
    public function getAuthor()
    {
        if ($this->author === null) {
            $this->author = $this->authorFactory->create($this->getAuthorId());
        }

        return $this->author;
    }

    /**
     * Get the parent post ID.
     *
     * @return int|null Parent post ID or null if no parent
     */
    public function getParentPostId()
    {
        return $this->base->post_parent ?: null;
    }

    /**
     * Get the parent post, if set.
     *
     * @return \Tev\Post\Model\AbstractPost|null
     */
    public function getParent()
    {
        if ($this->parent === null) {
            if (($parentId = $this->getParentPostId()) !== null) {
                $this->parent = $this->postFactory->create(get_post($parentId));
            } else {
                $this->parent = false;
            }
        }

        return $this->parent ?: null;
    }

    /**
     * Get the post published date.
     *
     * @return \Carbon\Carbon
     */
    public function getPublishedDate()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->base->post_date);
    }

    /**
     * Get the post published date in GMT.
     *
     * @return \Carbon\Carbon
     */
    public function getPublishedDateGmt()
    {
        return Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $this->base->post_date_gmt,
            'UTC'
        );
    }

    /**
     * Get the post modified date.
     *
     * @return \Carbon\Carbon
     */
    public function getModifiedDate()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->base->post_modified);
    }

    /**
     * Get the post modified date in GMT.
     *
     * @return \Carbon\Carbon
     */
    public function getModifiedDateGmt()
    {
        return Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $this->base->post_modified_gmt,
            'UTC'
        );
    }

    /**
     * Get all categories for this post.
     *
     * @return Tev\Term\Model\Term[] Array of categories
     */
    public function getCategories()
    {
        return $this->getTermsFor('category');
    }

    /**
     * Get all tags for this post.
     *
     * @return Tev\Term\Model\Term[] Array of tags
     */
    public function getTags()
    {
        return $this->getTermsFor('post_tag');
    }

    /**
     * Get all terms for this post in the given taxonomy.
     *
     * @param  mixed|\Tev\Taxonomy\Model\Taxonomy $taxonomy Taxonomy name or object
     * @return \Tev\Term\Model\Term[]                       Array of terms
     */
    public function getTermsFor($taxonomy)
    {
        if (!($taxonomy instanceof Taxonomy)) {
            $taxonomy = $this->taxonomyFactory->create($taxonomy);
        }

        return $taxonomy->getTermsForPost($this);
    }

    /**
     * Get a meta value on this post.
     *
     * See: http://codex.wordpress.org/Function_Reference/get_post_meta for
     * how `$single` works.
     *
     * @param  string  $key    Meta item key
     * @param  boolean $single Optional. Defaults to true
     * @return mixed           Meta data
     */
    public function meta($key, $single = true)
    {
        return get_post_meta($this->getId(), $key, $single);
    }

    /**
     * Get a custom field on this post.
     *
     * @param  string                         $name Field name (or key)
     * @return \Tev\Field\Model\AbstractField       Field object
     */
    public function field($name)
    {
        return $this->fieldFactory->create($name, $this);
    }

    /**
     * Get the underlying `WP_Post` object.
     *
     * @return \WP_Post
     */
    public function getBaseObject()
    {
        return $this->base;
    }

    /**
     * Get the post type object for this post.
     *
     * Contains information about the post type of this post.
     *
     * @return \stdClass
     */
    protected function getPostTypeObject()
    {
        if ($this->postTypeObj === null) {
            $this->postTypeObj = get_post_type_object($this->getType());
        }

        return $this->postTypeObj;
    }
}
