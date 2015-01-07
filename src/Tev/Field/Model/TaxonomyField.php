<?php
namespace Tev\Field\Model;

use Tev\Taxonomy\Factory as TaxonomyFactory,
    Tev\Term\Factory as TermFactory;

/**
 * Taxonomy field.
 *
 * Gives a single Taxonomy Term, or array of Taxonomy Terms.
 */
class TaxonomyField extends AbstractField
{
    /**
     * Taxonomy factory
     *
     * @var \Tev\Taxonomy\Factory
     */
    private $taxonomyFactory;

    /**
     * Term factory
     *
     * @var \Tev\Term\Factory
     */
    private $termFactory;

    /**
     * Term taxonomy.
     *
     * @var \Tev\Taxonomy\Model\Taxonomy
     */
    private $_taxonomy;

    /**
     * Constructor.
     *
     * @param  array                 $base            Base field data
     * @param  \Tev\Taxonomy\Factory $taxonomyFactory Taxonomy factory
     * @param  \Tev\Term\Factory     $termFactory     Term factory
     * @return void
     */
    public function __construct(array $base,
                                TaxonomyFactory $taxonomyFactory,
                                TermFactory $termFactory)
    {
        parent::__construct($base);

        $this->taxonomyFactory = $taxonomyFactory;
        $this->termFactory     = $termFactory;
        $this->_taxonomy       = null;
    }

    /**
     * Get Term or array of Terms.
     *
     * @return \Tev\Term\Model\Term|\Tev\Term\Model\Term[]|null
     */
    public function getValue()
    {
        $terms = $this->base['value'];

        if (is_array($terms)) {
            $ret = array();

            foreach ($terms as $t) {
                $ret[] = $this->termFactory->create($t, $this->taxonomy());
            }

            return $ret;
        } elseif (is_object($terms)) {
            return $this->termFactory->create($terms, $this->taxonomy());
        } else {
            if ($this->base['multiple'] || $this->base['field_type'] === 'multi_select' || $this->base['field_type'] === 'checkbox') {
                return array();
            } else {
                return null;
            }
        }
    }

    /**
     * Get Term Taxonomy.
     *
     * @return \Tev\Taxonomy\Model\Taxonomy
     */
    public function taxonomy()
    {
        if ($this->_taxonomy === null) {
            $this->_taxonomy = $this->taxonomyFactory->create($this->base['taxonomy']);
        }

        return $this->_taxonomy;
    }

    /**
     * Get a string representation of the terms.
     *
     * Term name, or list of comma-separated term names.
     *
     * @return string
     */
    public function __toString()
    {
        $terms = $this->getValue();

        if (is_array($terms)) {
            return array_reduce($terms, function ($string, $t) {
                return $string . (strlen($string) ? ', ' : '') . $t->getName();
            }, '');
        } else {
            return $terms->getName();
        }
    }
}
