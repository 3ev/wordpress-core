<?php
namespace Tev\Field\Model;

/**
 * Gallery field entity.
 *
 */
class GalleryField extends AbstractField
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
