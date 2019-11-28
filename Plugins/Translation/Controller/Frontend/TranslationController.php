<?php


namespace Translation\Controller\Frontend;


use FrontendUserManagement\Abstracts\SecureFrontendController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Translation\Services\TranslationService;

/**
 * Class TranslationController
 *
 * @package Translation\Controller\Frontend
 * @EndpointClass(path="/translate", name="frontend_translation", assetScope="Frontend")
 */
class TranslationController extends SecureFrontendController
{


    /**
     * @param Request $request
     * @param Response $response
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response){
        /** @var TranslationService $translationService */
        $translationService = Oforge()->Services()->get('translation');
        $translationService->translateFrom('de', 'Auf der Mauer sitzt eine kleine Wanze.', ['en', 'pl']);
    }
}
