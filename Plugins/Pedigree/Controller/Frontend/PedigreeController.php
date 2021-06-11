<?php

namespace Pedigree\Controller\Frontend;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Pedigree\Models\Ancestor;
use Pedigree\Services\PedigreeService;
use Slim\Http\Request;
use Slim\Http\Response;
use \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;


/**
 * Class PedigreeController
 *
 * @package Pedigree\Controller\Frontend
 * @EndpointClass(path="/insertions/pedigree", name="insertion_pedigree", assetScope="Frontend")
 */
class PedigreeController {

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
    }


    /**
     * @param Request $request
     * @param Response $response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/all")
     */
    public function getAllAction(Request $request, Response $response) {
        /** @var PedigreeService $pedigreeService */
        $pedigreeService = Oforge()->Services()->get('pedigree');
        $names = $pedigreeService->getAllAncestors();
        $names = array_map(function(Ancestor $name) {return $name->getName();}, $names);
        Oforge()->View()->assign(['json' => $names]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/all_ancestors")
     */
    public function getAllAncestorsDataAction(Request $request, Response $response) {
        // Set path to CSV file
        $csvFile = 'var/public/__assets/Frontend/abstammung/abstammung.csv';

        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle) ) {
            $line_of_text[] = fgetcsv($file_handle, 1024, ";");
        }
        fclose($file_handle);
        //append csv-data to twig/json2
        Oforge()->View()->assign(['json2' => $line_of_text]);
    }
}
