<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Core\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\TemplateEngine\Core\Twig\CustomTwig;
use Oforge\Engine\Modules\TemplateEngine\Core\Twig\TwigOforgeDebugExtension;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;

/**
 * Class TemplateRenderService
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Core\Services
 */
class TemplateRenderService {
    const TWIG_MAIN_NAMESPACE = Twig_Loader_Filesystem::MAIN_NAMESPACE;
    /**
     * @var $view CustomTwig
     */
    private $view;

    /**
     * This render function can be called either from a module controller or a template controller.
     * It checks, whether a template path based on the controllers namespace and the function name exists
     * [e.g.: Oforge/Engine/Modules/Test/Controller/Frontend/HomeController:indexAction => /Themes/$currentTheme/Test/Frontend/Home/Index.twig].
     * If the template is found, it gets rendered by the template engine, the fallback is a json response
     *
     * @param Request $request
     * @param Response $response
     * @param $data
     *
     * @return ResponseInterface|Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws TemplateNotFoundException
     */
    public function render(Request $request, Response $response, $data) {
        if (isset($data['json']) && is_array($data['json'])) {
            return $this->renderJson($request, $response, $data['json']);
        } else {
            if (isset($data['meta']['template']['path'])) {
                $templatePath = $data['meta']['template']['path'];
            } else {
                $routeController = Oforge()->View()->get('meta.route');
                $namespace       = explode("\\", $routeController['controllerClass']);
                $controllerName  = explode('Controller', $namespace[sizeof($namespace) - 1])[0];
                $fileName        = explode('Action', $routeController['controllerMethod'])[0];

                $foundController = false;
                $index           = 0;
                $size            = count($namespace);
                $templatePath    = null;

                // register all modules
                foreach ($namespace as $key) {
                    if ($index === 0 && $key !== 'Oforge') {
                        $templatePath = DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . $key;
                    }
                    if ($foundController && ($index + 1 !== $size)) {
                        $templatePath .= DIRECTORY_SEPARATOR . $key;
                    }

                    if ($key === 'Controller') {
                        $foundController = true;
                    }
                    $index++;
                }

                $templatePath .= DIRECTORY_SEPARATOR . $controllerName . DIRECTORY_SEPARATOR . ucwords($fileName) . '.twig';

                $data = ArrayHelper::dotSet($data, 'meta.template.path', $templatePath);
            }

            if (!isset($data['meta']['template']['layout'])) {
                $data['meta']['template']['layout'] = 'Default';
            }

            if ($this->hasTemplate($templatePath)) {
                return $this->renderTemplate($request, $response, $templatePath, $data);
            }
        }

        return $this->renderJson($request, $response, $data);
    }

    /**
     * If no Twig Engine is loaded, create one
     *
     * @return CustomTwig
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws TemplateNotFoundException
     */
    public function View() {
        if (!$this->view) {
            /** @var TemplateManagementService $templateManagementService */
            $templateManagementService = Oforge()->Services()->get('template.management');
            $activeTemplate            = $templateManagementService->getActiveTemplate();
            $templatePath              = DIRECTORY_SEPARATOR . Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $activeTemplate->getName();
            $debug                     = Oforge()->Settings()->isDevelopmentMode();
            $defaultThemePath          = ROOT_PATH . DIRECTORY_SEPARATOR . Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . Statics::DEFAULT_THEME;

            /** @var Plugin[] $plugins */
            $plugins = Oforge()->Services()->get('plugin.access')->getActive();
            $paths   = [
                'parent'                  => [],
                self::TWIG_MAIN_NAMESPACE => [],
            ];

            if ($activeTemplate->getName() !== Statics::DEFAULT_THEME) {
                $paths[self::TWIG_MAIN_NAMESPACE] = [ROOT_PATH . DIRECTORY_SEPARATOR . $templatePath];
            }

            foreach ($plugins as $plugin) {
                $viewsDir = ROOT_PATH . DIRECTORY_SEPARATOR . Statics::PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin->getName() . DIRECTORY_SEPARATOR
                            . Statics::VIEW_DIR;

                if (file_exists($viewsDir)) {
                    $paths['parent'][]                  = $viewsDir;
                    $paths[self::TWIG_MAIN_NAMESPACE][] = $viewsDir;

                    if (!isset($paths[$plugin->getName()])) {
                        $paths[$plugin->getName()] = [];
                    }
                    $paths[$plugin->getName()][] = $viewsDir;
                }
            }

            $paths['parent'][]                = $defaultThemePath;
            $paths[self::TWIG_MAIN_NAMESPACE] = $defaultThemePath;

            $this->view = new CustomTwig($paths, [
                'cache'       => ROOT_PATH . Statics::THEME_CACHE_DIR,
                'auto_reload' => $debug,
                'debug'       => $debug,
            ]);

            if ($debug) {
                $this->view->getEnvironment()->enableDebug();
            }

            $this->view->getEnvironment()->addExtension(new Twig_Extension_Debug());
            $this->view->getEnvironment()->addExtension(new TwigOforgeDebugExtension());
        }

        return $this->view;
    }

    /**
     * Send a JSON response
     *
     * @param Request $request
     * @param Response $response
     * @param array $data
     *
     * @return Response
     */
    private function renderJson(Request $request, Response $response, array $data) {
        return $response->withHeader('Content-Type', 'application/json')->withJson($data);
    }

    /**
     * Send the response through the Twig Engine
     *
     * @param Request $request
     * @param Response $response
     * @param string $template
     * @param array $data
     *
     * @return ResponseInterface
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws TemplateNotFoundException
     */
    private function renderTemplate(Request $request, Response $response, string $template, array $data) {
        return $this->View()->render($response, $template, $data);
    }

    /**
     * Check if the template exists
     *
     * @param $template
     *
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws TemplateNotFoundException
     */
    private function hasTemplate($template) : bool {
        return $this->View()->hasTemplate($template);
    }

}
