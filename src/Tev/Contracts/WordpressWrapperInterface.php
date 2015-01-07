<?php
namespace Tev\Contracts;

/**
 * This interface should be implemented by library entities that wrap source
 * Wordpress objects.
 *
 * It provides access to the underlying Wordpress object.
 */
interface WordpressWrapperInterface
{
    /**
     * Get the underying Wordpress object wrapped by this entity.
     *
     * @return mixed
     */
    public function getBaseObject();
}
