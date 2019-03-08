<?php

namespace Oforge\Engine\Modules\Core\Controller\Frontend;

use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

class NotFoundController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
        return $response->withStatus(404);
    }
}