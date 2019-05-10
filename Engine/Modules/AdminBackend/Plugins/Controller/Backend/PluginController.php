<?php

namespace Oforge\Engine\Modules\AdminBackend\Plugins\Controller\Backend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;

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
                'index'  => 'readonly',
            ],
        ],
        [
            'name'  => 'action',
            'type'     => CrudDataTypes::CUSTOM,
            'label' => ['key' => 'crud_plugin_action', 'default' => 'Action'],
            'crud'  => [
                'index'  => 'readonly',
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
     * @EndpointAction(path="/{id:\d+}/activate")
     */
    public function activateAction() {

    }

}
