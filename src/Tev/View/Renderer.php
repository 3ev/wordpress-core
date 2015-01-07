<?php
namespace Tev\View;

use Tev\View\Exception\NotFoundException;

/**
 * Simple buffer-based view renderer.
 *
 * Will search for template files in the configured template dir first,
 * and then fallback to the current theme directory.
 */
class Renderer
{
    /**
     * Template directory.
     *
     * @var string
     */
    private $templateDir;

    /**
     * View data container.
     *
     * @var array
     */
    private $viewData;

    /**
     * Constructor.
     *
     * @param  string $templateDir Template directory
     * @return void
     */
    public function __construct($templateDir)
    {
        $this->templateDir = $templateDir;
        $this->viewData    = array();
    }

    /**
     * Renders a template file.
     *
     * Assigns all $var keys to $this->$key for us in template.
     *
     * @param  string $filename Full-filename
     * @param  array  $vars     View variables
     * @return string           Rendered view
     *
     * @throws \Tev\View\Exception\NotFoundException If view file not found
     */
    public function render($filename, $vars = array())
    {
        $localTemplate = $this->templateDir . '/' . $filename;
        $themeTemplate = locate_template($filename);

        if (!file_exists($localTemplate) && !file_exists($themeTemplate)) {
            throw new NotFoundException("View at $localTemplate or $themeTemplate not found");
        }

        $this->viewData = $vars;

        $template = file_exists($localTemplate) ? $localTemplate : $themeTemplate;

        ob_start();
        include($template);
        $view = ob_get_contents();
        ob_end_clean();

        return $view;
    }

    /**
     * Magic getter, for getting view variables.
     *
     * @param  string $name View variable name
     * @return mixed        View variable, or null if not set
     */
    public function __get($name)
    {
        return isset($this->viewData[$name]) ? $this->viewData[$name] : null;
    }
}
