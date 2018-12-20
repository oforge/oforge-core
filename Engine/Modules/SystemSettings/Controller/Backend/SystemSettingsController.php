<?php
/**
 * Created by PhpStorm.
 * User: Steffen
 * Date: 15.12.2018
 * Time: 16:40
 */

namespace Oforge\Engine\Modules\SystemSettings\Controller\Backend;


use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Models\Config\Element;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;
use Slim\Http\Request;
use Slim\Http\Response;

class SystemSettingsController extends SecureBackendController
{

    public function initPermissions()
    {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }

    public function indexAction(Request $request, Response $response)
    {
        /**
         * @var $settingsService GenericCrudService
         */
        $settingsService = Oforge()->Services()->get("crud");
        $configElements = $settingsService->list(Element::class);


        $data = array();

        foreach ($configElements as $element) {
            $key =  explode("_", $element['name'])[0];
            if(!array_key_exists($key, $data)) {
                $data[$key] = array();
            }
            array_push($data[$key], $element);

            //$data[$key[]] = $element;
        }

        Oforge()-> View()->assign(["config" => $data]);
    }
}