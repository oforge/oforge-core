<?php

namespace Oforge\Engine\Modules\Media\Controller\Frontend\Media;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Controller\SecureController;
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
 * @EndpointClass(path="/frontend/media", name="backend_media", assetScope="Backend")
 */
class MediaController extends SecureController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws NonUniqueResultException
     * @throws ORMException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        if($request->isPost()) {

        }
    }
}
