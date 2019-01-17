<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:57
 */

namespace Oforge\Engine\Modules\CMS\Controller\Frontend;

use Oforge\Engine\Modules\CMS\Services\PageService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

class PageController extends AbstractController {
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        /**
         * @var PageService $pagePathService
         */
        $pagePathService = Oforge()->Services()->get("page.path");
        $path = $request->getUri()->getPath();
        
        $page = null;
        if ($pagePathService->hasPath($path)) {
            $page = $pagePathService->getPage($path);
            $normalized = $pagePathService->normalize($path);
            Oforge()->View()->assign(["page" => $normalized]);
            return $response;
        }
        //add 404
        
        /* TODO:
           
           - add pageservice and models ([page] id, name, url, {language}) ([contentblock] id, name, type, content), ([pagecontent] id, pageid, contentid)
           - add page crud
           - add content crud
        */
    }
}