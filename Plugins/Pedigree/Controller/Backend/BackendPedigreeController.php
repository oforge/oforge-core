<?php

namespace Pedigree\Controller\Backend;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMException as ORMExceptionAlias;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Pedigree\Models\Ancestor;
use Pedigree\Services\PedigreeService;
use Slim\Http\Request;
use Slim\Http\Response;
use \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Router;
use Exception;

/**
 * Class BackendPedigreeController
 *
 * @package Pedigree\Controller\Backend
 * @EndpointClass(path="/backend/insertion/pedigree", name="backend_insertion_pedigree", assetScope="Backend")
 */
class BackendPedigreeController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     * @throws ORMExceptionAlias
     * @throws ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        /** @var PedigreeService $pedigreeService */
        $pedigreeService    = Oforge()->Services()->get('pedigree');
        $nameCount          = $pedigreeService->getNameCount();
        $limit              = 500;
        $offset             = 0;
        $page               = 1;
        $pageCount          = $nameCount > 0 ? ceil($nameCount / $limit) : 1;

        if (isset($request->getQueryParams()['page']) && is_numeric($request->getQueryParams()['page'])) {
            $offset = $limit * ($request->getQueryParams()['page'] - 1);
            $page   = $request->getQueryParams()['page'];
        }
        $names = $pedigreeService->getAllAncestors($limit, $offset);

        $names = array_map(function(Ancestor $name) {return $name->toArray();}, $names);

        Oforge()->View()->assign([
            'content'     => [
                'names' => $names,
            ],
            'pageCount'   => $pageCount,
            'currentPage' => $page,
        ]);
    }

    public function deleteAction(Request $request, Response $response) {
        /** @var PedigreeService $pedigreeService */
        $pedigreeService = Oforge()->Services()->get('pedigree');
        $ancestorId = $request->getQueryParams()['id'];

        if (isset($ancestorId) && $ancestorId != null && $request->isPost()) {
            try {
                $pedigreeService->deleteAncestor($ancestorId);
            } catch (Exception $exception) {
                Oforge()->View()->Flash()->addMessage('error',
                    I18N::translate('backend_insertion_delete_failed'));
            }
        }

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $uri    = $router->pathFor('backend_insertion_pedigree');

        return $response->withRedirect($uri, 302);
    }

    public function addAction(Request $request, Response $response) {
        /** @var PedigreeService $pedigreeService */
        $pedigreeService = Oforge()->Services()->get('pedigree');
        $body = $request->getParsedBody();
        $name = $body['name'];

        if (isset($name) && $name != null && $request->isPost()) {
            try {
                $pedigreeService->addAncestor($name);
            } catch (Exception $exception) {
                Oforge()->View()->Flash()->addMessage('error',
                    I18N::translate('backend_insertion_add_failed'));
            }
        }

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $uri    = $router->pathFor('backend_insertion_pedigree');

        return $response->withRedirect($uri, 302);
    }

    public function initPermissions() {
        $this->ensurePermissions([
            'indexAction',
            'deleteAction',
            'editAction',
            'addAction'
        ], BackendUser::ROLE_MODERATOR);
    }
}
