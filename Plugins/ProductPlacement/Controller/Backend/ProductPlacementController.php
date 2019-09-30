<?php

namespace ProductPlacement\Controller\Backend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use ProductPlacement\Models\ProductPlacement;

/**
 * Class CategoryController
 *
 * @package ProductPlacement\Controller\Backend\ProductPlacement
 * @EndpointClass(path="/backend/product-placement", name="backend_product_placement", assetScope="Backend")
 */
class ProductPlacementController extends BaseCrudController {
    /** @var string $model */
    protected $model = ProductPlacement::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'   => 'source',
            'type'   => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'plugin_product_placement_property_source',
                'default' => [
                    'en' => 'Source',
                    'de' => 'Quelle',
                ],
            ],
            'crud'   => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],# source
        [
            'name'  => 'format',
            'type'  => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'plugin_product_placement_property_format',
                'default' => [
                    'en' => 'Format',
                    'de' => 'Format',
                ],
            ],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],#format
    ];
}
