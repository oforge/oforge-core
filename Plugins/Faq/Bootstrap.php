<?php

namespace Faq;

use Faq\Controller\Backend\BackendFaqController;
use Faq\Controller\Frontend\FrontendAccountFaqController;
use Faq\Controller\Frontend\FrontendFaqController;
use Faq\Models\FaqModel;
use FrontendUserManagement\Services\AccountNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            BackendFaqController::class,
            FrontendFaqController::class,
            FrontendAccountFaqController::class,
        ];

        $this->models = [
            FaqModel::class,
        ];

        $this->dependencies = [
            \FrontendUserManagement\Bootstrap::class,
        ];
    }

    public function activate() {
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add(BackendNavigationService::CONFIG_CONTENT);
        $backendNavigationService->add([
            'name'     => 'backend_faq',
            'order'    => 6,
            'parent'   => BackendNavigationService::KEY_CONTENT,
            'icon'     => 'fa fa-info-circle',
            'path'     => 'backend_faq',
            'position' => 'sidebar',
        ]);

        /** @var AccountNavigationService $accountNavigationService */
        $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');
        $accountNavigationService->put([
            'name'     => 'frontend_account_faq',
            'order'    => 13,
            'icon'     => 'profile',
            'path'     => 'frontend_account_faq',
            'position' => 'sidebar',
        ]);
    }
}
