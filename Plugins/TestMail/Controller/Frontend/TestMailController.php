<?php

namespace TestMail\Controller\Frontend;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;


class TestMailController extends AbstractController {
    /**
     * Returns rendered Mail to the browser for quick access/testing
     * @param Request $request
     * @param Response $response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        $testOptions = [
            "to"         => ['test@test.de', 'test2@test.com'],
            "cc"         => [ ],
            "bcc"        => [ ],
            "replyTo"    => [ ],
            "subject"    => "",
            "html"      => true,
            "attachment" => [ ],
            "template" => "Test.twig",
        ];

        $mailservice = Oforge()->Services()->get('mail');
        $data = ['mail'=> $testOptions, 'data'=> ['activationLink' => 'blablub']];
        $mail = $mailservice->renderMail($data);
        echo $mail;
        exit();
    }
}