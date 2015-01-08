<?php
namespace Tev\Field\Model;

use ArrayAccess, Iterator, Countable;
use Tev\Field\Factory as FieldFactory,
    Tev\Field\Util\FieldGroup;

/**
 * Repeater field.
 *
 * An iterable field which provides access to underlying grouped sets of
 * fields.
 */
class RepeaterField extends AbstractField implements ArrayAccess, Iterator, Countable
{
    /**
     * Field factory.
     *
     * @var \Tev\Field\Factory
     */
    protected $fieldFactory;

    /**
     * Current iteration position.
     *
     * @var int
     */
    protected $position;

    /**
     * Constructor.
     *
     * @param  array              $base         Repeater field data
     * @param  \Tev\Field\Factory $fieldFactory Field factory
     * @return void
     */
    public function __construct(array $base, FieldFactory $fieldFactory)
    {
        parent::__construct($base);

        $this->fieldFactory = $fieldFactory;
        $this->position     = 0;
    }

    /**
     * Shows the number of items in the repeater.
     *
     * @return int
     */
    public function getValue()
    {
        return $this->count();
    }

    /**
     * Get the number of items in the repeater.
     *
     * @return int
     */
    public function count()
    {
        if (is_array($this->base['value'])) {
            return count($this->base['value']);
        }

        return 0;
    }

    /**
     * Get the current repeater item.
     *
     * @return \Tev\Field\Util\FieldGroup
     */
    public function current()
    {
        return new FieldGroup(
            $this->base['sub_fields'],
            $this->base['value'][$this->position],
            $this,
            $this->fieldFactory
        );
    }

    /**
     * Get the current iteration key.
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Advance to the next position.
     *
     * @return void
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Rewind to the beginning.
     *
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Check if current position is valid.
     *
     * @return boolean
     */
    public function valid()
    {
        return isset($this->base['value'][$this->position]) && is_array($this->base['value'][$this->position]);
    }

    /**
     * Required for `\ArrayAccess`.
     *
     * @param  mixed   $offset Array offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->base['value'][$offset]) && is_array($this->base['value'][$offset]);
    }

    /**
     * Required for `\ArrayAccess`.
     *
     * @param  mixed $offset Array offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (isset($this->base['value'][$offset]) && is_array($this->base['value'][$offset])) {
            return new FieldGroup(
                $this->base['sub_fields'],
                $this->base['value'][$offset],
                $this,
                $this->fieldFactory
            );
        }

        return null;
    }

    /**
     * Required for `\ArrayAccess`.
     *
     * @param  mixed $offset Array offset
     * @param  mixed $value  Value to set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->base['value'][] = $value;
        } else {
            $this->base['value'][$offset] = $value;
        }
    }

    /**
     * Required for `\ArrayAccess`.
     *
     * @param  mixed $offset Array offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->base['value'][$offset]);
    }
}
