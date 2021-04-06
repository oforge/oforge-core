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
use Oforge\Engine\Modules\Core\Exceptions\DependencyNotResolvedException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
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
                        $templatePath = Statics::GLOBAL_SEPARATOR . 'Plugins' . Statics::GLOBAL_SEPARATOR . $key;
                    }
                    if ($foundController && ($index + 1 !== $size)) {
                        $templatePath .= Statics::GLOBAL_SEPARATOR . $key;
                    }

                    if ($key === 'Controller') {
                        $foundController = true;
                    }
                    $index++;
                }
                $templatePath = ltrim($templatePath, Statics::GLOBAL_SEPARATOR);
                $templatePath .= Statics::GLOBAL_SEPARATOR . $controllerName . Statics::GLOBAL_SEPARATOR . ucfirst($fileName) . '.twig';

                $data = ArrayHelper::dotSet($data, 'meta.template.path', $templatePath);
            }

            if (!isset($data['meta']['template']['layout'])) {
                $data['meta']['template']['layout'] = 'Default';
            }
            if (isset($data['crud'])) {
                $data['crud']['templatePath'] = ltrim(str_replace('\\', '/', dirname($templatePath)), '/');
            }
            if ($this->hasTemplate($templatePath)) {
                return $this->renderTemplate($request, $response, $templatePath, $data);
            }
            // TODO: Remove this because we don't have this here.
            if (isset($fileName) && isset($data['crud'])) {
                $fallbackTemplatePath = '/Backend/CRUD/' . ucfirst($fileName) . '.twig';
                if ($this->hasTemplate($fallbackTemplatePath)) {
                    $data['meta']['template']['path'] = $fallbackTemplatePath;

                    return $this->renderTemplate($request, $response, $fallbackTemplatePath, $data);
                }
            }
        }

        if (isset($data["omitRendering"]) && $data["omitRendering"] == true) {
            return $response;
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
     * @throws DependencyNotResolvedException
     */
    public function View() {
        //TODO REFACTORING: Generic Twig Instance by TemplateRenderService method
        if (!$this->view) {
            /** @var TemplateManagementService $templateManagementService */
            $templateManagementService = Oforge()->Services()->get('template.management');
            $activeTemplate            = $templateManagementService->getActiveTemplate();
            $templatePath              = Statics::TEMPLATE_DIR . Statics::GLOBAL_SEPARATOR . $activeTemplate->getName();
            $debug                     = Oforge()->Settings()->isDevelopmentMode();
            $defaultThemePath          = ROOT_PATH . Statics::GLOBAL_SEPARATOR . Statics::TEMPLATE_DIR . Statics::GLOBAL_SEPARATOR . Statics::DEFAULT_THEME;

            /** @var Plugin[] $plugins */
            $plugins = Oforge()->Services()->get('plugin.access')->getActive();
            $paths   = [
                'parent'                  => [],
                self::TWIG_MAIN_NAMESPACE => [],
            ];

            if ($activeTemplate->getName() !== Statics::DEFAULT_THEME) {
                $paths[self::TWIG_MAIN_NAMESPACE] = [ROOT_PATH . Statics::GLOBAL_SEPARATOR . $templatePath];
            }

            foreach ($plugins as $plugin) {
                $viewsDir = ROOT_PATH . Statics::GLOBAL_SEPARATOR . Statics::PLUGIN_DIR . Statics::GLOBAL_SEPARATOR . $plugin['name'] . Statics::GLOBAL_SEPARATOR
                            . Statics::VIEW_DIR;

                if (file_exists($viewsDir)) {
                    $paths['parent'][]                  = $viewsDir;
                    $paths[self::TWIG_MAIN_NAMESPACE][] = $viewsDir;

                    if (!isset($paths[$plugin['name']])) {
                        $paths[$plugin['name']] = [];
                    }
                    $paths[$plugin['name']][] = $viewsDir;
                }
            }

            $paths['parent'][]                  = $defaultThemePath;
            $paths[self::TWIG_MAIN_NAMESPACE][] = $defaultThemePath;

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
     * @throws TemplateNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
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
     * @throws TemplateNotFoundException
     * @throws Twig_Error_Loader
     */
    private function hasTemplate($template) : bool {
        return $this->View()->hasTemplate($template);
    }

}
