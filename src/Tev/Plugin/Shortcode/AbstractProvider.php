<?php
namespace Tev\Plugin\Shortcode;

use Tev\Application\Application,
    Tev\View\Renderer;

/**
 * Base class for shortcode providers.
 *
 * Extending classes provide a method which generates their shortcode content.
 */
abstract class AbstractProvider
{
    /**
     * Application.
     *
     * @var \Tev\Application\Application
     */
    protected $app;

    /**
     * View renderer.
     *
     * @var \Tev\View\Renderer
     */
    protected $renderer;

    /**
     * Constructor.
     *
     * Inject dependencies.
     *
     * @param  \Tev\Application\Application $app      Application
     * @param  \Tev\View\Renderer           $renderer View renderer
     * @return void
     */
    public function __construct(Application $app, Renderer $renderer)
    {
        $this->app      = $app;
        $this->renderer = $renderer;
    }

    /**
     * Generate a shortcode.
     *
     * @param  array  $attrs   Shortcode attributes
     * @param  string $content Shortcode content
     * @return string          Shortcode content
     */
    abstract public function shortcode($attrs, $content);
}
