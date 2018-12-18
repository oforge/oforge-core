<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 17.12.2018
 * Time: 14:54
 */

namespace Oforge\Engine\Modules\I18n\Controller\Backend;

use Oforge\Engine\Modules\CRUD\Controller\Backend\CrudController;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;
use Oforge\Engine\Modules\I18n\Services\LanguageService;


class LanguageController extends CrudController
{

    protected $model = LanguageService::class;
    /**
     * @var $i18nService InternationalizationService
     */
    private $i18nService;
    /**
     * @var $languagesService LanguageService
     */
    private $languagesService;

    function __construct()
    {
        $this->i18nService = Oforge()->Services()->get("i18n");
        $this->languagesService = Oforge()->Services()->get("languages");
    }


    public function initPermissions()
    {
        /*
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
        */
    }


}