<?php
/**
 * Created by PhpStorm.
 * User: Obs
 * Date: 23.11.2018
 * Time: 09:56
 */

namespace Test\Controller\Frontend;

use \Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class MailController
 * @package Test\Controller\Frontend
 */
class MailController extends AbstractController {
    public function mailAction( Request $request, Response $response ) {

        $testOptions = [
            "to"         => [ ],
            "cc"         => [ ],
            "bcc"        => [ ],
            "replyTo"    => [ ],
            "subject"    => "Some title",
            "html"      => true,
            "body"       => "<html><body>TestMail</body></html>",
            "attachment" => [ ],
        ];

        /**
         * @var $mailService MailService
         */
        // $mailService = Oforge()->Services()->get('mail.render')
        // $contentPartService = Oforge()->Services()->get('content.parts');
        // $htmlContent = $mailerService->render("password_forget", ["text" => $contentPartService->get("password", "de"), "user" => ""]);

        /**
         * @var $mailService MailService
         */
        $mailService = Oforge()->Services()->get('mail');
        $mailService->send($testOptions);
    }
}