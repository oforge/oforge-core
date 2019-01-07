<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:57
 */

namespace Oforge\Engine\Modules\CMS\Controller\Frontend;

use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

class PageController extends AbstractController {
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function indexAction(Request $request, Response $response) {
        /* TODO:
           - add pageservice and models ([page] id, name, url, {language}) ([contentblock] id, name, type, content), ([pagecontent] id, pageid, contentid)
           - add page crud
           - add content crud
        */
    }
}