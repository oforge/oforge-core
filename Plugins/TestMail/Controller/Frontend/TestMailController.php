<?php

namespace TestMail\Controller\Frontend;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;


class TestMailController extends AbstractController {
    /**
     * Returns rendered Mail to the browser for more convenient testing.
     * To display mail, go to: /testmail?template=<template-name>
     *
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
            "attachment" => [ ],
            "template" => $request->getQueryParam('template')
        ];
        if(!$testOptions['template']) {
            echo "Please specify query: '?template= ...' in the url";
            die();
        }
        $mailservice = Oforge()->Services()->get('mail');
        $data = ['mail'=> $testOptions, 'data'=> []];
        try {
            $mail = $mailservice->renderMail($data);
        }
        catch(\Exception $e) {
            echo $e;
            die();
        }

        echo $mail;
        exit();
    }
}