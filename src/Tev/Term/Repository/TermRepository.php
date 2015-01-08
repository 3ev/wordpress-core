<?php
namespace Tev\Term\Repository;

use Tev\Taxonomy\Model\Taxonomy,
    Tev\Taxonomy\Factory as TaxonomyFactory,
    Tev\Term\Factory as TermFactory;

/**
 * Term repository class.
 *
 * Provides methods for retrieving terms from the database.
 */
class TermRepository
{
    /**
     * Taxonomy factory.
     *
     * @var \Tev\Taxonomy\Factory
     */
    private $taxonomyFactory;

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
     * @param  \Tev\Taxonomy\Factory $taxonomyFactory Taxonomy factory
     * @param  \Tev\Term\Factory     $termFactory     Term factory
     * @return void
     */
    public function __construct(TaxonomyFactory $taxonomyFactory,
                                TermFactory $termFactory)
    {
        $this->taxonomyFactory = $taxonomyFactory;
        $this->termFactory     = $termFactory;
    }

    /**
     * Get all terms in the given taxonomy.
     *
     * @param  string|\Tev\Taxonomy\Model\Taxonomy $taxonomy Taxonomy object or name
     * @return \Tev\Term\Model\Term[]
     */
    public function getByTaxonomy($taxonomy)
    {
        if (!($taxonomy instanceof Taxonomy)) {
            $taxonomy = $this->taxonomyFactory->create($taxonomy);
        }

        return $this->convertTermsArray(get_terms($taxonomy->getName(), array(
            'hide_empty' => false
        )), $taxonomy);
    }

    /**
     * Convert an array of Wordpress term objects to array of Term objects.
     *
     * @param  \stdClass[]                  $terms    Wordpress term objects
     * @param  \Tev\Taxonomy\Model\Taxonomy $taxonomy Parent taxonomy
     * @return \Tev\Term\Model\Term[]                 Term objects
     */
    private function convertTermsArray($terms, Taxonomy $taxonomy)
    {
        $res = array();

        foreach ($terms as $t) {
            $res[] = $this->termFactory->create($t, $taxonomy);
        }

        return $res;
    }
}
