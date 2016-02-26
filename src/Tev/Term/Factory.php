<?php
namespace Tev\Term;

use Exception;
use WP_Error;
use WP_Term;

use Tev\Term\Model\Term,
    Tev\Taxonomy\Model\Taxonomy;

/**
 * Term entity factory.
 *
 * Allows for easy instantiation of Term objects.
 */
class Factory
{
    /**
     * Create a new Term object.
     *
     * @param  int|\WP_Term                 $term     Term ID or term object
     * @param  \Tev\Taxonomy\Model\Taxonomy $taxonomy Term taxonomy
     * @return \Tev\Term\Model\Term                   Term object
     *
     * @throws \Exception If term could not be created from given data
     */
    public function create($term, Taxonomy $taxonomy)
    {
        if (!($term instanceof WP_Term)) {
            $term = get_term($term, $taxonomy->getName());
        }

        if ($term && !($term instanceof WP_Error)) {
            return new Term($term, $taxonomy, $this);
        } else {
            throw new Exception('Term not found');
        }
    }
}
