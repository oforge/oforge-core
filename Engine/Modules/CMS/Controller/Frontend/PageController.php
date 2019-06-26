<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:57
 */

namespace Oforge\Engine\Modules\CMS\Controller\Frontend;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\CMS\Services\PageService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class PageController
 *
 * @package Oforge\Engine\Modules\CMS\Controller\Frontend
 * @EndpointClass(path="/[{content:.*}]", name="frontend_page", assetScope="Frontend", order=99999)
 */
class PageController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /**
         * @var PageService $pagePathService
         */
        $pagePathService = Oforge()->Services()->get('page.path');
        $path            = $request->getUri()->getPath();

        $cmsContent = $pagePathService->loadContentForPagePath($path);
        $pagePath   = $pagePathService->getPagePath($path);

        $data     = Oforge()->View()->fetch();
        $language = $data["meta"]["language"];

        if ($cmsContent == null) {
            $path = "/" . $language . $path;

            $cmsContent = $pagePathService->loadContentForPagePath($path);
            $pagePath   = $pagePathService->getPagePath($path);
        }

        if ($cmsContent !== null) {
            if ($pagePath->getLanguage()->getIso() != $language) {
                foreach ($pagePath->getPage()->getPaths() as $path) {
                    if ($path->getLanguage()->getIso() == $language) {
                        $response = $response->withRedirect($path->getPath(), 301);
                    }
                }
            }

            // TODO: Remove meta assignment
            Oforge()->View()->assign([
                'content' => $cmsContent,
                "cms"     => $pagePath->toArray(),
                'meta'    => ["header_class" => "cms cms-page " . $pagePath->getPage()->getName(), "title" => $pagePath->getTitle(), "description" => $pagePath->getDescription()],
                'cache-for' => '2D'
            ]);

            return $response;
        }

        /** @var Router $router */
        $router   = Oforge()->App()->getContainer()->get('router');
        $uri      = $router->pathFor('not_found');
        $response = $response->withRedirect($uri, 301);

        return $response;

    }

}
