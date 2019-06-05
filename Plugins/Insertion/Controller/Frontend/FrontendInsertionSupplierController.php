<?php
namespace Insertion\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use FrontendUserManagement\Services\FrontendUserService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class FrontendInsertionSupplierController
 *
 * @package Insertion\Controller\Frontend
 * @EndpointClass(path="/frontend/insertions/supplier/{id]", name="frontend_insertions_supplier", assetScope="Frontend")
 */
class FrontendInsertionSupplierController extends SecureFrontendController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return mixed
     * @throws ServiceNotFoundException
     *
     */
    public function indexAction(Request $request, Response $response) {
        /** @var  $userService */
        $userService = Oforge()->Services()->get('frontend.user');
        $user        = $userService->getUser();

        /** @var  $insertionListService */
        $insertionSupplierService = Oforge()->Services()->get('insertion.supplier');
        $result[] = ["insertions" => $insertionSupplierService->getSupplierInsertions(1)];

        Oforge()->View()->assign($result);

    }
}