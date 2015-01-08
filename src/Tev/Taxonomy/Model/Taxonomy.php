<?php
namespace Tev\Taxonomy\Model;

use stdClass;

use Tev\Contracts\WordpressWrapperInterface,
    Tev\Term\Factory as TermFactory,
    Tev\Post\Model\AbstractPost;

/**
 * Wordpress Taxonomy entity.
 *
 * Provides a nicely object-oriented interface to a Wordpress taxonomy.
 */
class Taxonomy implements WordpressWrapperInterface
{
    /**
     * Underlying taxonomy object.
     *
     * @var \stdClass
     */
    private $base;

    /**
     * Term factory.
     *
     * @var \Tev\Term\Factory
     */
    private $termFactory;

    /**
     * Constructor.
     *
     * @param  \stdClass         $base        Underlying taxonomy object
     * @param  \Tev\Term\Factory $termFactory Term factory
     * @return void
     */
    public function __construct(stdClass $base,
                                TermFactory $termFactory)
    {
        $this->base        = $base;
        $this->termFactory = $termFactory;
    }

    /**
     * Get the taxonomy name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->base->name;
    }

    /**
     * Get the taxonomy plural label.
     *
     * @return string
     */
    public function getPluralLabel()
    {
        return $this->base->label;
    }

    /**
     * Get the taxonomy singular label.
     *
     * @return string
     */
    public function getSingularLabel()
    {
        return $this->base->singular_label;
    }

    /**
     * Get all Terms in this taxonomy.
     *
     * @param  boolean                     $topLevel Whether or not to just get top level terms
     * @param  array                       $options  Optional. Same options as passed to `get_terms()`
     * @return \Tev\Term\Model\Term[]                Array of terms
     */
    public function getTerms($topLevel = false, $options = array())
    {
        $terms = array();

        $wpTerms = get_terms($this->getName(), array_merge(array(
            'hide_empty' => false,
            'parent'     => $topLevel ? 0 : ''
        ), $options));

        foreach ($wpTerms as $termData) {
            $terms[] = $this->termFactory->create($termData, $this);
        }

        return $terms;
    }

    /**
     * Get all terms in this taxonomy for the given post.
     *
     * @param  \Tev\Post\Model\AbstractPost $post Post object to get terms for
     * @return array[\Tev\Term\Model\Term]        Array of terms
     */
    public function getTermsForPost(AbstractPost $post)
    {
        $terms = array();

        if ($dbTerms = get_the_terms($post->getId(), $this->getName())) {
            foreach ($dbTerms as $termData) {
                $terms[] = $this->termFactory->create($termData, $this);
            }
        }

        return $terms;
    }

    /**
     * Check whether or not this taxonomy is heirarchical.
     *
     * @return boolean True if heirarchical, false if not
     */
    public function isHierarchical()
    {
        return (boolean) $this->base->hierarchical;
    }

    /**
     * Get the underlying taxonomy object.
     *
     * @return \stdClass
     */
    public function getBaseObject()
    {
        return $this->base;
    }
}
