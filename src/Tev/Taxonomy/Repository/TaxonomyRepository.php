<?php
namespace Tev\Taxonomy\Repository;

use Tev\Taxonomy\Model\Taxonomy,
    Tev\Taxonomy\Factory as TaxonomyFactory;

/**
 * Taxonomy repository class.
 *
 * Provides methods for retrieving taxonomies from the database.
 */
class TaxonomyRepository
{
    /**
     * Taxonomy factory.
     *
     * @var \Tev\Taxonomy\Factory
     */
    private $taxonomyFactory;

    /**
     * Constructor.
     *
     * Inject dependencies.
     *
     * @param  \Tev\Taxonomy\Factory $taxonomyFactory Taxonomy factory
     * @return void
     */
    public function __construct(TaxonomyFactory $taxonomyFactory)
    {
        $this->taxonomyFactory = $taxonomyFactory;
    }
}
