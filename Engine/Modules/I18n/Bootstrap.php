<?php

namespace Oforge\Engine\Modules\I18n;

use Oforge\Engine\Modules\AdminBackend\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Controller\Backend\LanguageController;
use Oforge\Engine\Modules\I18n\Controller\Backend\SnippetsController;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Models\Snippet;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;
use Oforge\Engine\Modules\I18n\Services\LanguageIdentificationService;
use Oforge\Engine\Modules\I18n\Services\LanguageService;

class Bootstrap extends AbstractBootstrap
{
    public function __construct()
    {
        $this->endpoints = [
            "/backend/i18n/languages" => ["controller" => LanguageController::class, "name" => "backend_i18n_languages", "asset_scope" => "Backend"],
            "/backend/i18n/snippets" => ["controller" => SnippetsController::class, "name" => "backend_i18n_snippets", "asset_scope" => "Backend"]
        ];

        $this->services = [
            "i18n" => InternationalizationService::class,
            "languages" => LanguageService::class,
            "language.identifier" => LanguageIdentificationService::class
        ];

        $this->models = [
            Language::class,
            Snippet::class
        ];
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */


    public function install()
    {
        /**
         * @var $sidebarNavigation BackendNavigationService
         */
        $sidebarNavigation = Oforge()->Services()->get("backend.navigation");

        $sidebarNavigation->put([
            "name" => "admin",
            "order" => 100,
            "position" => "sidebar",
        ]);

        $sidebarNavigation->put([
            "name" => "backend_i18n",
            "order" => 100,
            "parent" => "admin",
            "icon" => "glyphicon glyphicon-globe",
            "position" => "sidebar",
        ]);

        $sidebarNavigation->put([
            "name" => "backend_i18n_language",
            "order" => 1,
            "parent" => "backend_i18n",
            "icon" => "fa fa-language",
            "path" => "backend_i18n_languages",
            "position" => "sidebar",
        ]);

        $sidebarNavigation->put([
            "name" => "backend_i18n_snippets",
            "order" => 2,
            "parent" => "backend_i18n",
            "icon" => "fa fa-file-text-o",
            "path" => "backend_i18n_snippets",
            "position" => "sidebar",
        ]);

    }
}