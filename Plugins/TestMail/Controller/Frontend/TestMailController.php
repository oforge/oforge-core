<?php

namespace TestMail\Controller\Frontend;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;


class TestMailController extends AbstractController {
    /**
     * Returns rendered HTML for convenient testing.
     * To display mail, request -> <base-path>/testmail?template=<template-name>
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
        try {
            $mail = $mailservice->renderMail($testOptions, $templateData = []);
        }
        catch(\Exception $e) {
            echo $e;
            die();
        }

        echo $mail;
        exit();
    }
}