<?php

namespace Oforge\Engine\Modules\I18n\Helper;

use Exception;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;
use Oforge\Engine\Modules\I18n\Services\LanguageService;

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
     */
    public static function translate(string $key, ?string $defaultValue = null, ?string $language = null) : string {
        try {
            if (!isset($language)) {
                self::initCurrentLanguage([]);
                $language = self::$language;
            }
            if (!isset(self::$i18nService)) {
                /** @var InternationalizationService $service */
                self::$i18nService = Oforge()->Services()->get('i18n');
            }

            return self::$i18nService->get($key, $language, $defaultValue);
        } catch (Exception $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
        }

        return self::translateFallback($key, $defaultValue);
    }

    /**
     * Twig internationalization function (i18n).
     *
     * @param array $context
     * @param string $key
     * @param string|null $defaultValue
     *
     * @return string
     */
    public static function twigTranslate($context, string $key, ?string $defaultValue = null) : string {
        try {
            self::initCurrentLanguage($context);

            return self::translate($key, $defaultValue, self::$language);
        } catch (Exception $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
        }

        return self::translateFallback($key, $defaultValue);
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
            /**@var LanguageService $languageService */
            $languageService = Oforge()->Services()->get('i18n.language');

            self::$language = $languageService->getCurrentLanguage($context);
        }
    }

    /**
     * Fallback, if translate fails because of errors.
     *
     * @param string $key
     * @param string|null $defaultValue
     *
     * @return string
     */
    protected static function translateFallback(string $key, ?string $defaultValue = null) : string {
        return isset($defaultValue) ? $defaultValue : $key;
    }

}
