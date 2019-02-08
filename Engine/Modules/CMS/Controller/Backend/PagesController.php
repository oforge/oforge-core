<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:57
 */

namespace Oforge\Engine\Modules\CMS\Controller\Backend;

use Slim\Http\Request;
use Slim\Http\Response;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\CMS\Services\PageService;
use Oforge\Engine\Modules\CMS\Services\PageBuilderService;

class PagesController extends AbstractController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        $contentTypeService = OForge()->Services()->get("content.type");
        $pageBuilderService = OForge()->Services()->get("pages.tree.view");
        
        $data = [
            "js" => ["cms_page_controller_jstree_config" => $pageBuilderService->generateJsTreeConfigJSON()],
            "pages" => $pageBuilderService->getPageArray(),
            "contentTypeGroups" => $contentTypeService->getContentTypeGroupArray(),
            "post" => $_POST
            
        ];
        
        Oforge()->View()->assign($data);
    }
}