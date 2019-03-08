<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Core\Twig;

use Psr\Http\Message\ResponseInterface;

class CustomTwig {
    /**
     * Twig loader
     *
     * @var \Twig_LoaderInterface
     */
    protected $loader;

    /**
     * Twig environment
     *
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * Default view variables
     *
     * @var array
     */
    protected $defaultVariables = [];

    /**
     * Create new Twig view
     *
     * @param string|array $path Path(s) to templates directory
     * @param array $settings Twig environment settings
     *
     * @throws \Twig_Error_Loader
     */
    public function __construct($path, $settings = []) {
        $this->loader      = $this->createLoader(is_string($path) ? [$path] : $path);
        $this->environment = new TwigEnvironment($this->loader, $settings);
    }

    /**
     * Create a loader with the given path
     *
     * @param array $paths
     *
     * @return TwigFileSystemLoader
     * @throws \Twig_Error_Loader
     */
    private function createLoader(array $paths) {
        $loader = new TwigFileSystemLoader();

        foreach ($paths as $namespace => $path) {
            if (is_string($namespace)) {
                $loader->setPaths($path, $namespace);
            } else {
                $loader->addPath($path);
            }
        }

        return $loader;
    }

    /**
     * Proxy method to add an extension to the Twig environment
     *
     * @param \Twig_ExtensionInterface $extension A single extension instance or an array of instances
     */
    public function addExtension(\Twig_ExtensionInterface $extension) {
        $this->environment->addExtension($extension);
    }

    /**
     * Fetch rendered template
     *
     * @param  string $template Template pathname relative to templates directory
     * @param  array $data Associative array of template variables
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function fetch($template, $data = []) {
        $data = array_merge($this->defaultVariables, $data);

        return $this->environment->render($template, $data);
    }

    /**
     * Fetch rendered block
     *
     * @param  string $template Template pathname relative to templates directory
     * @param  string $block Name of the block within the template
     * @param  array $data Associative array of template variables
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function fetchBlock($template, $block, $data = []) {
        $data = array_merge($this->defaultVariables, $data);

        return $this->environment->loadTemplate($template)->renderBlock($block, $data);
    }

    /**
     * Fetch rendered string
     *
     * @param  string $string String
     * @param  array $data Associative array of template variables
     *
     * @return string
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     */
    public function fetchFromString($string = "", $data = []) {
        $data = array_merge($this->defaultVariables, $data);

        return $this->environment->createTemplate($string)->render($data);
    }

    /**
     * Output rendered template
     *
     * @param ResponseInterface $response
     * @param  string $template Template pathname relative to templates directory
     * @param  array $data Associative array of template variables
     *
     * @return ResponseInterface
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(ResponseInterface $response, $template, $data = []) {
        $response->getBody()->write($this->fetch($template, $data));

        return $response;
    }

    /********************************************************************************
     * Accessors
     *******************************************************************************/

    /**
     * Return Twig loader
     *
     * @return TwigFileSystemLoader
     */
    public function getLoader() {
        return $this->loader;
    }

    /**
     * Return Twig environment
     *
     * @return \Twig_Environment
     */
    public function getEnvironment() {
        return $this->environment;
    }

    /**
     * @param $template
     *
     * @return bool
     * @throws \Twig_Error_Loader
     */
    public function hasTemplate($template) {
        return $this->getLoader()->findTemplate($template, false) != false;
    }
}