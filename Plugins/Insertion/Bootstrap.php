<?php

namespace Insertion;

use FrontendUserManagement\Services\AccountNavigationService;
use Insertion\Controller\Backend\BackendAttributeController;
use Insertion\Controller\Backend\BackendInsertionController;
use Insertion\Controller\Backend\BackendInsertionTypeController;
use Insertion\Controller\Frontend\FrontendInsertionController;
use Insertion\Controller\Frontend\FrontendUsersInsertionController;
use Insertion\Models\AttributeKey;
use Insertion\Models\AttributeValue;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionAttributeValue;
use Insertion\Models\InsertionContact;
use Insertion\Models\InsertionContent;
use Insertion\Models\InsertionFeedback;
use Insertion\Models\InsertionMedia;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Models\InsertionTypeGroup;
use Insertion\Services\AttributeService;
use Insertion\Services\InsertionCreatorService;
use Insertion\Services\InsertionFeedbackService;
use Insertion\Services\InsertionListService;
use Insertion\Services\InsertionMockService;
use Insertion\Services\InsertionService;
use Insertion\Services\InsertionTypeService;
use Insertion\Services\InsertionUpdaterService;
use Insertion\Twig\InsertionExtensions;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
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
        ];

        $this->services = [
            'insertion'           => InsertionService::class,
            'insertion.type'      => InsertionTypeService::class,
            'insertion.attribute' => AttributeService::class,
            'insertion.mock'      => InsertionMockService::class,
            'insertion.creator'   => InsertionCreatorService::class,
            'insertion.updater'   => InsertionUpdaterService::class,
            'insertion.feedback'  => InsertionFeedbackService::class,
            'insertion.list'      => InsertionListService::class,
        ];

        $this->models = [
            AttributeKey::class,
            AttributeValue::class,
            InsertionContact::class,
            Insertion::class,
            InsertionAttributeValue::class,
            InsertionType::class,
            InsertionTypeAttribute::class,
            InsertionContent::class,
            InsertionMedia::class,
            InsertionTypeGroup::class,
            InsertionFeedback::class,
        ];

        $this->dependencies = [
            \FrontendUserManagement\Bootstrap::class,
        ];
    }

    public function load() {
        /**
         * @var $templateRenderer TemplateRenderService
         */
        $templateRenderer = Oforge()->Services()->get("template.render");

        $templateRenderer->View()->addExtension(new InsertionExtensions());
    }

    public function activate() {
        /** @var BackendNavigationService $sidebarNavigation */
        $sidebarNavigation = Oforge()->Services()->get('backend.navigation');
        $sidebarNavigation->put([
            'name'     => 'backend_content',
            'order'    => 2,
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_insertion',
            'order'    => 100,
            'parent'   => 'backend_content',
            'icon'     => 'fa fa-newspaper-o',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_insertion_attribute',
            'order'    => 1,
            'parent'   => 'backend_insertion',
            'icon'     => 'fa fa-sitemap',
            'path'     => 'backend_insertion_attribute',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_insertion_insertion_type',
            'order'    => 2,
            'parent'   => 'backend_insertion',
            'icon'     => 'fa fa-file-text-o',
            'path'     => 'backend_insertion_type',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_insertion_insertion',
            'order'    => 3,
            'parent'   => 'backend_insertion',
            'icon'     => 'fa fa-bar-chart',
            'path'     => '',
            'position' => 'sidebar',
        ]);


        /** @var AccountNavigationService $accountNavigationService */
        $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');

        $accountNavigationService->put([
            'name'     => 'frontend_account_insertions',
            'order'    => 1,
            'icon'     => 'profil',
            'path'     => 'frontend_account_insertions',
            'position' => 'sidebar',
        ]);
    }

}
