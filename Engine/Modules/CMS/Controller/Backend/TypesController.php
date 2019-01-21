<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:57
 */

namespace Oforge\Engine\Modules\CMS\Controller\Backend;

use Oforge\Engine\Modules\CMS\Services\PageService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

class TypesController extends AbstractController {
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {

    }
}