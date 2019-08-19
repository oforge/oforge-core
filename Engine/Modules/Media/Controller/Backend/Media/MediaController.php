<?php

namespace Oforge\Engine\Modules\Media\Controller\Backend\Media;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\FileSystemHelper;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterComparator;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;
use Oforge\Engine\Modules\Media\Models\Media;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class MediaController
 *
 * @package Oforge\Engine\Modules\Media\Controller\Backend\Media
 * @EndpointClass(path="/backend/media", name="backend_media", assetScope="Backend")
 */
class MediaController extends SecureBackendController {
    /** @var array $indexReservedQueryKeys */
    private $indexReservedQueryKeys = [
        'orderBy'         => 'orderBy',
        'order'           => 'order',
        'page'            => 'page',
        'entitiesPerPage' => 'entitiesPerPage',
    ];
    /** @var array $indexPagination */
    private $indexPagination = [
        'default' => 10,
        'buttons' => [10, 25, 50, 100, 250],
    ];
    /** @var array $indexFilter */
    private $indexFilter = [
        'name' => [
            'type'    => CrudFilterType::TEXT,
            'label'   => [
                'key'     => 'module_media_search_in_name',
                'default' => [
                    'en' => 'Search in name',
                    'de' => 'Suche im Namen',
                ],
            ],
            'compare' => CrudFilterComparator::LIKE,
        ],
    ];
    /** @var GenericCrudService crudService */
    private $crudService;

    /**
     * MediaController constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        $this->crudService = Oforge()->Services()->get('crud');
    }

    public function initPermissions() {
        $this->ensurePermissions([
            'indexAction',
            'replaceAction',
        ], BackendUser::ROLE_MODERATOR);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws NonUniqueResultException
     * @throws ORMException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        $queryParams = $request->getQueryParams();

        $pagination = $this->prepareIndexPaginationData($queryParams);
        $criteria   = $this->evaluateIndexFilter($queryParams);
        /** @var Media[] $entities */
        $entities = $this->crudService->list(Media::class, $criteria, null, $pagination['offset'], $pagination['limit']);
        foreach ($entities as $index => $entity) {
            $entities[$index] = $entity->toArray(0);
        }
        Oforge()->View()->assign([
            'media' => [
                'items' => $entities,
            ],
            'crud'  => [
                'filter'     => $this->indexFilter,
                'pagination' => $pagination,
                'queryKeys'  => $this->indexReservedQueryKeys,
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/replace/{id}")
     */
    public function replaceAction(Request $request, Response $response, array $args) {
        $mediaID = $args['id'];
        /** @var Media|null $media */
        $media = $this->crudService->getById(Media::class, $mediaID);
        if ($media !== null) {
            $postData = $request->getParsedBody();
            if ($request->isPost() && !empty($postData)) {
                $media->setName(ArrayHelper::get($postData, 'name', $media->getName()));
                if (isset($_FILES['file']) && !empty($_FILES['file'])) {
                    $fileData = $_FILES['file'];
                    if (isset($fileData['error']) && $fileData['error'] == 0 && isset($fileData['size']) && $fileData['size'] > 0) {
                        $filename         = urlencode(basename($fileData['name']));
                        $relativeFilePath = Statics::IMAGES_DIR . DIRECTORY_SEPARATOR . substr(md5(rand()), 0, 2) . DIRECTORY_SEPARATOR . substr(md5(rand()), 0,
                                2) . DIRECTORY_SEPARATOR . $filename;
                        FileSystemHelper::mkdir(dirname(ROOT_PATH . $relativeFilePath));
                        if (move_uploaded_file($fileData['tmp_name'], ROOT_PATH . $relativeFilePath)) {
                            $media->setType($fileData['type'])->setPath(str_replace('\\', '/', $relativeFilePath));
                            Oforge()->DB()->getForgeEntityManager()->update($media);
                        }
                    }
                }

                return RouteHelper::redirect($response, 'backend_media');
            }
        }
        Oforge()->View()->assign([
            'media' => [
                'item' => $media === null ? null : $media->toArray(0),
            ],
        ]);
    }

    /**
     * Evaluates query filter params.
     *
     * @param array $queryParams
     *
     * @return array
     */
    protected function evaluateIndexFilter(array $queryParams) : array {
        $queryKeys               = $this->indexReservedQueryKeys;
        $queryKeyPage            = $queryKeys['page'];
        $queryKeyEntitiesPerPage = $queryKeys['entitiesPerPage'];
        unset($queryParams[$queryKeyPage], $queryParams[$queryKeyEntitiesPerPage]);

        $filters = [];

        if (!empty($this->indexFilter)) {
            foreach ($this->indexFilter as $propertyName => $filterConfig) {
                if (isset($queryParams[$propertyName]) && $queryParams[$propertyName] !== '') {
                    $propertyNameValue = $queryParams[$propertyName];
                    switch ($filterConfig['type']) {
                        case CrudFilterType::SELECT:
                            $comparator = CrudFilterComparator::EQUALS;
                            break;
                        case CrudFilterType::TEXT:
                        case CrudFilterType::HIDDEN:
                            $comparator = ArrayHelper::get($filterConfig, 'compare', CrudFilterComparator::EQUALS);
                            break;
                        default:
                            continue 2;
                    }
                    switch ($comparator) {
                        case CrudFilterComparator::EQUALS:
                        case CrudFilterComparator::NOT_EQUALS:
                        case CrudFilterComparator::LIKE:
                        case CrudFilterComparator::NOT_LIKE:
                        case CrudFilterComparator::GREATER:
                        case CrudFilterComparator::GREATER_EQUALS:
                        case CrudFilterComparator::LESS:
                        case CrudFilterComparator::LESS_EQUALS:
                            break;
                        default:
                            $comparator = CrudFilterComparator::EQUALS;
                    }
                    $filters[$propertyName] = [
                        'comparator' => $comparator,
                        'value'      => $propertyNameValue,
                    ];
                }
            }
        }

        return $filters;
    }

    /**
     * Prepare data for index pagination.
     *
     * @param array $queryParams
     *
     * @return  array
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    protected function prepareIndexPaginationData(array $queryParams) : array {
        $queryKeys = $this->indexReservedQueryKeys;;
        $queryKeyPage            = $queryKeys['page'];
        $queryKeyEntitiesPerPage = $queryKeys['entitiesPerPage'];

        $itemsCount       = $this->crudService->count(Media::class);
        $offset           = null;
        $entitiesPerPage  = null;
        $buttons          = null;
        $paginatorCurrent = null;
        $paginatorMax     = null;

        if (isset($this->indexPagination)) {
            $buttons = $this->indexPagination['buttons'];
            if (isset($queryParams[$queryKeyEntitiesPerPage])) {
                $entitiesPerPage = $queryParams[$queryKeyEntitiesPerPage];
            } else {
                $entitiesPerPage = $this->indexPagination['default'];
            }
            $paginatorMax = ceil($itemsCount / $entitiesPerPage);
            if (isset($queryParams[$queryKeyPage])) {
                $paginatorCurrent = $queryParams[$queryKeyPage];
            } else {
                $paginatorCurrent = 1;
            }
            if ($paginatorCurrent > 1) {
                $offset = ($paginatorCurrent - 1) * $entitiesPerPage;
            }
        }

        return [
            'offset'  => $offset,
            'limit'   => $entitiesPerPage,
            'total'   => $itemsCount,
            'page'    => [
                'current' => $paginatorCurrent,
                'max'     => $paginatorMax,
            ],
            'buttons' => [
                'values'  => $buttons,
                'current' => $entitiesPerPage,
            ],
        ];
    }

}
