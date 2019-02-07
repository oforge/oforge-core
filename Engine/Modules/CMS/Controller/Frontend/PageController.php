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
            if(isset($path)) {
                $normalized = $pagePathService->normalize($page);
                Oforge()->View()->assign($normalized);
                return $response;
            }
        }

        Oforge()->View()->assign(["template.path" => "/Frontend/Page/404.twig"]);

        return $response->withStatus(404);
    }
}