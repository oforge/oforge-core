<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Services;

use Oforge\Engine\Modules\Core\Exceptions\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\TemplateEngine\Twig\CustomTwig;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use Oforge\Engine\Modules\TemplateEngine\Twig\TwigOforgeDebugExtension;

class TemplateRenderService {
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws TemplateNotFoundException
     */
    public function render(Request $request, Response $response, $data) {
        $namespace = explode("\\", Oforge()->View()->get('meta')['controller_method']);

        $templatePath   = null;
        $moduleName     = null;
        $region         = null;
        $controllerName = null;
        $fileName       = null;
        $folderDepth    = null;

        if ($namespace[0] === "Oforge") {
            $moduleName  = null; //$namespace[3];
            $region      = $namespace[5];
            $folderDepth = sizeof($namespace) - 7;
        } else {
            $moduleName  = $namespace[0];
            $region      = $namespace[2];
            $folderDepth = sizeof($namespace) - 4;
        }

        while ($folderDepth > 0) {
            $controllerName .= $namespace[sizeof($namespace) - 1 - $folderDepth] . DIRECTORY_SEPARATOR;
            $folderDepth--;
        }

        $controllerName .= explode("Controller", explode(":", $namespace[sizeof($namespace) - 1])[0])[0];
        $fileName       = explode("Action", explode(":", $namespace[sizeof($namespace) - 1])[1])[0];

        $templatePath .= DIRECTORY_SEPARATOR . $region . DIRECTORY_SEPARATOR . (isset($moduleName) ? ($moduleName . DIRECTORY_SEPARATOR) : "") . $controllerName
                         . DIRECTORY_SEPARATOR . ucwords($fileName) . ".twig";

        if (isset($data["template"]["path"])) {
            $templatePath = $data["template.path"];
        } else {
            $data["template.path"] = $templatePath;
        }
        
        if (!isset($data["template_layout"])) {
            $data["template_layout"] = "Default";
        }

        if ($this->hasTemplate($templatePath)) {
            return $this->renderTemplate($request, $response, $templatePath, $data);
        }

        return $this->renderJson($request, $response, $data);
    }

    /**
     * Send a JSON response
     *
     * @param Request $request
     * @param Response $response
     * @param $data
     *
     * @return Response
     */
    private function renderJson(Request $request, Response $response, $data) {
        return $response->withHeader('Content-Type', 'application/json')->withJson($data);
    }

    /**
     * Send the response through the Twig Engine
     *
     * @param Request $request
     * @param Response $response
     * @param string $template
     * @param $data
     *
     * @return ResponseInterface
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws TemplateNotFoundException
     */
    private function renderTemplate(Request $request, Response $response, string $template, $data) {
        return $this->View()->render($response, $template, $data);
    }

    /**
     * Check if the template exists
     *
     * @param $template
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Twig_Error_Loader
     * @throws TemplateNotFoundException
     */
    private function hasTemplate($template) : bool {
        return $this->View()->hasTemplate($template);
    }

    /**
     * If no Twig Engine is loaded, create one
     *
     * @return CustomTwig
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Twig_Error_Loader
     * @throws TemplateNotFoundException
     */
    public function View() {
        /** @var TemplateManagementService $templateManagementService */
        $templateManagementService = Oforge()->Services()->get('template.management');
        $activeTemplate            = $templateManagementService->getActiveTemplate();
        $templatePath              = DIRECTORY_SEPARATOR . Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $activeTemplate->getName();

        if (!$this->view) {
            $configService = Oforge()->Services()->get("config");
            $debug         = $configService->get("system_debug");

            /** @var $plugins Plugin[] */
            $plugins = Oforge()->Services()->get("plugin.access")->getActive();
            $paths   = [];

            $paths[Twig_Loader_Filesystem::MAIN_NAMESPACE] = [ROOT_PATH . DIRECTORY_SEPARATOR . $templatePath];
            $paths["parent"]                               = [];

            foreach ($plugins as $plugin) {
                $viewsDir = ROOT_PATH . DIRECTORY_SEPARATOR . Statics::PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin->getName() . DIRECTORY_SEPARATOR
                            . Statics::VIEW_DIR;

                if (file_exists($viewsDir)) {
                    array_push($paths["parent"], $viewsDir);
                    array_push($paths[Twig_Loader_Filesystem::MAIN_NAMESPACE], $viewsDir);

                    if (!array_key_exists($plugin->getName(), $paths)) {
                        $paths[$plugin->getName()] = [];
                    }

                    array_push($paths[$plugin->getName()], $viewsDir);
                }
            }

            array_push($paths["parent"], ROOT_PATH . "/Themes/Base");
            array_push($paths[Twig_Loader_Filesystem::MAIN_NAMESPACE], ROOT_PATH . "/Themes/Base");

            $this->view = new CustomTwig($paths, [
                'cache'       => ROOT_PATH . Statics::THEME_CACHE_DIR,
                'auto_reload' => $debug,
                'debug'       => $debug,
            ]);

            if ($debug) {
                $this->view->getEnvironment()->enableDebug();
                $this->view->getEnvironment()->addExtension(new Twig_Extension_Debug());
                $this->view->getEnvironment()->addExtension(new TwigOforgeDebugExtension());
            }
        }

        return $this->view;
    }
}
