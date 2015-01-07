<?php
namespace Tev\Field\Model;

/**
 * Select field.
 *
 * A select field can have one or more objects selected.
 */
class SelectField extends AbstractField
{
    /**
     * Get the selected options.
     *
     * @return mixed Single selected option, or array of selected options
     */
    public function getValue()
    {
        $options = $this->options();

        if ($this->multiSelected()) {
            $values = array();

            foreach ($this->selected() as $val) {
                $values[] = $options[$val];
            }

            return $values;
        } else {
            return $options[$this->selected()];
        }
    }

    /**
     * Get the selected option values.
     *
     * @return mixed Single selected option value, or array of selected options values
     */
    public function selected()
    {
        return $this->base['value'];
    }

    /**
     * Get all available options from this select.
     *
     * @return array Key-value pairs (values to labels)
     */
    public function options()
    {
        return $this->base['choices'];
    }

    /**
     * Check if the given value is selected.
     *
     * @param  mixed   $val Value
     * @return boolean      True if selected, false if not
     */
    public function isSelected($val)
    {
        if ($this->isMulti()) {
            return in_array($val, $this->getValue());
        } else {
            return $val === $this->getValue();
        }
    }

    /**
     * Check if this is a multi-select or not.
     *
     * @return boolean True if multi-select, false if not
     */
    public function isMulti()
    {
        return (boolean) $this->base['multiple'];
    }

    /**
     * Get a string representation of this selected options.
     *
     * If this is a multi-select, values will be comma-space separated.
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->multiSelected()) {
            return implode(', ', $this->getValue());
        } else {
            return $this->getValue();
        }
    }

    /**
     * Check if the selected options are in mult-format.
     *
     * @return boolean
     */
    private function multiSelected()
    {
        return is_array($this->selected());
    }
}
