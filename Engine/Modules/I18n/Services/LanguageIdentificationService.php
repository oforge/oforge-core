<?php

namespace Oforge\Engine\Modules\I18n\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\I18n\Models\Language;

/**
 * Class LanguageIdentificationService
 *
 * @package Oforge\Engine\Modules\I18n\Services
 */
class LanguageIdentificationService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(['default' => Language::class]);
    }

    /**
     * @param $context
     *
     * @return string
     */
    public function getCurrentLanguage($context) {
        if (isset($context['meta'])
            && isset($context['meta']['route'])
            && isset($context['meta']['route']['assetScope'])
            && isset($context['meta']['route']['languageId'])
            && strtolower($context['meta']['route']['assetScope']) !== 'backend') {
            return $context['meta']['route']['languageId'];
        }

        if (isset($_SESSION) && isset($_SESSION['config']) && isset($_SESSION['config']['language'])) {
            return $_SESSION['config']['language'];
        }

        /** @var ?Language $all */
        $language = $this->repository()->findOneBy(['active' => true]);

        if (isset($language)) {
            return $language->getIso();
        }

        return 'en';
    }

}
