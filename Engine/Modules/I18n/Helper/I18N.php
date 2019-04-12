<?php

namespace Oforge\Engine\Modules\I18n\Helper;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;
use Oforge\Engine\Modules\I18n\Services\LanguageIdentificationService;

/**
 * Class I18N
 *
 * @package Oforge\Engine\Modules\I18n\Helper
 */
class I18N {
    /** @var InternationalizationService $i18nService */
    private static $i18nService;
    /** @var string $language */
    private static $language;

    /**
     * Internationalization of text or labels.
     *
     * @param string $key
     * @param string|null $defaultValue
     * @param string|null $language
     *
     * @return string
     * @throws ServiceNotFoundException
     */
    public static function translate(string $key, ?string $defaultValue = null, ?string $language = null) : string {
        if (!isset($language)) {
            self::initCurrentLanguage([]);
            $language = self::$language;
        }
        if (!isset(self::$i18nService)) {
            /** @var InternationalizationService $service */
            self::$i18nService = Oforge()->Services()->get('i18n');
        }

        return self::$i18nService->get($key, $language, $defaultValue);
    }

    /**
     * Twig internationalization function (i18n).
     *
     * @param array $context
     * @param string $key
     * @param string|null $defaultValue
     *
     * @return string
     * @throws ServiceNotFoundException
     */
    public static function twigTranslate($context, string $key, ?string $defaultValue = null) : string {
        self::initCurrentLanguage($context);

        return self::translate($key, $defaultValue, self::$language);
    }

    /**
     * Init current language for internationalization.
     *
     * @param $context
     *
     * @throws ServiceNotFoundException
     */
    protected static function initCurrentLanguage($context) {
        if (!isset(self::$language)) {
            /**@var LanguageIdentificationService $languageIdentificationService */
            $languageIdentificationService = Oforge()->Services()->get('language.identifier');

            self::$language = $languageIdentificationService->getCurrentLanguage($context);
        }
    }

}
