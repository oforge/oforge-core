<?php

namespace Oforge\Engine\Modules\I18n\Controller\Backend\I18n;

use Oforge\Engine\Modules\CRUD\Controller\Backend\CrudController;
use Oforge\Engine\Modules\I18n\Models\Language;

/**
 * Class LanguageController
 *
 * @package Oforge\Engine\Modules\I18n\Controller\Backend
 */
class LanguageController extends CrudController {
    /** @var string $model */
    protected $model = Language::class;

    public function __construct() {
        parent::__construct();
    }

}
