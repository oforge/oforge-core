<?php
/**
 * Created by PhpStorm.
 * User: steff
 * Date: 14.12.2018
 * Time: 15:08
 */

namespace Oforge\Engine\Modules\CRUD\Controller\Backend;


use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;
use Slim\Http\Request;
use Slim\Http\Response;

class CrudController extends SecureBackendController
{
    protected $model = null;

    /**
     * define your own table header
     *  e.g.
     *     protected $header = [
     *             ["name" => "id",  //key name
     *              "header" => "backend_column_id",  //own i18n column header name
     *              "type" => "text|int|object" //alternative type
     *              "value" => "name" //if type == object this is the key of the object
     *      ], ..
     *   ];
     *
     */
    protected $header = null;

    protected $editLine = true;
    /**
     * @var $crudService GenericCrudService
     */
    protected $crudService;
    /**
     * @var $i18nService InternationalizationService
     */
    private $i18nService;

    public function __construct()
    {
        $this->crudService = Oforge()->Services()->get("crud");
        $this->i18nService = Oforge()->Services()->get("i18n");
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ConfigOptionKeyNotExists
     * @throws NotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response)
    {
        if (isset($this->model)) {

            $params = $request->getParams();
            if (sizeof($params) > 0 && $request->isPost()) {

                if (isset($params["type"])) {
                    switch (strtolower($params["type"])) {
                        case "create":
                            $this->create($request, $response);
                            break;
                        case "update":
                            $this->update($request, $response);
                            break;
                        case "delete":
                            $this->delete($request, $response);
                            break;
                    }
                }
            }
        }

        $this->list($request, $response);

        Oforge()->View()->assign(["editInline" => $this->editLine]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Request $request, Response $response)
    {
        if (isset($model)) {
            $params = $request->getParams();
            if (isset($params["id"])) {
                $this->crudService->delete($this->model, $params["id"]);

                Oforge()->View()->assign(["message" => [
                    "type" => "danger",
                    "body" => "backend_message_delete_success_body",
                    "headline" => "backend_message_delete_success_headline"
                ]]);
            }
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigOptionKeyNotExists
     * @throws NotFoundException
     */
    public function update(Request $request, Response $response)
    {
        if (isset($this->model)) {
            $params = $request->getParams();

            $this->crudService->update($this->model, $params);

            Oforge()->View()->assign(["message" => [
                "type" => "danger",
                "body" => "backend_message_update_success_body",
                "headline" => "backend_message_update_success_headline"
            ]]);
        }
    }

    public function create(Request $request, Response $response)
    {
        if (isset($this->model)) {
            $params = $request->getParams();

            $this->crudService->create($this->model, $params);
            Oforge()->View()->assign(["message" => [
                "type" => "danger",
                "body" => "backend_message_create_success_body",
                "headline" => "backend_message_create_success_headline"
            ]]);
        }
    }

    private function list($request, $response)
    {
        $params = $request->getParams();
        $result = $this->crudService->list($this->model, $params);
        Oforge()->View()->assign(["result" => $result, "header" => $this->header ?: $this->crudService->definition($this->model)]);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function detailAction(Request $request, Response $response)
    {
        if (isset($this->model)) {
            $params = $request->getParams();

            $result = $this->crudService->list($this->model, $params);
            Oforge()->View()->assign(["result" => $result]);
        }
    }

    public function initPermissions()
    {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions("detailAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions("createAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions("updateAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions("deleteAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}


