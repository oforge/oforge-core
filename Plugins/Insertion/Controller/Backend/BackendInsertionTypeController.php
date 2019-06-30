<?php

namespace Insertion\Controller\Backend;

use Doctrine\ORM\ORMException as ORMExceptionAlias;
use Insertion\Models\AttributeKey;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Services\AttributeService;
use Insertion\Services\InsertionTypeService;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class FrontendHelpdeskController
 *
 * @package FrontendInsertion\Controller\Backend
 * @EndpointClass(path="/backend/insertion/type", name="backend_insertion_type", assetScope="Backend")
 */
class BackendInsertionTypeController extends SecureBackendController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @throws ORMExceptionAlias
     */
    public function indexAction(Request $request, Response $response) {
        /** @var InsertionTypeService $attributeService */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');
        $count                = $insertionTypeService->getInsertionTypeCount();
        $limit                = 20;
        $offset               = 0;
        $page                 = 1;
        $pageCount            = $count > 0 ? ceil($count / $limit) : 1;
        $data                 = [];

        if (isset($request->getQueryParams()['page']) && is_numeric($request->getQueryParams()['page'])) {
            $offset = $limit * ($request->getQueryParams()['page'] - 1);
            $page   = $request->getQueryParams()['page'];
        }

        $insertionTypes = $insertionTypeService->getInsertionTypeList($limit, $offset);

        foreach ($insertionTypes as $insertionType) {
            $data[] = $insertionType->toArray();
        }

        Oforge()->View()->assign([
            'content'     => [
                'insertionTypes' => $data,
            ],
            'pageCount'   => $pageCount,
            'currentPage' => $page,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/edit")
     *
     * @return Response
     * @throws ORMExceptionAlias
     * @throws ServiceNotFoundException
     */
    public function editAction(Request $request, Response $response) {
        /** @var AttributeService $attributeService */
        $attributeService = Oforge()->Services()->get('insertion.attribute');
        /** @var InsertionTypeService $insertionTypeService */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');
        $insertionTypeId      = $request->getQueryParams()['id'];

        if ($request->isPost()) {
            $body                             = $request->getParsedBody();
            $body['values']                   = json_decode($body['values'], true);
            $body['insertionTypeQuickSearch'] = $body['insertionTypeQuickSearch'] ? true : false;

            /** @var InsertionType $parent */
            $parent = $insertionTypeService->getInsertionTypeById($body['parent']);
            if (isset($request->getQueryParams()['id'])) {
                /** @var InsertionType $insertionType */
                $data = [
                    'name' => $body['name'],
                    'parent' => $parent,
                    'quickSearch' => $body['insertionTypeQuickSearch'],
                    'description' => $body['description'],
                    'image' => $body['image']
                ];
                $insertionType = $insertionTypeService->updateInsertionType($insertionTypeId, $data);
                /** @var InsertionTypeAttribute[] $insertionTypeAttributes */
                $insertionTypeAttributes = $insertionType->getAttributes();
                $idList                  = [];
                foreach ($insertionTypeAttributes as $value) {
                    $idList[] = $value->getId();
                }

                foreach ($body['values'] as $attribute) {
                    /** @var AttributeKey $attributeKey */
                    $attributeKey = $attributeService->getAttribute($attribute['attribute_key']);
                    if ($attribute['quick_search_order'] === '') {
                        $attribute['quick_search_order'] = null;
                    }
                    if (isset($attribute['id'])) {
                        $insertionTypeService->updateInsertionTypeAttribute($attribute['id'], $attributeKey, $attribute['is_top'],
                            $attribute['attribute_group'], $attribute['is_required'], $attribute['is_quick_search_filter'], $attribute['quick_search_order']);
                        $idList = array_diff($idList, [$attribute['id']]);
                    } else {
                        $insertionTypeService->addAttributeToInsertionType($insertionType, $attributeKey, $attribute['is_top'], $attribute['attribute_group'],
                            $attribute['is_required'], $attribute['is_quick_search_filter'], $attribute['quick_search_order']);
                    }
                }

                foreach ($idList as $id) {
                    $insertionTypeService->deleteInsertionTypeAttribute($id);
                }
            } else {
                /** @var InsertionType $insertionType */
                $insertionType = $insertionTypeService->createNewInsertionType($body['name'], $parent, $body['insertionTypeQuickSearch']);
                foreach ($body['values'] as $attribute) {
                    if ($attribute['quick_search_order'] === '') {
                        $attribute['quick_search_order'] = null;
                    }
                    $attributeKey = $attributeService->getAttribute($attribute['attribute_key']);
                    $insertionTypeService->addAttributeToInsertionType($insertionType, $attributeKey, $attribute['is_top'], $attribute['attribute_group'],
                        $attribute['is_required'], $attribute['is_quick_search_filter'], $attribute['quick_search_order']);
                }
            }
            /** @var Router $router */
            $router = Oforge()->App()->getContainer()->get('router');
            $uri    = $router->pathFor('backend_insertion_type');

            return $response->withRedirect($uri, 302);
        }

        if (isset($request->getQueryParams()['id'])) {
            $attribute = $insertionTypeService->getInsertionTypeById($insertionTypeId);
            Oforge()->View()->assign([
                'content' => [
                    'insertionType' => $attribute->toArray(3),
                ],
            ]);
        }

        $attributeList = [];
        foreach ($attributeService->getAttributeList() as $attribute) {
            $attributeList[] = $attribute->toArray(1);
        }

        $insertionTypeList = [];
        foreach ($insertionTypeService->getInsertionTypeList(null, null) as $insertionType) {
            $insertionTypeList[] = $insertionType->toArray(1);
        }

        $attributeGroupList = [];
        foreach ($insertionTypeService->getAttributeGroupList() as $group) {
            $attributeGroupList[] = $group->toArray(1);
        }

        Oforge()->View()->assign([
            'content' => [
                'attributeList'      => $attributeList,
                'insertionTypeList'  => $insertionTypeList,
                'attributeGroupList' => $attributeGroupList,
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ServiceNotFoundException
     */
    public function deleteAction(Request $request, Response $response) {
        /** @var InsertionTypeService $insertionTypeService */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');
        $insertionTypeId      = $request->getQueryParams()['id'];

        if (isset($request->getQueryParams()['id']) && $insertionTypeId != null && $request->isPost()) {
            try {
                $insertionTypeService->deleteInsertionType($insertionTypeId);
            } catch (\Exception $exception) {
                Oforge()->View()->Flash()->addMessage('error', I18N::translate('backend_insertion_type_delete_failed', $exception));
            }
        }
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $uri    = $router->pathFor('backend_insertion_type');

        return $response->withRedirect($uri, 302);
    }

    public function initPermissions() {
        parent::initPermissions();
    }
}
