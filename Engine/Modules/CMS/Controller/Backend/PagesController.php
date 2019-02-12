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
use Oforge\Engine\Modules\CMS\Services\PageTreeService;
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
        $contentTypeService = OForge()->Services()->get("content.type.service");
        $pageTreeService = OForge()->Services()->get("page.tree.service");
        $pageBuilderService = OForge()->Services()->get("page.builder.service");
        
        $data = [
            "js" => ["cms_page_controller_jstree_config" => $pageTreeService->generateJsTreeConfigJSON()],
            "pages" => $pageTreeService->getPageArray(),
            "content" => $pageBuilderService->getPageArray(2),
            "contentTypeGroups" => $contentTypeService->getContentTypeGroupArray(),
            "post" => $_POST
            
        ];
        
        Oforge()->View()->assign($data);
    }
}