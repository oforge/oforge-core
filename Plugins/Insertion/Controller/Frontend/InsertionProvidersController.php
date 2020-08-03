<?php

namespace Insertion\Controller\Frontend;

use Insertion\Services\InsertionProfileService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class InsertionProvidersController
 *
 * @package Insertion\Controller\Frontend
 * @EndpointClass(path="/insertions/provider", name="insertions_provider", assetBundles="Frontend")
 */
class InsertionProvidersController {

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /** @var InsertionProfileService $insertionProfileService */
        $insertionProfileService = Oforge()->Services()->get('insertion.profile');

        Oforge()->View()->assign([
            'insertionProfiles' => $insertionProfileService->getInsertionProvidersData(),
        ]);
    }

}
