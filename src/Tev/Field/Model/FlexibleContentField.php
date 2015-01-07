<?php
namespace Tev\Field\Model;

use Tev\Field\Util\LayoutFieldGroup;

/**
 * Flexible content field.
 *
 * An iterable field which provides access to underlying grouped sets of
 * fields.
 */
class FlexibleContentField extends RepeaterField
{
    /**
     * Get the current flexible content item.
     *
     * @return \Tev\Field\Util\FieldGroup
     */
    public function current()
    {
        $val    = $this->base['value'][$this->position];
        $layout = $val['acf_fc_layout'];

        $group = new LayoutFieldGroup(
            $this->getLayoutFields($layout),
            $val,
            $this,
            $this->fieldFactory
        );

        return $group->setLayout($layout);
    }

    /**
     * Get sub fields for layout.
     *
     * @param  string $layout Layout name
     * @return array          Sub fields
     */
    private function getLayoutFields($layout)
    {
        foreach ($this->base['layouts'] as $l) {
            if ($l['name'] === $layout) {
                return $l['sub_fields'];
            }
        }
    }
}
