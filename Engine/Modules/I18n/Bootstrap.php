<?php

namespace Oforge\Engine\Modules\I18n;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Controller\Backend\LanguageController;
use Oforge\Engine\Modules\I18n\Controller\Backend\SnippetsController;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Models\Snippet;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;
use Oforge\Engine\Modules\I18n\Services\LanguageIdentificationService;
use Oforge\Engine\Modules\I18n\Services\LanguageService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\I18n
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            LanguageController::class,
            SnippetsController::class,
        ];

        $this->models = [
            Language::class,
            Snippet::class,
        ];

        $this->services = [
            "i18n"                => InternationalizationService::class,
            "languages"           => LanguageService::class,
            "language.identifier" => LanguageIdentificationService::class,
        ];
    }

    /**
     * @throws ConfigElementAlreadyExists
     * @throws ConfigOptionKeyNotExists
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws ParentNotFoundException
     */
    public function install() {
        /** @var BackendNavigationService $sidebarNavigation */
        $sidebarNavigation = Oforge()->Services()->get("backend.navigation");

        $sidebarNavigation->put([
            "name"     => "admin",
            "order"    => 100,
            "position" => "sidebar",
        ]);

        $sidebarNavigation->put([
            "name"     => "backend_i18n",
            "order"    => 100,
            "parent"   => "admin",
            "icon"     => "glyphicon glyphicon-globe",
            "position" => "sidebar",
        ]);

        $sidebarNavigation->put([
            "name"     => "backend_i18n_language",
            "order"    => 1,
            "parent"   => "backend_i18n",
            "icon"     => "fa fa-language",
            "path"     => "backend_i18n_languages",
            "position" => "sidebar",
        ]);

        $sidebarNavigation->put([
            "name"     => "backend_i18n_snippets",
            "order"    => 2,
            "parent"   => "backend_i18n",
            "icon"     => "fa fa-file-text-o",
            "path"     => "backend_i18n_snippets",
            "position" => "sidebar",
        ]);

    }
}
