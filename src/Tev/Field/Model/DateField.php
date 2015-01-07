<?php
namespace Tev\Field\Model;

use Carbon\Carbon;

/**
 * Date field.
 *
 * Provides access to the data in string form and as a Carbon object.
 */
class DateField extends AbstractField
{
    /**
     * Carbon object for date.
     *
     * @var \Carbon\Carbon
     */
    private $date;

    /**
     * Constructor.
     *
     * @param  array $data Underlying field data
     * @return void
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        if ($this->base['value']) {
            $this->date = Carbon::createFromFormat($this->base['return_format'], $this->base['value']);
        } else {
            $this->data = null;
        }

        $this->format = $this->base['display_format'];
    }

    /**
     * Get this date as a string.
     *
     * Empty string if no date is set.
     *
     * @return string
     */
    public function getValue()
    {
        if ($this->date) {
            return $this->date->format($this->format);
        } else {
            return '';
        }
    }

    /**
     * Get this date as a Carbon object.
     *
     * Returns null if no date is set.
     *
     * @return \Carbon\Carbon|null
     */
    public function date()
    {
        return $this->date;
    }

    /**
     * Format the date, or return its format.
     *
     * @param  string|null                       $newFormat If string, will set format. If omitted,
     *                                                      will return format
     * @return string|\Tev\Field\Model\DateField            Format or this, for chaining
     */
    public function format($newFormat = null)
    {
        if ($newFormat === null) {
            return $this->format;
        }

        $this->format = $newFormat;

        return $this;
    }
}
