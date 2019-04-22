<?php

namespace Oforge\Engine\Modules\AdminBackend\TemplateSettings;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\TemplateSettings\Controller\Backend\TemplateSettingsController;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\AdminBackend\TemplateSettings
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            TemplateSettingsController::class,
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExists
     * @throws ConfigOptionKeyNotExists
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     */
    public function activate() {
        /** @var BackendNavigationService $backendNavigation */
        $backendNavigation = Oforge()->Services()->get('backend.navigation');
        $backendNavigation->put([
            'name'     => 'backend_template_settings',
            'order'    => 98,
            'parent'   => 'admin',
            'icon'     => 'fa fa-paint-brush',
            'path'     => 'backend_template_settings',
            'position' => 'sidebar',
        ]);
    }

}
