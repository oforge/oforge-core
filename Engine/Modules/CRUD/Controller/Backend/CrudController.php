<?php
/**
 * Created by PhpStorm.
 * User: steff
 * Date: 14.12.2018
 * Time: 15:08
 */

namespace Oforge\Engine\Modules\AdminBackendDocumentation\Controller\Backend;


use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;
use Slim\Http\Request;
use Slim\Http\Response;

class CrudController extends SecureBackendController
{
    protected $model = null;

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


    public function indexAction(Request $request, Response $response)
    {
        if (isset($model)) {
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
                        case "list":
                            $this->list($request, $response);
                            break;
                    }
                }

                $this->list($request, $response);
            } else {
                $this->list($request, $response);
            }
        }
    }

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

    public function update(Request $request, Response $response)
    {
        if (isset($model)) {
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
        if (isset($model)) {
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
        Oforge()->View()->assign(["result" => $result]);
    }

    public function detailAction(Request $request, Response $response)
    {
        if (isset($model)) {
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


