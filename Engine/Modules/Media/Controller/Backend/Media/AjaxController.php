<?php

namespace Oforge\Engine\Modules\Media\Controller\Backend\Media;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Media\Services\MediaService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class MediaController
 *
 * @package Oforge\Engine\Modules\Media\Controller\Backend;
 * @EndpointClass(path="/backend/media/ajax", name="backend_media_ajax", assetScope="Backend")
 */
class AjaxController extends SecureBackendController {

    public function initPermissions() {
        $this->ensurePermissions([
            'indexAction',
            'demoAction',
            'uploadAction',
            'searchAction',
            'deleteAction',
        ], BackendUser::ROLE_LOGGED_IN);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /** @var MediaService $service */
        $service = Oforge()->Services()->get('media');

        Oforge()->View()->assign(['media' => $service->search('')]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response|void
     * @EndpointAction(path="/demo")
     */
    public function demoAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @EndpointAction(path="/upload")
     */
    public function uploadAction(Request $request, Response $response)
    {
        if (isset($_FILES['upload-media'])) {
            /** @var MediaService $service */
            $service = Oforge()->Services()->get('media');
            $created = $service->add($_FILES['upload-media']);
            Oforge()->View()->assign(
                [
                    'json' => [
                        'created' => $created !== null ? $created->toArray() : false,
                    ]
                ]
            );
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response|void
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/search")
     */
    public function searchAction(Request $request, Response $response) {
        /** @var MediaService $service */
        $service = Oforge()->Services()->get('media');

        $query = ArrayHelper::get($_GET, 'query', '');
        $page  = ArrayHelper::get($_GET, 'page', 1);

        Oforge()->View()->assign(['media' => $service->search($query, $page)]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response|void
     * @EndpointAction(path="/delete/{id}")
     */
    public function deleteAction(Request $request, Response $response) {
    }

}
