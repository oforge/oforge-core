<?php

namespace TestMail\Controller\Frontend;

use Exception;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class TestMailController
 *
 * @package TestMail\Controller\Frontend
 * @EndpointClass(path="/testmail", name="testmail", assetScope="Frontend")
 */
class TestMailController extends AbstractController {
    /**
     * Returns rendered HTML for convenient testing.
     * To display mail, request -> <base-path>/testmail?template=<template-name>
     *
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        $testOptions = [
            'to'         => [],
            'cc'         => [],
            'bcc'        => [],
            'replyTo'    => [],
            'subject'    => '',
            'attachment' => [],
            'template'   => $request->getQueryParam('template'),
        ];
        if (!$testOptions['template']) {
            echo "Please specify query: '?template= ...' in the url";
            die();
        }
        /** @var MailService $mailservice */
        $mailservice = Oforge()->Services()->get('mail');
        try {
            $mail = $mailservice->renderMail($testOptions, $templateData = []);
        } catch (Exception $e) {
            echo $e;
            die();
        }

        echo $mail;
        exit();
    }

}
