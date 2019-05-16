<?php

namespace Blog\Controller\Backend;

use Blog\Models\Category;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;

/**
 * Class CategoryController
 *
 * @package Blog\Controller\Backend\Blog
 * @EndpointClass(path="/backend/blog/categories", name="backed_blog_categories", assetScope="Backend")
 */
class CategoryController extends BaseCrudController {
    /** @var string $model */
    protected $model = Category::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'     => 'name',
            'type'     => CrudDataTypes::STRING,
            'label'    => ['key' => 'plugin_blog_category_name', 'default' => 'Name'],
            'crud'     => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'editable',
            ],
        ],
        [
            'name'     => 'language',
            'type'     => CrudDataTypes::SELECT,
            'label'    => ['key' => 'plugin_blog_category_language', 'default' => 'Name'],
            'crud'     => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'readonly',
                'delete' => 'editable',
            ],
            'list'          => 'getSelectLanguages',
        ],
    ];
    //     Sprache, Link zum Frontend
    // Linkbutton "Posts (of category)" mit Anzahl Posts und Icon (ala Wordpress)

    public function __construct() {
        parent::__construct();
    }

}
