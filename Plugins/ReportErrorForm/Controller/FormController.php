<?php

namespace ReportErrorForm\Controller;

use Helpdesk\Models\IssueTypeGroup;
use Helpdesk\Models\IssueTypes;
use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Services\TokenService;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use ReportErrorForm\Bootstrap;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class FormController
 *
 * @package ReportErrorForm\Controller
 * @EndpointClass(path="/report-error", name="frontend_report_error", assetBundles="Frontend")
 */
class FormController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) : Response {
        $twigFlash = Oforge()->View()->Flash();

        $issueTypesMap  = [];
        $issueTypeIDMap = [];
        /** @var GenericCrudService $crudService */
        $crudService = Oforge()->Services()->get('crud');
        /** @var IssueTypeGroup|null $issueGroup */
        $issueGroup = $crudService->getRepository(IssueTypeGroup::class)->findBy(['issueTypeGroupName' => Bootstrap::HELPDESK_ISSUE_TYPE_GROUP]);
        if ($issueGroup !== null) {
            /** @var IssueTypes[] $issueTypes */
            $issueTypes = $crudService->getRepository(IssueTypes::class)->findBy(['issueTypeGroup' => $issueGroup]);
            foreach ($issueTypes as $issueType) {
                $issueTypesMap[$issueType->getIssueTypeName()]  = I18N::translate('report_error_issueType_' . $issueType->getIssueTypeName());
                $issueTypeIDMap[$issueType->getIssueTypeName()] = $issueType->getId();
            }
        }

        if ($request->isPost() && !empty(($postData = $request->getParsedBody()))) {
            /** @var TokenService $tokenService */
            $tokenService = Oforge()->Services()->get('token');
            if (!$tokenService->isValid(ArrayHelper::get($postData, 'token'), 'report_error_form')) {
                $tokenService->clear('report_error_form');

                $twigFlash->addMessage('warning', I18N::translate('form_invalid_token', [
                    'en' => 'The data has been sent from an invalid form.',
                    'de' => 'Die Daten wurden von einem ungültigen Formular gesendet.',
                ]));
                $twigFlash->setData(__METHOD__, $postData);

                return RouteHelper::redirect($response, 'frontend_report_error');
            }
            $tokenService->clear('report_error_form');

            $issueType      = trim(ArrayHelper::get($postData, 'issueType', ''));
            $issueTypeLabel = ArrayHelper::get($issueTypesMap, $issueType, $issueType);
            if (!isset($issueTypeIDMap[$issueType])) {
                $twigFlash->addMessage('warning', I18N::translate('form_invalid_token', [
                    'en' => 'The data has been sent from an invalid form.',
                    'de' => 'Die Daten wurden von einem ungültigen Formular gesendet.',
                ]));
                $twigFlash->setData(__METHOD__, $postData);

                return RouteHelper::redirect($response, 'frontend_report_error');
            }

            $message = trim(ArrayHelper::get($postData, 'message', ''));
            if (empty($message)) {
                $twigFlash->addMessage('error', I18N::translate('report_error_message_missing_data', [
                    'en' => 'You have to fill out all required fields!',
                    'de' => 'Sie müssen alle erforderlichen Felder ausfüllen!',
                ]));
                $twigFlash->setData(__METHOD__, $postData);

                return RouteHelper::redirect($response, 'frontend_report_error');
            }

            if (Oforge()->View()->has('current_user')) {
                $user    = Oforge()->View()->get('current_user');
                $message .= "\r\n\r\n" . I18N::translate('report_error_form_sender_user', [
                        'en' => 'User',
                        'de' => 'Benutzer',
                    ]) . ': ' . $user['email'];
                $opener  = $user['id'];
            } else {
                $from = trim(ArrayHelper::get($postData, 'from', ''));
                if (empty($from)) {
                    $from = I18N::translate('anonymous', [
                        'en' => 'anonymous',
                        'de' => 'anonym',
                    ]);
                }
                $message .= "\r\n\r\n" . I18N::translate('report_error_form_sender', [
                        'en' => 'Sender',
                        'de' => 'Absender',
                    ]) . ': ' . $from;
                $opener  = '0';
            }

            $receiver = Oforge()->Settings()->get('Plugins.ReportErrorForm', []);
            if (empty($receiver)) {
                $receiverFallback = Oforge()->Settings()->get('error_mail_report.mailer_settings.receiver_address');
                if (!empty($receiverFallback)) {
                    $receiver = [$receiverFallback => $receiverFallback];
                }
            }
            if (!empty($receiver)) {
                $subject = I18N::translate('error_mail_issueType_prefix', [
                    'en' => 'Report a bug form: ',
                    'de' => 'Fehler melden Formular: ',
                ]);
                $subject .= $issueTypeLabel;
                /** @var MailService $mailService */
                $mailService = Oforge()->Services()->get('mail');
                $mailOptions = [
                    'from'    => 'no_reply',
                    'to'      => $receiver,
                    'subject' => $subject,
                    'text'    => $message,
                ];
                $mailService->send($mailOptions);
            }
            /** @var HelpdeskTicketService $helpdeskTicketService */
            $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');
            $helpdeskTicketService->createNewTicket($opener, $issueTypeIDMap[$issueType], $issueTypeLabel, $message);

            $twigFlash->addMessage('success', I18N::translate('report_error_message_success', [
                'en' => 'Thank you for your report.',
                'de' => 'Vielen Dank für Ihren Bericht.',
            ]));

            return RouteHelper::redirect($response, 'frontend_report_error');
        }
        if ($twigFlash->hasData(__METHOD__)) {
            Oforge()->View()->assign(['reportErrorForm.postData' => $twigFlash->getData(__METHOD__)]);
            $twigFlash->clearData(__METHOD__);
        }
        Oforge()->View()->assign(['reportErrorForm.issueTypes' => $issueTypesMap]);

        return $response;
    }

}
