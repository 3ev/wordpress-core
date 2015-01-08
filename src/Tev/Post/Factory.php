<?php
namespace Tev\Post;

use Exception;

use Tev\Post\Model\Post,
    Tev\Author\Factory as AuthorFactory,
    Tev\Taxonomy\Factory as TaxonomyFactory,
    Tev\Field\Factory as FieldFactory;

/**
 * Post entity factory.
 *
 * Allows for easy instantiation of post entity objects. Classes can be
 * instantiated based on their post type. 'post' and 'page' are registered
 * by default.
 */
class Factory
{
    /**
     * Post type to entity class mappings.
     *
     * @var array
     */
    private $registeredMappings;

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
     * Constructor.
     *
     * Inject dependencies.
     *
     * @param  \Tev\Author\Factory   $authorFactory   Author object factory
     * @param  \Tev\Taxonomy\Factory $taxonomyFactory Taxonomy entity factory
     * @param  \Tev\Field\Factory    $fieldFactory    Field entity factory
     * @return void
     */
    public function __construct(AuthorFactory $authorFactory,
                                TaxonomyFactory $taxonomyFactory,
                                FieldFactory $fieldFactory)
    {
        $this->authorFactory      = $authorFactory;
        $this->taxonomyFactory    = $taxonomyFactory;
        $this->fieldFactory       = $fieldFactory;
        $this->registeredMappings = array();
    }

    /**
     * Register a new post type to class name mapping.
     *
     * @param  string            $postType  Post type identifier
     * @param  string            $className Class name to instantiate for $postType. Class
     *                                      must inherit from `\Tev\Post\Model\AbstractPost`
     * @return \Tev\Post\Factory            This, for chaining
     *
     * @throws \Exception If class name does not inherit from `\Tev\Post\Model\AbstractPost`
     */
    public function register($postType, $className)
    {
        if (!is_subclass_of($className, 'Tev\Post\Model\AbstractPost')) {
            throw new Exception("Given class '$className' is not instance of 'Tev\Post\Model\AbstractPost'");
        }

        $this->registeredMappings[$postType] = $className;

        return $this;
    }

    /**
     * Get the registered class name for the given post type.
     *
     * @param  string      $postType Post type identifier
     * @return string|null           Class name or null if no registration
     */
    public function registered($postType)
    {
        if (isset($this->registeredMappings[$postType])) {
            return $this->registeredMappings[$postType];
        }

        return null;
    }

    /**
     * Instantiate a post entity from a given post object.
     *
     * @param  \WP_Post                     $base      Optional. Base post object. If not
     *                                                 supplied will attempt to get the current
     *                                                 post from The Loop
     * @param  string                       $className Optional. Class name to instantiate. Will
     *                                                 use registered default if not supplied
     * @return \Tev\Post\Model\AbstractPost            Post entity
     *
     * @throws \Exception If $base is not given and not in the The Loop
     */
    public function create($base = null, $className = null)
    {
        if ($base === null) {
            if (have_posts()) {
                the_post();
                $base = get_post();
            } else {
                throw new Exception('Please supply a post object when not within The Loop');
            }
        }

        $cls = 'Tev\Post\Model\Post';

        if (($className !== null) && is_subclass_of($className, 'Tev\Post\Model\AbstractPost')) {
            $cls = $className;
        } elseif ($className = $this->registered($base->post_type)) {
            $cls = $className;
        }

        return new $cls($base, $this->authorFactory, $this->taxonomyFactory, $this->fieldFactory, $this);
    }

    /**
     * Instantiate a post entity from the current post object.
     *
     * Only works within The Loop.
     *
     * @param  string                       $className Optional. Class name to instantiate. Will
     *                                                 use registered default if not supplied
     * @return \Tev\Post\Model\AbstractPost            Post entity
     *
     * @throws \Exception If not in the The Loop
     */
    public function current($className = null)
    {
        if ($p = get_post()) {
            return $this->create($p, $className);
        } else {
            throw new Exception('This method can only be called within The Loop');
        }
    }
}
