<?php

namespace Faq\Controller\Frontend;

use Faq\Models\FaqModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;
use Slim\Http\Response;
use Slim\Http\Request;

/**
 * Class AccountController
 *
 * @package FrontendUserManagement\Controller\Frontend
 * @EndpointClass(path="/account/faq", name="frontend_account_faq", assetScope="Frontend")
 */
class FrontendFaqController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @EndpointAction()
     *
     * @throws ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        /** @var GenericCrudService $crud */
        $crud = Oforge()->Services()->get('crud');
        $faq = [];

        foreach ($crud->list(FaqModel::class) as $value) {
            $faq[] = $value->toArray();
        }

        Oforge()->View()->assign(['faq' => $faq]);
    }
}