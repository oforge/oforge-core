<?php

namespace Insertion;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use FrontendUserManagement\Services\AccountNavigationService;
use Insertion\Controller\Backend\BackendInsertionFeedbackController;
use Insertion\Services\InsertionProfileProgressService;
use Insertion\Commands\ReminderCommand;
use Insertion\Commands\SearchBookmarkCommand;
use Insertion\Controller\Backend\BackendAttributeController;
use Insertion\Controller\Backend\BackendInsertionController;
use Insertion\Controller\Backend\BackendInsertionTypeController;
use Insertion\Controller\Backend\BackendInsertionTypeGroupController;
use Insertion\Controller\Frontend\FrontendInsertionController;
use Insertion\Controller\Frontend\FrontendUsersInsertionController;
use Insertion\Cronjobs\Reminder14DaysCronjob;
use Insertion\Cronjobs\Reminder30DaysCronjob;
use Insertion\Cronjobs\Reminder3DaysCronjob;
use Insertion\Cronjobs\SearchBookmarkCronjob;
use Insertion\Middleware\InsertionDetailMiddleware;
use Insertion\Models\AttributeKey;
use Insertion\Models\AttributeValue;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionAttributeValue;
use Insertion\Models\InsertionContact;
use Insertion\Models\InsertionContent;
use Insertion\Models\InsertionFeedback;
use Insertion\Models\InsertionMedia;
use Insertion\Models\InsertionProfile;
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
use Insertion\Services\InsertionService;
use Insertion\Services\InsertionSliderService;
use Insertion\Services\InsertionTypeService;
use Insertion\Services\InsertionUpdaterService;
use Insertion\Services\InsertionUrlService;
use Insertion\Services\InsertionZipService;
use Insertion\Twig\InsertionExtensions;
use Oforge\Engine\Modules\AdminBackend\Core\Enums\DashboardWidgetPosition;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateRenderService;

class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            FrontendInsertionController::class,
            FrontendUsersInsertionController::class,
            BackendAttributeController::class,
            BackendInsertionController::class,
            BackendInsertionTypeController::class,
            BackendInsertionTypeGroupController::class,
            BackendInsertionFeedbackController::class,
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
            'insertion.profile.progress'=> InsertionProfileProgressService::class,
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
        ];

        $this->dependencies = [
            \FrontendUserManagement\Bootstrap::class,
            \Messenger\Bootstrap::class,
            \Helpdesk\Bootstrap::class,
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

        //what is this??
        new QueryCacheProfile(0, "asd");
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
           'name'      => 'plugin_insertion_feedback',
           'template'  => 'InsertionFeedback',
           'handler'   => Widgets\InsertionFeedbackWidget::class,
           'label'     => [
               'en' => 'Insertion Feedback',
               'de' => 'Inserate Feedback',
               ],
               'position' => DashboardWidgetPosition::TOP,
               'cssClass' => 'bg-green',
        ]);
    }

    public function uninstall() {
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->uninstall('plugin_insertion_count');
        $dashboardWidgetsService->uninstall('plugin_insertion_moderation');
        $dashboardWidgetsService->uninstall('plugin_insertion_feedback');
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
