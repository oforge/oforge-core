<?php

namespace Insertion\Controller\Backend;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMException as ORMExceptionAlias;
use Insertion\Enum\AttributeType;
use Insertion\Models\AttributeKey;
use Insertion\Models\AttributeValue;
use Insertion\Services\AttributeService;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
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
 * @EndpointClass(path="/backend/insertion/attribute", name="backend_insertion_attribute", assetScope="Backend")
 */
class BackendAttributeController extends SecureBackendController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @throws ORMExceptionAlias
     */
    public function indexAction(Request $request, Response $response) {
        /** @var AttributeService $attributeService */
        $attributeService = Oforge()->Services()->get('insertion.attribute');
        $count            = $attributeService->getAttributeCount();
        $limit            = 20;
        $offset           = 0;
        $page             = 1;
        $pageCount        = $count > 0 ? ceil($count / $limit) : 1;
        $data             = [];

        if (isset($request->getQueryParams()['page']) && is_numeric($request->getQueryParams()['page'])) {
            $offset = $limit * ($request->getQueryParams()['page']-1);
            $page = $request->getQueryParams()['page'];
        }

        $attributes = $attributeService->getAttributeList($limit, $offset);

        foreach ($attributes as $attribute) {
            $data[] = $attribute->toArray();
        }

        Oforge()->View()->assign([
            'content'     => [
                'attributes' => $data,
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
        $attributeKeyId   = $request->getQueryParams()['id'];

        if ($request->isPost()) {
            $body           = $request->getParsedBody();
            $body['values'] = json_decode($body['values'], true);
            if (isset($request->getQueryParams()['id'])) {
                /** @var AttributeKey $attributeKey */
                $attributeKey = $attributeService->updateAttributeKey($attributeKeyId, $body['name'], $body['type'], $body['filterType']);
                /** @var AttributeValue[] $attributeValues */
                $attributeValues = $attributeKey->getValues();
                $idList = [];
                foreach ($attributeValues as $value) {
                    $idList[] = $value->getId();
                }

                foreach ($body['values'] as $value) {
                    if (isset($value['id'])) {
                        if ($value['sub_attribute'] === 0) {
                            $attributeService->updateAttributeValue($value['id'], $value['value']);
                        } else {
                            $subAttribute = $attributeService->getAttribute($value['sub_attribute']);
                            $attributeService->updateAttributeValue($value['id'], $value['value'], $subAttribute);
                        }
                        $idList = array_diff($idList, [$value['id']]);
                    } else {
                        if ($value['sub_attribute'] === 0) {
                            $attributeService->createNewAttributeValue($value['value'], $attributeKey);
                        } else {
                            $subAttribute = $attributeService->getAttribute($value['sub_attribute']);
                            $attributeService->createNewAttributeValue($value['value'], $attributeKey, $subAttribute);
                        }
                    }
                }

                foreach ($idList as $id) {
                    $attributeService->deleteAttributeValue($id);
                }
            } else {
                $attributeKey = $attributeService->createNewAttributeKey($body['name'], $body['type'], $body['filterType']);
                foreach ($body['values'] as $value) {
                    if ($value['sub_attribute'] === 0) {
                        $attributeService->createNewAttributeValue($value['value'], $attributeKey);
                    } else {
                        $subAttribute = $attributeService->getAttribute($value['sub_attribute']);
                        $attributeService->createNewAttributeValue($value['value'], $attributeKey, $subAttribute);
                    }
                }
            }
            /** @var Router $router */
            $router = Oforge()->App()->getContainer()->get('router');
            $uri    = $router->pathFor('backend_insertion_attribute');

            return $response->withRedirect($uri, 302);
        }

        if (isset($request->getQueryParams()['id'])) {
            $attribute = $attributeService->getAttribute($attributeKeyId);
            Oforge()->View()->assign([
                'content' => [
                    'attribute' => $attribute->toArray(3),
                ],
            ]);
        }

        $types = [
            AttributeType::BOOLEAN,
            AttributeType::CONTAINER,
            AttributeType::MULTI,
            AttributeType::NUMBER,
            AttributeType::RANGE,
            AttributeType::SINGLE,
            AttributeType::TEXT,
            AttributeType::DATE,
            AttributeType::DATEYEAR,
            AttributeType::DATEMONTH
        ];

        $attributeList = [];

        foreach ($attributeService->getAttributeList() as $attribute) {
            $attributeList[] = $attribute->toArray(1);
        }

        Oforge()->View()->assign([
            'content' => [
                'types'         => $types,
                'attributeList' => $attributeList,
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
        /** @var AttributeService $attributeService */
        $attributeService = Oforge()->Services()->get('insertion.attribute');
        $attributeKeyId   = $request->getQueryParams()['id'];

        if (isset($request->getQueryParams()['id']) && $attributeKeyId != null && $request->isPost()) {
            try {
                $attributeService->deleteAttributeKey($attributeKeyId);
            } catch (\Exception $exception) {
                Oforge()->View()->Flash()->addMessage('error',
                    I18N::translate('backend_insertion_delete_failed', 'Delete operation of AttributeKey failed. Please remove it from all InsertionsTypes'));
            }
        }
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $uri    = $router->pathFor('backend_insertion_attribute');

        return $response->withRedirect($uri, 302);

    }

    public function initPermissions() {
        $this->ensurePermissions([
            'indexAction',
            'deleteAction',
            'editAction',
        ], BackendUser::ROLE_MODERATOR);
    }
}
