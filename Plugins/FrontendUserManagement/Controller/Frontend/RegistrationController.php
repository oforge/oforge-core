<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 11.02.2019
 * Time: 10:05
 */

namespace FrontendUserManagement\Controller\Frontend;

use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

class RegistrationController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
        // show registration form and button for "forgot password"
    }
    
    public function processAction(Request $request, Response $response) {
    
    }
}