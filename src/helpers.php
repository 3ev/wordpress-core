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
     * Register a new post type to class name mapping.
     *
     * @param  string $postType  Post type identifier
     * @param  string $className Class name to instantiate for $postType. Class
     *                           must inherit from `\Tev\Post\Model\AbstractPost`
     * @return void
     *
     * @throws \Exception If class name does not inherit from `\Tev\Post\Model\AbstractPost`
     */
    function tev_post_factory_register($postType, $className) {
        Tev\Application\Application::getInstance()
            ->fetch('post_factory')
            ->register($postType, $className);
    }
}


if (!function_exists('tev_post_factory')) {

    /**
     * Instantiate a post entity from a given post object.
     *
     * @param  \WP_Post                     $base      Optional. Base post object. If not
     *                                                 supplied will attempt to get the current
     *                                                 post from The Loop
     * @param  string                       $className Optional. Class name to instantiate. Will
     *                                                 use registered default if not supplied
     * @return \Tev\Post\Model\AbstractPost            Post entity
     *
     * @throws \Exception If $base is not given and not in the The Loop
     */
    function tev_post_factory($base = null, $className = null) {
        return Tev\Application\Application::getInstance()
            ->fetch('post_factory')
            ->create($base, $className);
    }
}
