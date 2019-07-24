<?php

namespace Helpdesk\Controller\Backend;

use Doctrine\ORM\ORMException;
use Helpdesk\Models\IssueTypeGroup;
use Helpdesk\Models\IssueTypes;
use Insertion\Models\InsertionTypeGroup;
use Insertion\Services\AttributeService;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

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
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /** @var GenericCrudService $crud */
        $crud = Oforge()->Services()->get('crud');
        /** @var AttributeService $attributeService */
        $count           = $crud->count(IssueTypeGroup::class);
        $limit           = 20;
        $offset          = 0;
        $page            = 1;
        $pageCount       = $count > 0 ? ceil($count / $limit) : 1;
        $issueTypeGroups = [];

        if (isset($request->getQueryParams()['page']) && is_numeric($request->getQueryParams()['page'])) {
            $offset = $limit * ($request->getQueryParams()['page'] - 1);
            $page   = $request->getQueryParams()['page'];
        }

        foreach ($crud->list(IssueTypeGroup::Class, [], null, $offset, $limit) as $issueTypeGroup) {
            $issueTypeGroups[] = $issueTypeGroup->toArray(1);
        }

        Oforge()->View()->assign([
            'issueTypeGroups' => $issueTypeGroups,
            'page'            => $page,
            'pageCount'       => $pageCount,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\NotFoundException
     * @EndpointAction(path="/edit")
     */
    public function editAction(Request $request, Response $response) {
        /** @var GenericCrudService $crud */
        $crud             = Oforge()->Services()->get('crud');
        $issueTypeGroupId = intval($request->getQueryParams()['id']);
        /** @var IssueTypeGroup $issueTypeGroup */
        $issueTypeGroup = $crud->getById(IssueTypeGroup::class, $issueTypeGroupId);

        if ($request->isPost()) {
            $body           = $request->getParsedBody();
            $body['values'] = json_decode($body['values'], true);
            if (isset($request->getQueryParams()['id'])) {
                $idList = [];
                foreach ($issueTypeGroup->getIssueTypes() as $issueType) {
                    $idList[] = $issueType->getId();
                }

                foreach ($body['values'] as $issueType) {
                    if (isset($issueType['id'])) {
                        $crud->update(IssueTypes::class, [
                            'id' => $issueType['id'],
                            'issueTypeName'  => $issueType['issueTypeName'],
                        ], false);
                        $idList = array_diff($idList, [$issueType['id']]);
                    } else {
                        $crud->create(IssueTypes::class, [
                            'issueTypeName'  => $issueType['issueTypeName'],
                            'issueTypeGroup' => $issueTypeGroup,
                        ]);
                    }
                }
                $crud->flush(IssueTypes::class);
                foreach ($idList as $id) {
                    $crud->delete(IssueTypes::class, $id);
                }
            } else {
                $issueTypeGroup = $crud->create(IssueTypeGroup::class, ['issueTypeGroupName' => $body['issueTypeGroupName']]);
                foreach ($body['values'] as $issueType) {
                    $crud->create(IssueTypes::class, [
                        'issueTypeName'  => $issueType['issueTypeName'],
                        'issueTypeGroup' => $issueTypeGroup,
                    ]);
                }
            }
            /** @var Router $router */
            $router = Oforge()->App()->getContainer()->get('router');
            $uri    = $router->pathFor('backend_helpdesk_settings');

            return $response->withRedirect($uri, 302);
        }

        Oforge()->View()->assign(["issueTypeGroup" => $issueTypeGroup->toArray()]);
    }

    /**
     */
    public function initPermissions() {
        $this->ensurePermission('indexAction', BackendUser::ROLE_ADMINISTRATOR);
    }
}
