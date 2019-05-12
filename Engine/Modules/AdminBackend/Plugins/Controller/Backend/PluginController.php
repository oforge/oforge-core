<?php

namespace Oforge\Engine\Modules\AdminBackend\Plugins\Controller\Backend;

use Exception;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\CouldNotActivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\PluginAlreadyActivatedException;
use Oforge\Engine\Modules\Core\Exceptions\PluginNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\PluginNotInstalledException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\Core\Services\PluginStateService;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\TemplateEngine\Core\Exceptions\InvalidScssVariableException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PluginController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Plugins\Controller\Backend
 * @EndpointClass(path="/backend/plugins", name="backend_plugins", assetScope="Backend")
 */
class PluginController extends BaseCrudController {
    /** @var string $model */
    protected $model = Plugin::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name' => 'id',
            'type' => CrudDataTypes::INT,
            'crud' => [
                'index' => 'readonly',
            ],
        ],
        [
            'name'  => 'name',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'crud_plugin_name', 'default' => 'Plugin name'],
            'crud'  => [
                'index' => 'readonly',
            ],
        ],
        [
            'name'     => 'action',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => ['key' => 'crud_plugin_action', 'default' => 'Action'],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Backend/Plugin/Index/ActionColumn.twig',
            ],
        ],
    ];
    /** @var array $crudActions */
    protected $crudActions = [
        'index'  => true,
        'create' => false,
        'view'   => false,
        'update' => false,
        'delete' => false,
    ];

    public function __construct() {
        parent::__construct();
    }

    /**
     * @EndpointAction(path="/activate/{name}")
     */
    public function activateAction(Request $request, Response $response, array $args) {
        if (!isset($args['name'])) {
            // return RedirectHelper::redirect($response, 'backend_plugins');
        }
        $pluginName = $args['name'];
        /** @var PluginStateService $pluginStateService */
        $pluginStateService = Oforge()->Services()->get('plugin.state');

        try {
            // $pluginStateService->activate($pluginName);
        } catch (PluginNotFoundException $exception) {
        } catch (PluginNotInstalledException $exception) {
        } catch (CouldNotActivatePluginException $exception) {
        } catch (PluginAlreadyActivatedException $exception) {
        } catch (TemplateNotFoundException | InvalidScssVariableException $exception) {

        } catch (Exception $exception) {
        }

        var_dump($pluginName);
        die();
        // Oforge()->View()->assign(['test' => $pluginID]);
        // TODO
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/deactivate/{name}")
     */
    public function deactivateAction(Request $request, Response $response, array $args) {
        /** @var PluginStateService $pluginStateService */
        $pluginStateService = Oforge()->Services()->get('plugin.state');
        //TODO
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/deinstall/{name}")
     */
    public function deinstallAction(Request $request, Response $response, array $args) {
        /** @var PluginStateService $pluginStateService */
        $pluginStateService = Oforge()->Services()->get('plugin.state');
        //TODO
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/install/{name}")
     */
    public function installAction(Request $request, Response $response, array $args) {
        /** @var PluginStateService $pluginStateService */
        $pluginStateService = Oforge()->Services()->get('plugin.state');
        //TODO
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/reinstall/{name}")
     */
    public function reinstallAction(Request $request, Response $response, array $args) {
        /** @var PluginStateService $pluginStateService */
        $pluginStateService = Oforge()->Services()->get('plugin.state');
        //TODO
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/reinstall-activate/{name}")
     */
    public function reinstallActivateAction(Request $request, Response $response, array $args) {
        /** @var PluginStateService $pluginStateService */
        $pluginStateService = Oforge()->Services()->get('plugin.state');
        //TODO
    }

    /** @EndpointAction(create=false) */
    public function updateAction(Request $request, Response $response, array $args) {
    }

    /** @EndpointAction(create=false) */
    public function createAction(Request $request, Response $response) {
    }

    /** @EndpointAction(create=false) */
    public function viewAction(Request $request, Response $response, array $args) {
    }

    /** @EndpointAction(path="/delete/{name}") */
    public function deleteAction(Request $request, Response $response, array $args) {
        //TODO
    }

}
