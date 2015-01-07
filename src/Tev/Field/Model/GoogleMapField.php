<?php
namespace Tev\Field\Model;

/**
 * Google map field.
 *
 * Provides access to the lat, lng and address fields. By default, value
 * is lat,lng.
 */
class GoogleMapField extends AbstractField
{
    /**
     * Get the lat and lng, separated by a comma.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->lat() . ',' . $this->lng();
    }

    /**
     * Get the latitude value.
     *
     * @return string
     */
    public function lat()
    {
        if (is_array($this->base['value']) && isset($this->base['value']['lat'])) {
            return $this->base['value']['lat'];
        }

        return '';
    }

    /**
     * Get the longitude value.
     *
     * @return string
     */
    public function lng()
    {
        if (is_array($this->base['value']) && isset($this->base['value']['lng'])) {
            return $this->base['value']['lng'];
        }

        return '';
    }

    /**
     * Get the address field.
     *
     * @return string
     */
    public function address()
    {
        if (is_array($this->base['value']) && isset($this->base['value']['address'])) {
            return $this->base['value']['address'];
        }

        return '';
    }
}
