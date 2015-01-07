<?php
namespace Tev\Field\Model;

/**
 * Basic field entity.
 *
 * A basic field has a simple primitive value.
 */
class BasicField extends AbstractField
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
