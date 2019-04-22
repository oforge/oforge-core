<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 17.12.2018
 * Time: 14:54
 */

namespace Oforge\Engine\Modules\I18n\Controller\Backend;

use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\CrudController;
use Oforge\Engine\Modules\I18n\Models\Language;

/**
 * Class LanguageController
 *
 * @package Oforge\Engine\Modules\I18n\Controller\Backend
 * @EndpointClass(path="/backend/i18n/languages", name="backend_i18n_languages", assetScope="Backend")
 */
class LanguageController extends CrudController
{
    protected $model = Language::class;

    function __construct()
    {
        parent::__construct();
    }
}
