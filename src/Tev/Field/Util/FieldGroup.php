<?php
namespace Tev\Field\Util;

use Exception;
use Tev\Field\Model\AbstractField,
    Tev\Field\Factory as FieldFactory;

/**
 * Utility object that provides access to a group of fields.
 *
 * Used internally by RepeaterField and similar to access each individual
 * set of fields.
 */
class FieldGroup
{
    /**
     * Set of field data.
     *
     * @var array
     */
    private $fields;

    /**
     * Set of field values
     *
     * @var string
     */
    private $values;

    /**
     * Parent field.
     *
     * @var \Tev\Field\Model\AbstractField
     */
    private $parent;

    /**
     * Field factory.
     *
     * @var \Tev\Field\Factory
     */
    private $fieldFactory;

    /**
     * Cached field data (indexed by field key or name).
     *
     * @var array
     */
    private $_fieldData;

    /**
     * Constructor.
     *
     * @param  array                          $fields       Set of field data
     * @param  array                          $values       Set of field values
     * @param  \Tev\Field\Model\AbstractField $parent       Parent field
     * @param  \Tev\Field\Factory             $fieldFactory Field factory
     * @return void
     */
    public function __construct(array $fields,
                                array $values,
                                AbstractField $parent,
                                FieldFactory $fieldFactory)
    {
        $this->fields       = $fields;
        $this->values       = $values;
        $this->parent       = $parent;
        $this->fieldFactory = $fieldFactory;
        $this->_fieldData   = array();
    }

    /**
     * Get a field from the group.
     *
     * @param  string                         $field Field name or key
     * @return \Tev\Field\Model\AbstractField        Field object
     *
     * @throws \Exception If field does not exist
     */
    public function field($field)
    {
        return $this->fieldFactory->createFromField(
            $this->getFieldData($field),
            $this->getFieldValue($field)
        );
    }

    /**
     * Get the parent of this field.
     *
     * @return \Tev\Field\Model\AbstractField
     */
    public function parent()
    {
        return $this->parent;
    }

    /**
     * Get field data by field name or key.
     *
     * @param  string $field Field name or key
     * @return array         Field data
     *
     * @throws \Exception If field does not exist
     */
    private function getFieldData($field)
    {
        if (!isset($this->_fieldData[$field])) {
            foreach ($this->fields as $f) {
                if (($f['key'] === $field) || ($f['name'] === $field)) {
                    $this->_fieldData[$field] = $f;
                    break;
                }
            }
        }

        if (isset($this->_fieldData[$field])) {
            return $this->_fieldData[$field];
        } else {
            throw new Exception("Field $field does not exist");
        }
    }

    /**
     * Get field value by name or key.
     *
     * @param  string $field Field name or key
     * @return mixed         Value or null if not set
     */
    private function getFieldValue($field)
    {
        return isset($this->values[$field]) ? $this->values[$field] : null;
    }
}
