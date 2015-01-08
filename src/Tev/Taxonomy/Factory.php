<?php
namespace Tev\Taxonomy;

use stdClass;

use Tev\Taxonomy\Model\Taxonomy,
    Tev\Term\Factory as TermFactory;

/**
 * Taxonomy entity factory.
 *
 * Allows for easy instantiation of Taxonomy objects.
 */
class Factory
{
    /**
     * Term factory.
     *
     * @var \Tev\Term\Factory
     */
    private $termFactory;

    /**
     * Constructor.
     *
     * Inject dependencies.
     *
     * @param  \Tev\Term\Factory $termFactory Term factory
     * @return void
     */
    public function __construct(TermFactory $termFactory)
    {
        $this->termFactory = $termFactory;
    }

    /**
     * Create a new Taxonomy object.
     *
     * @param  string|\stdClass             $taxonomy Taxonomy name or object
     * @return \Tev\Taxonomy\Model\Taxonomy           Created taxonomy
     */
    public function create($taxonomy)
    {
        if (!($taxonomy instanceof stdClass)) {
            $taxonomy = get_taxonomy($taxonomy);
        }

        return new Taxonomy($taxonomy, $this->termFactory);
    }
}
