<?php

namespace Insertion\Controller\Frontend;

use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\Event\OnFlushEventArgs;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\FrontendUserService;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Services\InsertionCreatorService;
use Insertion\Services\InsertionFeedbackService;
use Insertion\Services\InsertionListService;
use Insertion\Services\InsertionService;
use Insertion\Services\InsertionTypeService;
use Insertion\Services\InsertionUpdaterService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class MessengerController
 *
 * @package Messenger\Controller\Frontend
 * @EndpointClass(path="/frontend/account/insertions", name="frontend_account_insertions", assetScope="Frontend")
 */
class FrontendUsersInsertionController extends SecureFrontendController {


    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function indexAction(Request $request, Response $response) {


    }

    public function initPermissions() {
        $this->ensurePermissions('accountListAction', User::class);
    }
}
