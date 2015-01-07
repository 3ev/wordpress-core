<?php
namespace Tev\Plugin\Action;

use Tev\Application\Application,
    Tev\View\Renderer;

/**
 * Base class for action providers.
 *
 * Extending classes provide a method which will be called as an action
 * callback. This method should be called 'action()'.
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
        $this->app = $app;
        $this->renderer = $renderer;
    }

    /**
     * Get the action priority of this provider.
     *
     * @return int Default 10
     */
    public function priority()
    {
        return 10;
    }

    /**
     * Get the number of arguments expected by the action method of this
     * provider.
     *
     * @return int Default 1
     */
    public function numArgs()
    {
        return 1;
    }
}
