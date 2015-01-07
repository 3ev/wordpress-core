<?php
namespace Tev\Field\Model;

use Tev\Contracts\WordpressWrapperInterface;

/**
 * Base custom field entity.
 */
abstract class AbstractField implements WordpressWrapperInterface
{
    /**
     * Underlying custom field array.
     *
     * @var array
     */
    protected $base;

    /**
     * Constructor.
     *
     * @param  array $base Underlying customer field array
     * @return void
     */
    public function __construct(array $base)
    {
        $this->base = $base;
    }

    /**
     * Get the key for this field.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->base['key'];
    }

    /**
     * Get the name of this field.
     *
     * @return string
     */
    public function getName()
    {
        return $this->base['name'];
    }

    /**
     * Get the field value.
     *
     * Alias for `getValue()`.
     *
     * @return mixed Field value
     */
    public function val()
    {
        return $this->getValue();
    }

    /**
     * Get the underlying field array
     *
     * @return array
     */
    public function getBaseObject()
    {
        return $this->base;
    }

    /**
     * When attempting to echo this field print its value as a string.
     *
     * @return string Field value
     */
    public function __toString()
    {
        return $this->getValue();
    }

    /**
     * Get the value of this field.
     *
     * @return mixed Field value
     */
    abstract public function getValue();
}
