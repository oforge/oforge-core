<?php

namespace ProductPlacement;

use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Helper\Statics;
use ProductPlacement\Controller\Backend\ProductPlacementController;
use ProductPlacement\Models\ProductPlacement;

/**
 * Class Bootstrap
 *
 * @package ProductPlacement
 */
class Bootstrap extends AbstractBootstrap {
    protected $order = Statics::DEFAULT_ORDER;

    public function __construct() {
        $this->endpoints = [
            ProductPlacementController::class
        ];
        $this->models = [
            ProductPlacement::class,
        ];
    }

    public function activate() {
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add(BackendNavigationService::CONFIG_CONTENT);
        $backendNavigationService->add([
            'name'     => 'plugin_product_placement',
            'order'    => 100,
            'parent'   => BackendNavigationService::KEY_CONTENT,
            'icon'     => 'fa fa-tags',
            'path'     => 'backend_product_placement',
            'position' => 'sidebar',
        ]);
    }
}
