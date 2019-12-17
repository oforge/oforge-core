<?php

namespace Insertion\Controller\Backend;

use Doctrine\ORM\ORMException;
use Insertion\Models\InsertionSeoContent;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Seo\Services\SeoService;

/**
 * Class BackendInsertionSeoContentController
 * @package Insertion\Controller\Backend\BackendInsertionSeoContentController
 * @EndpointClass(path="/backend/insertions/seo-content", name="backend_insertions_seo_content", assetScope="Backend")
 */
class BackendInsertionSeoContentController extends BaseCrudController {
    protected  $model = InsertionSeoContent::class;
    protected $modelProperties = [
        [
            'name'      => 'seoTargetUrl',
            'label' => ['key' => 'seo_target_url', 'default' => ['en' => 'seo target url', 'de' => 'Seo Ziel Url']],
            'type'  => CrudDataTypes::SELECT,
            'crud'  => [
                'index'  => 'editable',
                'view'   => 'editable',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'editable',
            ],
            'list' => 'getSeoTargetUrls'
        ],
        [
            'name'      => 'metaTitle',
            'label'     => ['key' => 'meta_title', 'default' => ['en' => 'meta title', 'de' => 'Meta Titel']],
            'type'      => CrudDataTypes::STRING,
            'crud'      => [
                'index'     => 'editable',
                'view'      => 'editable',
                'create'    => 'editable',
                'update'    => 'editable',
                'delete'    => 'editable',
            ],
        ],
        [
            'name'      => 'metaDescription',
            'label'     => ['key' => 'meta_description', 'default' => ['en' => 'meta description', 'de' => 'Meta Beschreibung']],
            'type'      => CrudDataTypes::STRING,
            'crud'      => [
                'index'     => 'editable',
                'view'      => 'editable',
                'create'    => 'editable',
                'update'    => 'editable',
                'delete'    => 'editable',
            ],
        ],
        [
            'name'      => 'contentElements',
            'label'     => ['key' => 'content_elements', 'default' => ['en' => 'content elements', 'de' => 'Inhaltselemente']],
            'type'      => CrudDataTypes::STRING,
            'crud'      => [
                'index'     => 'editable',
                'view'      => 'editable',
                'create'    => 'editable',
                'update'    => 'editable',
                'delete'    => 'editable',
            ],
        ],
    ];

    /**
     * @return array
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    protected function getSeoTargetUrls() {
        /** @var SeoService $seoService */
        $seoService = Oforge()->Services()->get('seo');
        return $seoService->getAllSeoUrls();
    }

    /**
     * Override the entity object in the Twig template.
     * We have an input array and only need the id to compare it with the selected value
     *
     * @param AbstractModel|null $entity
     * @param string $crudAction
     *
     * @return array
     */
    protected function prepareItemDataArray(?AbstractModel $entity, string $crudAction) : array {
        $data = parent::prepareItemDataArray($entity, $crudAction);
        if (isset($data['seoTargetUrl']['id'])) {
            $data['seoTargetUrl'] = $data['seoTargetUrl']['id'];
        }
        return $data;
    }
}
