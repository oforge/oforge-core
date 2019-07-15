<?php

namespace Oforge\Engine\Modules\I18n;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Controller\Backend\I18n\LanguageController;
use Oforge\Engine\Modules\I18n\Controller\Backend\I18n\SnippetsController;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\I18n\Middleware\I18nMiddleware;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Models\Snippet;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;
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
            'i18n'          => InternationalizationService::class,
            'i18n.language' => LanguageService::class,
        ];
    }

    /**
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     */
    public function install() {
        /** @var LanguageService $languageService */
        $languageService = Oforge()->Services()->get('i18n.language');
        $languageService->create([
            'iso'     => 'en',
            'name'    => 'English',
            'active'  => true,
            'default' => true,
        ]);

        I18N::translate('backend_i18n', ['en' => 'Internationalization', 'de' => 'Internationalisierung']);
        I18N::translate('backend_i18n_language', ['en' => 'Language', 'de' => 'Sprache']);
        I18N::translate('backend_i18n_snippets', ['en' => 'Text snippets', 'de' => 'Textschnipsel']);

        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add([
            'name'     => 'admin',
            'order'    => 100,
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_i18n',
            'order'    => 100,
            'parent'   => 'admin',
            'icon'     => 'glyphicon glyphicon-globe',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_i18n_language',
            'order'    => 1,
            'parent'   => 'backend_i18n',
            'icon'     => 'fa fa-language',
            'path'     => 'backend_i18n_languages',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_i18n_snippets',
            'order'    => 2,
            'parent'   => 'backend_i18n',
            'icon'     => 'fa fa-file-text-o',
            'path'     => 'backend_i18n_snippets',
            'position' => 'sidebar',
        ]);
    }

    public function activate() {
        if (Oforge()->isAppReady()) {
            Oforge()->App()->add(new I18nMiddleware());
        }
    }

}
