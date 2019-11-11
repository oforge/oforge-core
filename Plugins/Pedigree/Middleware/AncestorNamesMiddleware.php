<?php

namespace Pedigree\Middleware;

use Pedigree\Services\PedigreeService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;

class AncestorNamesMiddleware {
    public function prepend(Request $request, Response $response) {

        $typeId = $this->getTypeId($request);
        $insertion = [];
        if (isset($_SESSION['insertion' . $typeId])) {
            $insertion = $_SESSION['insertion' . $typeId]['insertion'];
        } else if (isset($request->getParsedBody()['insertion'])){
            $insertion = $request->getParsedBody()['insertion'];
        }

        if(sizeof($insertion) > 0) {
            /** @var PedigreeService $pedigreeService */
            $pedigreeService = Oforge()->Services()->get('pedigree');
            $ancestors = $pedigreeService->getAncestorsFromInsertion($insertion);
            $pedigreeService->addAncestors($ancestors);
        }
    }

        public function getTypeId (Request $request) {
        /** @var Route $route */
        $route      = $request->getAttribute('route');
        $arguments  = $route->getArguments();
        $typeId     = $arguments ['type'];
        return $typeId;
    }
}
