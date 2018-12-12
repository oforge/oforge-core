<?php

namespace Oforge\Engine\Modules\TemplateEngine\Manager;

use Oforge\Engine\Modules\Core\Abstracts\AbstractTemplateManager;
use Oforge\Engine\Modules\Core\Helper\Helper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\TemplateEngine\Services\TemplateManagementService;
use Oforge\Engine\Modules\TemplateEngine\Services\TemplateRenderService;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class TemplateManager extends AbstractTemplateManager {
    protected static $instance;

    /**
     * Create a singleton instance of the TemplateManager
     *
     * @return TemplateManager
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new TemplateManager();
        }
        return self::$instance;
    }
    
    /**
     * Initialize and configure the TemplateManager.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function init() {
        /**
         * @var $templateManagementService TemplateManagementService
         */
        $templateManagementService = Oforge()->Services()->get("template.management");
        $templateFiles = Helper::getTemplateFiles(ROOT_PATH . DIRECTORY_SEPARATOR . Statics::TEMPLATE_DIR);

        foreach($templateFiles as $templateName => $dir ) {
            $templateManagementService->register($templateName);
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
     * @param $data
     *
     * @return ResponseInterface|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Twig_Error_Loader
     */
    public function render(Request $request, Response $response, $data) {
        /**
         * @var $templateRenderService TemplateRenderService
         */
        $templateRenderService = Oforge()->Services()->get("template.render");
        return $templateRenderService->render($request, $response, $data);
    }
}
