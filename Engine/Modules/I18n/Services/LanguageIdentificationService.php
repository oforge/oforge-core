<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:11
 */

namespace Oforge\Engine\Modules\I18n\Services;

use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Models\Snippet;


/**
 * Class LanguageIdentificationService
 * @package Oforge\Engine\Modules\I18n\Services
 */
class LanguageIdentificationService
{
    /**
     * LanguageIdentificationService constructor.
     */
    public function __construct()
    {
        $this->em = Oforge()->DB()->getManager();
        $this->repo = $this->em->getRepository(Language::class);
    }

    /**
     * @param $context
     * @return string
     */
    public function getCurrentLanguage($context)
    {
        if (key_exists("meta", $context) &&
            key_exists("route", $context["meta"]) &&
            key_exists("assetScope", $context["meta"]["route"]) &&
            key_exists("languageId", $context["meta"]["route"]) &&
            strtolower($context["meta"]["route"]["assetScope"]) != "backend"
        ) {

            return $context["meta"]["route"]["languageId"];
        }

        if (key_exists("config", $_SESSION) && key_exists("language", $_SESSION["config"])) {
            return $_SESSION["config"]["language"];
        }

        /**
         * @var $all Language[]
         */
        $all = $this->repo->findAll();

        if (sizeof($all) > 0) {
            return $all[0]->getIso();
        }

        return "en";
    }
}
