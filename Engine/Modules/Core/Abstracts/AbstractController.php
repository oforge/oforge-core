<?php
namespace Oforge\Engine\Modules\Core\Abstracts;

class AbstractController {
    protected function json($request, $response, $data) {
        return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->withJson($data);
    }
}
