<?php

namespace Insertion;

use FrontendUserManagement\Middleware\FrontendSecureMiddleware;
use FrontendUserManagement\Services\AccountNavigationService;
use Insertion\Commands\ReminderCommand;
use Insertion\Commands\SearchBookmarkCommand;
use Insertion\Controller\Backend\BackendAttributeController;
use Insertion\Controller\Backend\BackendInsertionController;
use Insertion\Controller\Backend\BackendInsertionFeedbackController;
use Insertion\Controller\Backend\BackendInsertionSeoContentController;
use Insertion\Controller\Backend\BackendInsertionTypeController;
use Insertion\Controller\Backend\BackendInsertionTypeGroupController;
use Insertion\Controller\Frontend\FrontendInsertionController;
use Insertion\Controller\Frontend\FrontendUsersInsertionController;
use Insertion\Cronjobs\Reminder14DaysCronjob;
use Insertion\Cronjobs\Reminder30DaysCronjob;
use Insertion\Cronjobs\Reminder3DaysCronjob;
use Insertion\Cronjobs\SearchBookmarkCronjob;
use Insertion\Middleware\InsertionDetailMiddleware;
use Insertion\Middleware\InsertionProfileProgressMiddleware;
use Insertion\Models\AttributeKey;
use Insertion\Models\AttributeValue;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionAttributeValue;
use Insertion\Models\InsertionContact;
use Insertion\Models\InsertionContent;
use Insertion\Models\InsertionFeedback;
use Insertion\Models\InsertionMedia;
use Insertion\Models\InsertionProfile;
use Insertion\Models\InsertionSeoContent;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Models\InsertionTypeGroup;
use Insertion\Models\InsertionUserBookmark;
use Insertion\Models\InsertionUserSearchBookmark;
use Insertion\Models\InsertionZipCoordinates;
use Insertion\Services\AttributeService;
use Insertion\Services\InsertionBookmarkService;
use Insertion\Services\InsertionCreatorService;
use Insertion\Services\InsertionFeedbackService;
use Insertion\Services\InsertionFormsService;
use Insertion\Services\InsertionListService;
use Insertion\Services\InsertionMockService;
use Insertion\Services\InsertionProfileService;
use Insertion\Services\InsertionSearchBookmarkService;
use Insertion\Services\InsertionSeoService;
use Insertion\Services\InsertionService;
use Insertion\Services\InsertionSliderService;
use Insertion\Services\InsertionTypeService;
use Insertion\Services\InsertionUpdaterService;
use Insertion\Services\InsertionUrlService;
use Insertion\Services\InsertionValidationService;
use Insertion\Services\InsertionZipService;
use Insertion\Twig\InsertionExtensions;
use Oforge\Engine\Modules\AdminBackend\Core\Enums\DashboardWidgetPosition;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateRenderService;

class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            FrontendInsertionController::class,
            FrontendUsersInsertionController::class,
            Controller\Frontend\InsertionProvidersController::class,
            BackendAttributeController::class,
            BackendInsertionController::class,
            BackendInsertionTypeController::class,
            BackendInsertionTypeGroupController::class,
            BackendInsertionFeedbackController::class,
            BackendInsertionSeoContentController::class,
        ];

        $this->middlewares = [
            'frontend_account_dashboard' => [
                'class'    => InsertionProfileProgressMiddleware::class,
                'position' => 1,
            ],
        ];

        $this->services = [
            'insertion'                 => InsertionService::class,
            'insertion.type'            => InsertionTypeService::class,
            'insertion.attribute'       => AttributeService::class,
            'insertion.mock'            => InsertionMockService::class,
            'insertion.creator'         => InsertionCreatorService::class,
            'insertion.updater'         => InsertionUpdaterService::class,
            'insertion.forms'           => InsertionFormsService::class,
            'insertion.feedback'        => InsertionFeedbackService::class,
            'insertion.list'            => InsertionListService::class,
            'insertion.bookmark'        => InsertionBookmarkService::class,
            'insertion.search.bookmark' => InsertionSearchBookmarkService::class,
            'insertion.profile'         => InsertionProfileService::class,
            'insertion.slider'          => InsertionSliderService::class,
            'insertion.zip'             => InsertionZipService::class,
            'insertion.seo'             => InsertionSeoService::class,
            'insertion.validation'      => InsertionValidationService::class,
        ];

        $this->models = [
            AttributeKey::class,
            AttributeValue::class,
            Insertion::class,
            InsertionAttributeValue::class,
            InsertionContact::class,
            InsertionContent::class,
            InsertionFeedback::class,
            InsertionMedia::class,
            InsertionType::class,
            InsertionTypeAttribute::class,
            InsertionTypeGroup::class,
            InsertionUserBookmark::class,
            InsertionUserSearchBookmark::class,
            InsertionProfile::class,
            InsertionZipCoordinates::class,
            InsertionSeoContent::class,
        ];

        $this->dependencies = [
            \CMS\Bootstrap::class,
            \FrontendUserManagement\Bootstrap::class,
            \ImageUpload\Bootstrap::class,
            \Messenger\Bootstrap::class,
            \Helpdesk\Bootstrap::class,
            \VideoUpload\Bootstrap::class,
            \Seo\Bootstrap::class,
        ];

        $this->commands = [
            ReminderCommand::class,
            SearchBookmarkCommand::class,
        ];

        $this->cronjobs = [
            Reminder3DaysCronjob::class,
            Reminder14DaysCronjob::class,
            Reminder30DaysCronjob::class,
            SearchBookmarkCronjob::class,
        ];

        $this->middlewares = [
            'insertions_feedback' => [
                'class'    => FrontendSecureMiddleware::class,
                'position' => 1,
            ],
            'insertions_contact'  => [
                'class'    => FrontendSecureMiddleware::class,
                'position' => 1,
            ],
        ];
    }

    public function load() {
        /**
         * @var $templateRenderer TemplateRenderService
         */
        $templateRenderer = Oforge()->Services()->get("template.render");

        $templateRenderer->View()->addExtension(new InsertionExtensions());

        if (Oforge()->isAppReady()) {
            Oforge()->App()->add(new InsertionDetailMiddleware());
        }

        $urlService = Oforge()->Services()->get("url");

        $seoUrlService = new InsertionUrlService($urlService);
        Oforge()->Services()->set("url", $seoUrlService);
    }

    public function install() {
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->install([
            'name'     => 'plugin_insertion_count',
            'template' => 'InsertionCount',
            'handler'  => Widgets\InsertionCountWidget::class,
            'label'    => [
                'en' => 'Number of insertions',
                'de' => 'Anzahl Inserate',
            ],
            'position' => DashboardWidgetPosition::TOP,
            'cssClass' => 'bg-purple',
        ]);
        $dashboardWidgetsService->install([
            'name'     => 'plugin_insertion_moderation',
            'template' => 'InsertionModeration',
            'handler'  => Widgets\InsertionModerationWidget::class,
            'label'    => [
                'en' => 'Insertions require moderation',
                'de' => 'Inserate benötigen Moderation',
            ],
            'position' => DashboardWidgetPosition::TOP,
            'cssClass' => 'bg-maroon',
        ]);
        $dashboardWidgetsService->install([
            'name'     => 'plugin_insertion_feedback',
            'template' => 'InsertionFeedback',
            'handler'  => Widgets\InsertionFeedbackWidget::class,
            'label'    => [
                'en' => 'Insertion Feedback',
                'de' => 'Inserate Feedback',
            ],
            'position' => DashboardWidgetPosition::TOP,
            'cssClass' => 'bg-green',
        ]);

        I18N::translate('config_group_insertions', [
            'en' => 'Insertions',
            'de' => 'Inserate',
        ]);
        I18N::translate('config_insertions_creation_moderator_mail', [
            'en' => 'Insertion creation reviewer: Mail',
            'de' => 'Inseratserstellungsprüfer: Mail',
        ]);
        I18N::translate('config_insertions_creation_moderator_name', [
            'en' => 'Insertion creation reviewer: Name',
            'de' => 'Inseratserstellungsprüfer: Name',
        ]);
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $configService->add([
            'name'     => 'insertions_creation_moderator_mail',
            'type'     => ConfigType::STRING,
            'group'    => 'insertions',
            'default'  => '',
            'label'    => 'config_insertions_creation_moderator_mail',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'insertions_creation_moderator_name',
            'type'     => ConfigType::STRING,
            'group'    => 'insertions',
            'default'  => '',
            'label'    => 'config_insertions_creation_moderator_name',
            'required' => true,
        ]);
    }


    /** @inheritDoc */
    public function uninstall(bool $keepData) {
        if (!$keepData) {
            /** @var DashboardWidgetsService $dashboardWidgetsService */
            $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
            $dashboardWidgetsService->uninstall('plugin_insertion_count');
            $dashboardWidgetsService->uninstall('plugin_insertion_moderation');
            $dashboardWidgetsService->uninstall('plugin_insertion_feedback');
        }
    }

    public function deactivate() {
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->deactivate('plugin_insertion_count');
        $dashboardWidgetsService->deactivate('plugin_insertion_moderation');
        $dashboardWidgetsService->deactivate('plugin_insertion_feedback');

    }

    public function activate() {
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add(BackendNavigationService::CONFIG_CONTENT);
        $backendNavigationService->add([
            'name'     => 'backend_insertion',
            'order'    => 100,
            'parent'   => BackendNavigationService::KEY_CONTENT,
            'icon'     => 'fa fa-newspaper-o',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_insertion_attribute',
            'order'    => 1,
            'parent'   => 'backend_insertion',
            'icon'     => 'fa fa-sitemap',
            'path'     => 'backend_insertion_attribute',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_insertion_insertion_type_group',
            'order'    => 2,
            'parent'   => 'backend_insertion',
            'icon'     => 'fa fa-file-text-o',
            'path'     => 'backend_insertion_insertion_type_group',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_insertion_insertion_type',
            'order'    => 3,
            'parent'   => 'backend_insertion',
            'icon'     => 'fa fa-file-text-o',
            'path'     => 'backend_insertion_type',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_insertion_insertion',
            'order'    => 4,
            'parent'   => 'backend_insertion',
            'icon'     => 'fa fa-bar-chart',
            'path'     => 'backend_insertions',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_insertions_seo_content',
            'order'    => 5,
            'parent'   => 'backend_insertion',
            'icon'     => 'fa fa-code',
            'path'     => 'backend_insertions_seo_content',
            'position' => 'sidebar',
        ]);

        /** @var AccountNavigationService $accountNavigationService */
        $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');
        $accountNavigationService->put([
            'name'     => 'frontend_account_insertions',
            'order'    => 1,
            'icon'     => 'insertion',
            'path'     => 'frontend_account_insertions',
            'position' => 'sidebar',
        ]);
        $accountNavigationService->put([
            'name'     => 'frontend_account_insertions_profile',
            'order'    => 2,
            'icon'     => 'profile',
            'path'     => 'frontend_account_insertions_profile',
            'position' => 'sidebar',
        ]);
        $accountNavigationService->put([
            'name'     => 'frontend_account_insertions_bookmarks',
            'order'    => 3,
            'icon'     => 'heart',
            'path'     => 'frontend_account_insertions_bookmarks',
            'position' => 'sidebar',
        ]);
        $accountNavigationService->put([
            'name'     => 'frontend_account_insertions_searchBookmarks',
            'order'    => 4,
            'icon'     => 'magnifier',
            'path'     => 'frontend_account_insertions_searchBookmarks',
            'position' => 'sidebar',
        ]);
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->activate('plugin_insertion_count');
        $dashboardWidgetsService->activate('plugin_insertion_moderation');
        $dashboardWidgetsService->activate('plugin_insertion_feedback');
    }

}
