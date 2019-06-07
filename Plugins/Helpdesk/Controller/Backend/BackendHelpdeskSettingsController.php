<?php


namespace Helpdesk\Controller\Backend;

use Doctrine\ORM\ORMException;
use Helpdesk\Models\IssueTypeGroup;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class BackendHelpdeskController
 *
 * @package Helpdesk\Controller\Backend
 * @EndpointClass(path="/backend/helpdesk/settings", name="backend_helpdesk_settings", assetScope="Backend")
 */
class BackendHelpdeskSettingsController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        $crud = Oforge()->Services()->get('crud');

        $issueTypeGroups = [];
        foreach ($crud->list(IssueTypeGroup::Class) as $issueTypeGroup) {
            $issueTypeGroups[] = $issueTypeGroup->toArray();
        }

        Oforge()->View()->assign(['issueTypeGroups' => $issueTypeGroups]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/edit[/{id}]")
     */
    public function editAction(Request $request, Response $response, array $args) {

    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }
}