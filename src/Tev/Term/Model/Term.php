<?php
namespace Tev\Term\Model;

use WP_Term;

use Tev\Contracts\WordpressWrapperInterface,
    Tev\Term\Factory as TermFactory,
    Tev\Taxonomy\Model\Taxonomy;

/**
 * Wordpress Term entity.
 *
 * Provides a nicely object-oriented interface to a Wordpress taxonomy term.
 */
class Term implements WordpressWrapperInterface
{
    /**
     * Base term oject.
     *
     * @var \WP_Term
     */
    private $base;

    /**
     * Term taxonomy.
     *
     * @var \Tev\Taxonomy\Model\Taxonomy
     */
    private $taxonomy;

    /**
     * Term factory.
     *
     * @var \Tev\Term\Factory
     */
    private $termFactory;

    /**
     * Parent term.
     *
     * @var \Tev\Term\Model\Term
     */
    private $parent;

    /**
     * Constructor.
     *
     * Inject dependencies.
     *
     * @param  \WP_Term                     $base        Base term oject
     * @param  \Tev\Taxonomy\Model\Taxonomy $taxonomy    Term taxonomy
     * @param  \Tev\Term\Factory            $termFactory Term factory
     * @return void
     */
    public function __construct(WP_Term $base,
                                Taxonomy $taxonomy,
                                TermFactory $termFactory)
    {
        $this->base        = $base;
        $this->taxonomy    = $taxonomy;
        $this->termFactory = $termFactory;
        $this->parent      = null;
    }

    /**
     * Get the term ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->base->term_id;
    }

    /**
     * Get the term name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->base->name;
    }

    /**
     * Get the term slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->base->slug;
    }

    /**
     * Get the taxonomy for this term.
     *
     * @return \Tev\Taxonomy\Model\Taxonomy
     */
    public function getTaxonomy()
    {
        return $this->taxonomy;
    }

    /**
     * Get the term description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->base->description;
    }

    /**
     * Get term URL.
     *
     * @param  array  $query Query string args. Key value pairs
     * @return string
     */
    public function getUrl($query = array())
    {
        return add_query_arg($query, get_term_link($this->base));
    }

    /**
     * Get the parent term ID of this term.
     *
     * @return int|null
     */
    public function getParentId()
    {
        return $this->base->parent ?: null;
    }

    /**
     * Get the parent term of this term.
     *
     * @return \Tev\Term\Model\Term
     */
    public function getParent()
    {
        if (($this->parent === null) && $this->getParentId()) {
            $this->parent = $this->termFactory->create($this->getParentId(), $this->taxonomy);
        }

        return $this->parent;
    }

    /**
     * Check whether or not this Term has a parent Term.
     *
     * @return boolean True if has a parent, false if not
     */
    public function hasParent()
    {
        return (boolean) $this->getParentId();
    }

    /**
     * Return array of direct child terms of this term.
     *
     * @return \Tev\Term\Model\Term[]
     */
    public function getChildren()
    {
        $children = array();

        $wpTerms = get_terms($this->taxonomy->getName(), array(
            'hide_empty' => false,
            'parent'     => (int) $this->getId()
        ));

        foreach ($wpTerms as $termData) {
            $children[] = $this->termFactory->create($termData, $this->taxonomy);
        }

        return $children;
    }

    /**
     * Get the underlying term object.
     *
     * @return \WP_Term
     */
    public function getBaseObject()
    {
        return $this->base;
    }
}
