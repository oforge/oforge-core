<?php
/**
 * Created by PhpStorm.
 * User: steff
 * Date: 14.12.2018
 * Time: 15:08
 */

namespace Oforge\Engine\Modules\AdminBackendDocumentation\Controller\Backend;


use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Slim\Http\Request;
use Slim\Http\Response;

class DocumentationUIController extends SecureBackendController
{

    public function indexAction(Request $request, Response $response)
    {
        $data = ['page_header' => 'Hello from the TestPlugin', 'page_header_description' => "Mega awesome optional additional description"];
        Oforge()->View()->assign($data);
    }

    public function generalAction(Request $request, Response $response)
    {
        /**
         * @var $sidebarNavigation SidebarNavigationService
         */
        $sidebarNavigation = Oforge()->Services()->get("backend.sidebar.navigation");

        $sidebarNavigation->put([
            "name" => "backend_documentation",
            "order" => 100
        ]);

        $sidebarNavigation->put([
            "name" => "backend_ui_elements",
            "order" => 100,
            "parent" => "backend_documentation",
            "icon" => "fa fa-laptop"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_documentation_ui_general",
            "order" => 1,
            "parent" => "backend_ui_elements",
            "icon" => "fa fa-circle-o",
            "path" => "backend_documentation_ui_general"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_documentation_ui_icons",
            "order" => 2,
            "parent" => "backend_ui_elements",
            "icon" => "fa fa-circle-o",
            "path" => "backend_documentation_ui_icons"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_documentation_ui_buttons",
            "order" => 3,
            "parent" => "backend_ui_elements",
            "icon" => "fa fa-circle-o",
            "path" => "backend_documentation_ui_buttons"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_documentation_ui_silders",
            "order" => 4,
            "parent" => "backend_ui_elements",
            "icon" => "fa fa-circle-o",
            "path" => "backend_documentation_ui_silders"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_documentation_ui_timeline",
            "order" => 5,
            "parent" => "backend_ui_elements",
            "icon" => "fa fa-circle-o",
            "path" => "backend_documentation_ui_timeline"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_documentation_ui_modals",
            "order" => 6,
            "parent" => "backend_ui_elements",
            "icon" => "fa fa-circle-o",
            "path" => "backend_documentation_ui_modals"
        ]);

    }

    public function iconsAction(Request $request, Response $response)
    {

    }
    public function buttonsAction(Request $request, Response $response)
    {

    }
    public function modalsAction(Request $request, Response $response)
    {

    }
    public function slidersAction(Request $request, Response $response)
    {

    }
    public function timelineAction(Request $request, Response $response)
    {

    }
}