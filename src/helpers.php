<?php


if (!function_exists('tev_app')) {

    /**
     * Fetch the global Tev application instance.
     *
     * @return \Tev\Application\Application
     */
    function tev_app() {
        return Tev\Application\Application::getInstance();
    }
}


if (!function_exists('tev_fetch')) {

    /**
     * Fetch a service from the container.
     *
     * @param  string $name Service name
     * @return mixed        Bound service
     */
    function tev_fetch($service) {
        return tev_app()->fetch($service);
    }
}


if (!function_exists('tev_partial')) {

    /**
     * Render a view partial.
     *
     * @param  string $file   Partial file name
     * @param  array  $params Optional variables to make available to partial
     * @return string         Rendered partial
     */
    function tev_partial($file, array $params = array()) {
        return tev_fetch('template_extras')->partial($file, $params);
    }
}


if (!function_exists('tev_post_factory_register')) {

    /**
     * @see \Tev\Post\Factory::register()
     */
    function tev_post_factory_register($postType, $className) {
        Tev\Application\Application::getInstance()
            ->fetch('post_factory')
            ->register($postType, $className);
    }
}


if (!function_exists('tev_post_factory')) {

    /**
     * @see \Tev\Post\Factory::create()
     */
    function tev_post_factory($base = null, $className = null) {
        return Tev\Application\Application::getInstance()
            ->fetch('post_factory')
            ->create($base, $className);
    }
}
