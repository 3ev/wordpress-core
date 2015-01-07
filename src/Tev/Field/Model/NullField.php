<?php
namespace Tev\Field\Model;

/**
 * Null field entity.
 *
 * This field will be returned if field data could not be loaded.
 */
class NullField extends AbstractField
{
    /**
     * Constructor.
     *
     * Initializes required field data to null.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(array(
            'key'  => null,
            'name' => null
        ));
    }

    /**
     * Get the value of this field.
     *
     * Value will be null.
     *
     * @return null
     */
    public function getValue()
    {
        return null;
    }

    /**
     * Conver this field to string.
     *
     * @return string The empty string
     */
    public function __toString()
    {
        return '';
    }
}
