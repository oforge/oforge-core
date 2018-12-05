<?php
namespace Oforge\Engine\Modules\Test\Controller\Frontend;

use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Services\PluginStateService;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController extends AbstractController {
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        // Some very difficult algorithm to fetch data
        $data = ["greeting" => "Hello TEST HomeController"];
        
        /**
         * @var $pluginService PluginStateService
         */
        $pluginService = Oforge()->Services()->get("plugin.state");
        
        try {
            $pluginService->install("Test");
            $pluginService->activate("Test");
            $pluginService->uninstall("Test2");
        } catch(\Exception $e) {
            Oforge()->View()->assign(["error" => $e->getMessage()]);
        }
        
        Oforge()->View()->assign($data);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function testAction(Request $request, Response $response) {
        // Some very difficult algorithm to fetch data
        $data = ["greeting" => "Hello TEST HomeController"];

        /**
         * @var $pluginService PluginStateService
         */
        $pluginService = Oforge()->Services()->get("plugin.state");


        Oforge()->View()->assign($data);
    }
}
