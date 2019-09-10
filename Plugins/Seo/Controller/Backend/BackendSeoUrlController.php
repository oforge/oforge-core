<?php

namespace Seo\Controller\Backend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterComparator;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroupByOrder;
use Seo\Models\SeoUrl;

/**
 * Class CategoryController
 *
 * @package Seo\Controller\Backend\Seo
 * @EndpointClass(path="/backend/seo", name="backend_seo", assetScope="Backend")
 */
class BackendSeoUrlController extends BaseCrudController {
    /** @var string $model */
    protected $model = SeoUrl::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'  => 'target',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'plugin_seo_property_target_url', 'default' => 'Target'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'  => 'source',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'plugin_seo_property_source_url', 'default' => 'Source'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
    ];

    protected $indexFilter = [
        'target' => [
            'type'    => CrudFilterType::TEXT,
            'label'   => ['key' => 'plugin_seo_filter_target_url', 'default' => 'Search in target url'],
            'compare' => CrudFilterComparator::LIKE,
        ],
        'source'     => [
            'type'    => CrudFilterType::TEXT,
            'label'   => ['key' => 'plugin_seo_filter_source_url', 'default' => 'Search in source url'],
            'compare' => CrudFilterComparator::LIKE,
        ],
    ];

    /** @var array $indexOrderBy */
    protected $indexOrderBy = [
        'target' => CrudGroupByOrder::ASC,
    ];

    public function __construct() {
        parent::__construct();
    }
}
