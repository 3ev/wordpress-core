<?php
namespace Tev\Field;

use Closure;
use Exception;
use Tev\Post\Model\AbstractPost,
    Tev\Application\Application,
    Tev\Field\Model\NullField;

/**
 * Custom field factory.
 *
 * Used for creating custom field entities.
 */
class Factory
{
    /**
     * Application instance.
     *
     * @var \Tev\Application\Application
     */
    private $app;

    /**
     * Registered field type factory functions.
     *
     * @var \Closure[]
     */
    private $registry;

    /**
     * Constructor.
     *
     * @param  \Tev\Application\Application $app Application instance
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app      = $app;
        $this->registry = array();
    }

    /**
     * Register a new factory function with the factory.
     *
     * Can either be field class name or factory callback function. If class
     * name, the field data array will be passed to the constructor. If
     * callback, the field data array will be passed as the first parameter
     * and the application instance will be passed as the second.
     *
     * For example:
     *
     * // Class name
     *
     * $factory->register('text', 'Tev\Field\Model\BasicField');
     *
     * // Callback
     *
     * $factory->register('text', function ($data, $app) {
     *     return new \Tev\Field\Model\BasicField($data);
     * });
     *
     * @param  string             $type     Field type string
     * @param  string|\Closure    $factory  Class name or factory function
     * @return \Tev\Field\Factory           This, for chaining
     */
    public function register($type, $factory)
    {
        $this->registry[$type] = $factory;

        return $this;
    }

    /**
     * Check if the given type is registered.
     *
     * @param  string  $type Field type string
     * @return boolean       True if registered false if not
     */
    public function registered($type)
    {
        return isset($this->registry[$type]);
    }

    /**
     * Create a new custom field object.
     *
     * If the field is not registered, a `\Tev\Field\Model\NullField` will be
     * returned.
     *
     * @param  string                         $field Field name or ID
     * @param  \Tev\Post\Model\AbstractPost   $post  Post object field is for
     * @return \Tev\Field\Model\AbstractField        Field object
     *
     * @throws \Exception If field type is not registered
     */
    public function create($field, AbstractPost $post)
    {
        $data = get_field_object($field, $post->getId());

        return $this->createFromField($data);
    }

    /**
     * Create a new custom field object from an existing set of field data.
     *
     * If the field is not registered, a `\Tev\Field\Model\NullField` will be
     * returned.
     *
     * @param  array                          $field Field data array
     * @param  mixed                          $value If supplied, will be set as the fields value
     * @return \Tev\Field\Model\AbstractField        Field object
     *
     * @throws \Exception If field type is not registered
     */
    public function createFromField($field, $value = null)
    {
        if (!is_array($field)) {
            return new NullField;
        }

        if ($value !== null) {
            $field['value'] = $value;
        }

        return $this->resolve($field['type'], $field);
    }

    /**
     * Creata new custom field object that's not in the context of a post
     * and doesn't have a loaded value.
     *
     * @param  string                         $field Field name or ID
     * @return \Tev\Field\Model\AbstractField        Field object
     *
     * @throws \Exception If field type is not registered
     */
    public function createEmpty($field)
    {
        return $this->createFromField(get_field_object($field, null, false, false));
    }

    /**
     * Resolve a field object using its type, from the registered factory
     * functions.
     *
     * @param  string                         $type Field type
     * @param  array                          $data Field data
     * @return \Tev\Field\Model\AbstractField       Field object
     *
     * @throws \Exception If field type is not registered
     */
    protected function resolve($type, array $data)
    {
        if ($this->registered($type)) {
            $f = $this->registry[$type];
            if ($f instanceof Closure) {
                return $f($data, $this->app);
            } else {
                return new $f($data);
            }
        } else {
            throw new Exception("Field type $type not registered");
        }
    }
}
