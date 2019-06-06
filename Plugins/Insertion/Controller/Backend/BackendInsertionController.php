<?php

namespace Insertion\Controller\Backend;

use Doctrine\ORM\ORMException;
use Insertion\Models\Insertion;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Forge\ForgeEntityManager;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;

/**
 * Class BackendInsertionController
 *
 * @package Oforge\Engine\Modules\I18n\Controller\Backend
 * @EndpointClass(path="/backend/insertions", name="backend_insertions", assetScope="Backend")
 */
class BackendInsertionController extends BaseCrudController {
    /** @var string $model */
    protected $model = Insertion::class;
    /** @var array $modelProperties */
    protected $modelProperties = [

        [
            'name'  => 'link',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'plugin_insertion_link', 'default' => 'Link'],
            'crud'  => [
                'index'  => 'readonly'
            ],
            'renderer' => [
                'custom' => 'Plugins/Insertion/Backend/BackendInsertion/CRUD/RenderLink.twig',
            ],
        ],
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
            'label' => ['key' => 'plugin_insertion_name', 'default' => 'Name'],
            'crud'  => [
                'index'  => 'readonly'
            ],
            'renderer' => [
                'custom' => 'Plugins/Insertion/Backend/BackendInsertion/CRUD/RenderName.twig',
            ],
        ],
        [
            'name'  => 'title',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'plugin_insertion_title', 'default' => 'Title'],
            'crud'  => [
                'index'  => 'readonly'
            ],
            'renderer' => [
                'custom' => 'Plugins/Insertion/Backend/BackendInsertion/CRUD/RenderTitle.twig',
            ],
        ],
        [
            'name'     => 'user',
            'type'     => CrudDataTypes::CUSTOM,
            'label' => ['key' => 'plugin_insertion_user', 'default' => 'User'],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/Insertion/Backend/BackendInsertion/CRUD/RenderUser.twig',
            ],
        ],
        [
            'name'  => 'active',
            'type'  => CrudDataTypes::BOOL,
            'label' => ['key' => 'plugin_insertion_active', 'default' => 'Active'],
            'crud'  => [
                'index'  => 'editable',
                'view'   => 'editable',
                'create' => 'off',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'  => 'moderation',
            'type'  => CrudDataTypes::BOOL,
            'label' => ['key' => 'plugin_insertion_moderation', 'default' => 'Freigegeben'],
            'crud'  => [
                'index'  => 'editable',
                'view'   => 'editable',
                'create' => 'off',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ]
    ];

    public function __construct() {
        parent::__construct();
    }

}