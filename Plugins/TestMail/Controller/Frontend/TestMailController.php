<?php

namespace TestMail\Controller\Frontend;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;


class TestMailController extends AbstractController {
    /**
     * Returns rendered Mail to the browser for quick access/testing
     * To display mail, go to: /testmail?template=<template-name>
     * @param Request $request
     * @param Response $response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {

        $testOptions = [
            "to"         => [ ],
            "cc"         => [ ],
            "bcc"        => [ ],
            "replyTo"    => [ ],
            "subject"    => "",
            "html"      => true,
            "attachment" => [ ],
            "template" => $request->getQueryParam('template')
        ];

        $mailservice = Oforge()->Services()->get('mail');
        $data = ['mail'=> $testOptions, 'data'=> []];
        $mail = $mailservice->renderMail($data);
        echo $mail;
        exit();
    }
}