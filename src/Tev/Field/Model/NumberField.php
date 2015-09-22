<?php
namespace Tev\Field\Model;

/**
 * Number field
 */
class NumberField extends AbstractField
{
    /**
     * Get the value of this field.
     *
     * Value will be a primitive type.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->base['value'];
    }
}
