<?php

namespace ReportErrorForm;

use Helpdesk\Models\IssueTypeGroup;
use Helpdesk\Models\IssueTypes;
use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class Bootstrap
 *
 * @package ProductPlacement
 */
class Bootstrap extends AbstractBootstrap {
    public const HELPDESK_ISSUE_TYPE_GROUP = 'ReportErrorForm';

    public function __construct() {
        $this->endpoints = [
            Controller\FormController::class,
        ];

        $this->dependencies = [
            \Helpdesk\Bootstrap::class,
        ];
    }

    public function install() {
        parent::install();

        /** @var GenericCrudService $crudService */
        $crudService = Oforge()->Services()->get('crud');
        try {
            /** @var HelpdeskTicketService $helpdeskTicketService */
            $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');

            /** @var IssueTypeGroup|null $issueGroup */
            $issueGroup = $crudService->getOneBy(IssueTypeGroup::class, ['issueTypeGroupName' => self::HELPDESK_ISSUE_TYPE_GROUP]);
            if ($issueGroup === null) {
                $issueGroup = $crudService->create(IssueTypeGroup::class, ['issueTypeGroupName' => self::HELPDESK_ISSUE_TYPE_GROUP]);
            }
            $issueTypes = [
                'style_error' => [
                    'en' => 'Display error',
                    'de' => 'Darstellungsfehler',
                ],
                'technical_error' => [
                    'en' => 'Technical error',
                    'de' => 'Technischer Fehler',
                ],
                'content_error' => [
                    'en' => 'Content error',
                    'de' => 'Inhaltlicher Fehler',
                ],
                'missing_function' => [
                    'en' => 'Missing function',
                    'de' => 'Fehlende Funktion',
                ],
                'privacy_error' => [
                    'en' => 'Privacy error',
                    'de' => 'Fehler beim Datenschutz',
                ],
            ];
            foreach ($issueTypes as $issueTypeKey => $i18nDefaults) {
                I18N::translate('report_error_issueType_' . $issueTypeKey, $i18nDefaults);
                /** @var IssueTypes|null $issueType */
                $criteria  = ['issueTypeName' => $issueTypeKey, 'issueTypeGroup' => $issueGroup];
                $issueType = $crudService->getOneBy(IssueTypes::class, $criteria);
                if ($issueType === null) {
                    $crudService->create(IssueTypes::class, $criteria);
                }
            }
        } catch (ServiceNotFoundException $exception) {
        }
    }

    public function uninstall(bool $keepData) {
        parent::uninstall($keepData);
        // TODO uninstalling of issue types & group
    }

}
