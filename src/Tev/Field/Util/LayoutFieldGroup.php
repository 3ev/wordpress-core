<?php
namespace Tev\Field\Util;

/**
 * Field group extension, that has a layout name.
 */
class LayoutFieldGroup extends FieldGroup
{
    /**
     * Layout name.
     *
     * @var string
     */
    private $layoutName;

    /**
     * Set the layout name.
     *
     * @param  string                           $layoutName Layout name
     * @return \Tev\Field\Util\LayoutFieldGroup             This, for chaining
     */
    public function setLayout($layoutName)
    {
        $this->layoutName = $layoutName;

        return $this;
    }

    /**
     * Get the layout name, or check if it's equal to the supplied param.
     *
     * @param  null|string    $compare Optional. Layout name comparator
     * @return string|boolean          If no param, layout name. If param, boolean
     *                                 result of comparison
     */
    public function layout($compare = null)
    {
        if ($compare === null) {
            return $this->layoutName;
        }

        return $compare === $this->layoutName;
    }
}
