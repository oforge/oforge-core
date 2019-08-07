<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 05.12.2018
 * Time: 15:49
 */

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Twig;

use Doctrine\ORM\ORMException;
use Messenger\Services\FrontendMessengerService;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\DateTimeFormatter;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Twig_Extension;
use Twig_ExtensionInterface;
use Twig_Filter;
use Twig_Function;

/**
 * Class AccessExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class AccessExtension extends Twig_Extension implements Twig_ExtensionInterface {

    /** @inheritDoc */
    public function getFilters() {
        return [
            new Twig_Filter('formatDate', [DateTimeFormatter::class, 'date'], [
                'is_safe' => ['html'],
            ]),
            new Twig_Filter('formatDatetime', [DateTimeFormatter::class, 'datetime'], [
                'is_safe' => ['html'],
            ]),
            new Twig_Filter('formatTime', [DateTimeFormatter::class, 'time'], [
                'is_safe' => ['html'],
            ]),
            new Twig_Filter('ucfirst', 'ucfirst', [
                'is_safe' => ['html'],
            ]),
            new Twig_Filter('sortby', [$this, 'sortBy']),
        ];
    }

    /** @inheritDoc */
    public function getFunctions() {
        return [
            new Twig_Function('config', [$this, 'getConfig'], [
                'is_safe' => ['html'],
            ]),
            new Twig_Function('i18n', [$this, 'getInternationalization'], [
                'is_safe'       => ['html'],
                'needs_context' => true,
            ]),
            new Twig_Function('i18nExists', [$this, 'getInternationalizationExists'], [
                'is_safe'       => ['html'],
                'needs_context' => true,
            ]),
            new Twig_Function('dotToNested', [$this, 'dotToNested'], [
                'is_safe' => ['html'],
            ]),
            new Twig_Function('mergeRecursive', [$this, 'mergeRecursive'], [
                'is_safe' => ['html'],
            ]),
            new Twig_Function('has_messages',  [$this, 'hasMessages']),
        ];
    }

    /** @inheritDoc */
    public function getTests() {
        return [
            new \Twig_Test('array', [$this, 'isArray']),
            new \Twig_Test('string', [$this, 'isString']),
        ];
    }

    /**
     * Convert array with keys in dot notation to nested arrays.
     *
     * @param array $array
     *
     * @return array
     * @see ArrayHelper::dotToNested()
     */
    public function dotToNested(array $array) : array {
        return ArrayHelper::dotToNested($array);
    }

    /**
     * Merge array recursive.
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     * @see ArrayHelper::mergeRecursive()
     */
    public function mergeRecursive(array $array1, array $array2) : array {
        return ArrayHelper::mergeRecursive($array1, $array2);
    }

    /**
     * @param mixed ...$vars
     *
     * @return mixed|string
     * @throws ConfigElementNotFoundException
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function getConfig(...$vars) {
        $result = '';
        if (count($vars) == 1) {
            /** @var ConfigService $configService */
            $configService = Oforge()->Services()->get('config');

            $result = $configService->get($vars[0]);
        }

        return $result;
    }

    /**
     * @param $context
     * @param mixed ...$vars
     *
     * @return string
     */
    public function getInternationalization($context, ...$vars) {
        $result     = '';
        $varsLength = count($vars);
        if ($varsLength > 0 && isset($vars[0])) {
            $key          = $vars[0];
            $defaultValue = count($vars) > 1 ? $vars[1] : null;
            if (is_array($key)) {
                if (isset($key['key']) && isset($key['default'])) {
                    $defaultValue = $key['default'];
                    $key          = $key['key'];
                } else {
                    return $key;
                }
            }
            $result = I18N::twigTranslate($context, $key, $defaultValue);
        }

        return $result;
    }

    /**
     * @param $context
     * @param mixed ...$vars
     *
     * @return string
     */
    public function getInternationalizationExists($context, ...$vars) {
        $result = false;
        if (count($vars) > 0 && isset($vars[0])) {
            $defaultValue = count($vars) > 1 ? $vars[1] : null;

            $result = I18N::twigTranslateExists($context, $vars[0], $defaultValue);
        }

        return $result;
    }

    /**
     * Twig test '... is array' to php is_array.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isArray($value) {
        return is_array($value);
    }

    /**
     * Twig test '... is string' to php is_string.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isString($value) {
        return is_string($value);
    }

    public function sortBy($array, $key) {
        usort($array, function ($item1, $item2) use ($key) {
            return $item1[$key] <=> $item2[$key];
        });
        return $array;
    }
}
