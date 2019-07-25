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
    /** @var LanguageService $language */
    private static $languageService;

    /**
     * Internationalization of text or labels.
     *
     * @param string $key
     * @param string|array|null $defaultValue
     * @param string|null $language
     *
     * @return string
     */
    public static function translate(string $key, $defaultValue = null, ?string $language = null) : string {
        try {
            if (!isset(self::$i18nService)) {
                /** @var InternationalizationService $service */
                self::$i18nService = Oforge()->Services()->get('i18n');
            }
            if (!isset($language)) {
                $language = self::getCurrentLanguage([]);
            }
            if (is_array($defaultValue)) {
                if (empty($defaultValue)) {
                    return $key;
                }
                $result = null;
                if (isset($defaultValue[$language])) {
                    $result = self::$i18nService->get($key, $language, $defaultValue[$language]);
                }
                foreach ($defaultValue as $languageIso => $languageDefaultValue) {
                    if ($language === $languageIso) {
                        continue;
                    }
                    $tmpResult = self::$i18nService->get($key, $languageIso, $defaultValue[$languageIso]);
                    if ($result === null) {
                        $result = $tmpResult;
                    }
                }

                return $result;
            }

            return self::$i18nService->get($key, $language, $defaultValue);
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }

        return self::translateFallback($key, $defaultValue);
    }

    /**
     * Twig internationalization function (i18n).
     *
     * @param array $context
     * @param string $key
     * @param string|array|null $defaultValue
     *
     * @return string
     */
    public static function twigTranslate($context, string $key, $defaultValue = null) : string {
        try {
            return self::translate($key, $defaultValue, self::getCurrentLanguage($context));
        } catch (Exception $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
        }

        return self::translateFallback($key);
    }

    /**
     * Twig internationalization function (i18n).
     *
     * @param array $context
     * @param string $key
     * @param string|null $defaultValue
     *
     * @return bool
     */
    public static function twigTranslateExists($context, string $key, ?string $defaultValue = null) : bool {
        try {
            if (!isset($language)) {
                $language = self::getCurrentLanguage($context);
            }

            if (!isset(self::$i18nService)) {
                /** @var InternationalizationService $service */
                self::$i18nService = Oforge()->Services()->get('i18n');
            }

            return self::$i18nService->exists($key, $language);
        } catch (Exception $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
        }

        return false;
    }

    /**
     * Init current language for internationalization.
     *
     * @param mixed $context
     *
     * @return string
     * @throws ServiceNotFoundException
     */
    protected static function getCurrentLanguage($context) : string {
        if (!isset(self::$languageService)) {
            /**@var LanguageService $languageService */
            self::$languageService = Oforge()->Services()->get('i18n.language');
        }

        return self::$languageService->getCurrentLanguageIso($context);
    }

    /**
     * Fallback, if translate fails because of errors.
     *
     * @param string $key
     * @param string|array|null $defaultValue
     *
     * @return string
     */
    protected static function translateFallback(string $key, $defaultValue = null) : string {
        if (isset($defaultValue)) {
            if (is_string($defaultValue)) {
                return $defaultValue;
            } elseif (is_array($defaultValue)) {
                try {
                    $languageIso = self::getCurrentLanguage([]);
                } catch (ServiceNotFoundException $e) {
                    $languageIso = 'en';
                }
                if (isset($defaultValue[$languageIso])) {
                    return $defaultValue[$languageIso];
                }
            }
        }

        return $key;
    }

}
