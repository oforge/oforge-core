<?php

namespace Oforge\Engine\Modules\TemplateEngine\Core\Manager;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractTemplateManager;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Helper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\TemplateEngine\Core\Exceptions\InvalidScssVariableException;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateManagementService;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateRenderService;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Class TemplateManager
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Core\Manager
 */
class TemplateManager extends AbstractTemplateManager {
    /** @var TemplateManager $instance */
    protected static $instance;

    /**
     * Create a singleton instance of the TemplateManager
     *
     * @return TemplateManager
     */
    public static function getInstance() : TemplateManager {
        if (!isset(self::$instance)) {
            self::$instance = new TemplateManager();
        }

        return self::$instance;
    }

    /**
     * Initialize and configure the TemplateManager.
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws InvalidScssVariableException
     * @throws TemplateNotFoundException
     */
    public function init() {
        /** @var TemplateManagementService $templateManagementService */
        $templateManagementService = Oforge()->Services()->get("template.management");

        $templateFiles = Helper::getTemplateFiles(ROOT_PATH . DIRECTORY_SEPARATOR . Statics::TEMPLATE_DIR);

        $templateManagementService->register(Statics::DEFAULT_THEME);

        foreach ($templateFiles as $templateName => $dir) {
            if ($templateName != Statics::DEFAULT_THEME) {
                $templateManagementService->register($templateName);
            }
        }
    }

    /**
     * This render function can be called either from a module controller or a template controller.
     * It checks, whether a template path based on the controllers namespace and the function name exists
     * [e.g.: Oforge/Engine/Modules/Test/Controller/Frontend/HomeController:indexAction => /Themes/$currentTheme/Test/Frontend/Home/Index.twig].
     * If the template is found, it gets rendered by the template engine, the fallback is a json response
     *
     * @param Request $request
     * @param Response $response
     * @param array $data
     *
     * @return ResponseInterface|Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws TemplateNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function render(Request $request, Response $response, array $data) {
        /** @var TemplateRenderService $templateRenderService */
        $templateRenderService = Oforge()->Services()->get("template.render");

        return $templateRenderService->render($request, $response, $data);
    }

}
