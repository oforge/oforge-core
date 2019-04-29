<?php
/**
 * Created by PhpStorm.
 * User: Obs
 * Date: 23.11.2018
 * Time: 09:56
 */

namespace Test\Controller\Frontend;

use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use PHPMailer\PHPMailer\Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Class MailController
 *
 * @package Test\Controller\Frontend
 * @EndpointClass(path="/test/mail", name="frontend_test_mail", assetScope="Frontend")
 */
class MailController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ConfigElementNotFoundException
     * @throws ConfigOptionKeyNotExists
     * @throws ServiceNotFoundException
     * @throws Exception
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @EndpointAction()
     */
    public function testAction(Request $request, Response $response) {
        $testOptions = [
            'to'         => [],
            'cc'         => [],
            'bcc'        => [],
            'replyTo'    => [],
            'subject'    => 'Some title',
            'html'       => true,
            'body'       => '<html><body>TestMail</body></html>',
            'attachment' => [],
        ];

        /** @var MailService $mailService */
        // $mailService = Oforge()->Services()->get('mail.render')
        // $contentPartService = Oforge()->Services()->get('content.parts');
        // $htmlContent = $mailerService->render('password_forget', ['text' => $contentPartService->get('password', 'de'), 'user' => '']);

        /** @var MailService $mailService */
        $mailService = Oforge()->Services()->get('mail');
        $mailService->send($testOptions);
    }

}
