<?php

namespace Oforge\Engine\Modules\Media\Controller\Backend;

use Exception;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\CouldNotActivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\CouldNotDeactivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\CouldNotInstallPluginException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginAlreadyActivatedException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginAlreadyInstalledException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginNotActivatedException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginNotInstalledException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RedirectHelper;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\Core\Services\PluginStateService;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Media\Services\MediaService;
use Oforge\Engine\Modules\TemplateEngine\Core\Exceptions\InvalidScssVariableException;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateManagementService;
use Oforge\Engine\Modules\TemplateEngine\Core\Twig\TwigFlash;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class MediaController
 *
 * @package Oforge\Engine\Modules\Media\Controller\Backend;
 * @EndpointClass(path="/backend/media", name="backend_media", assetScope="Backend")
 */
class MediaController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response|void
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /**
         * @var $service MediaService
         */
        $service = Oforge()->Services()->get("media");

        Oforge()->View()->assign(["media" => $service->search("")]);
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
     * @return Response|void
     * @EndpointAction(path="/upload")
     */
    public function uploadAction(Request $request, Response $response) {
        if (isset($_FILES["upload-media"])) {
            /**
             * @var $service MediaService
             */
            $service = Oforge()->Services()->get("media");
            $created = $service->add($_FILES["upload-media"]);
            Oforge()->View()->assign(["created" => $created != null ? $created->toArray() : false]);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response|void
     * @EndpointAction(path="/search")
     */
    public function searchAction(Request $request, Response $response) {
        /**
         * @var $service MediaService
         */
        $service = Oforge()->Services()->get("media");

        $query = isset($_GET["query"]) ? $_GET["query"] : "";
        $page  = isset($_GET["page"]) ? $_GET["page"] : 1;

        Oforge()->View()->assign(["media" => $service->search($query, $page)]);
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

    /**
     * @inheritdoc
     */
    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions('createAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions('searchAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions('deleteAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
    }

}
