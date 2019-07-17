<?php

namespace Oforge\Engine\Modules\AdminBackend\KeyValueStore;

use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\AdminBackend\SystemSettings
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            Controller\Backend\KeyValueStoreController::class,
        ];
    }

    public function activate() {
        /** @var BackendNavigationService $backendNavigationService */
        I18N::translate('backend_key_value_store', "Key Value Store");
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add(BackendNavigationService::CONFIG_ADMIN);
        $backendNavigationService->add([
            'name'     => 'backend_key_value_store',
            'order'    => 101,
            'parent'   => BackendNavigationService::KEY_ADMIN,
            'icon'     => 'fa fa-gears',
            'path'     => 'backend_key_value_store',
            'position' => 'sidebar',
        ]);
    }

}
