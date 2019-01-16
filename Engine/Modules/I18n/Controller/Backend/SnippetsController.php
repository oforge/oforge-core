<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 17.12.2018
 * Time: 14:54
 */

namespace Oforge\Engine\Modules\I18n\Controller\Backend;

use Oforge\Engine\Modules\CRUD\Controller\Backend\CrudController;
use Oforge\Engine\Modules\I18n\Models\Snippet;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;
use Oforge\Engine\Modules\I18n\Services\LanguageService;


class SnippetsController extends CrudController
{
    protected $model = Snippet::class;

    function __construct()
    {
        parent::__construct();
    }

    public function initPermissions()
    {
        /*
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
        */
    }


}