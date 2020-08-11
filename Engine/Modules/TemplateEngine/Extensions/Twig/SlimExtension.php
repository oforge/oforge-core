<?php

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Twig;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\DateTimeFormatter;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Twig_Extension;
use Twig_Filter;
use Twig_Function;
use Twig_Test;

/**
 * Class SlimExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class SlimExtension extends Twig_Extension {
    /** @var array OPTIONS_HTML_SAVE */
    private const OPTIONS_HTML_SAVE = [
        'is_safe' => ['html'],
    ];
    /** @var array OPTIONS_HTML_SAVE_WITH_CONTEXT */
    private const OPTIONS_HTML_SAVE_WITH_CONTEXT = [
        'is_safe'       => ['html'],
        'needs_context' => true,
    ];
    /** @var array $footerIncludes */
    private $footerIncludes = [];

    /** @inheritDoc */
    public function getFilters() {
        return [
            new Twig_Filter('deepMerge', [$this, 'filterDeepMerge'], self::OPTIONS_HTML_SAVE),
            new Twig_Filter('dotDeepMerge', [$this, 'filterDotDeepMerge'], self::OPTIONS_HTML_SAVE),
            new Twig_Filter('dotSet', [$this, 'filterDotSet'], self::OPTIONS_HTML_SAVE),
            new Twig_Filter('formatDate', [DateTimeFormatter::class, 'date'], self::OPTIONS_HTML_SAVE),
            new Twig_Filter('formatDatetime', [DateTimeFormatter::class, 'datetime'], self::OPTIONS_HTML_SAVE),
            new Twig_Filter('formatTime', [DateTimeFormatter::class, 'time'], self::OPTIONS_HTML_SAVE),
            new Twig_Filter('sortby', [$this, 'filterSortBy']),
            new Twig_Filter('ucfirst', 'ucfirst', self::OPTIONS_HTML_SAVE),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFunctions() {
        return [
            new Twig_Function('attr', [$this, 'functionAttr'], self::OPTIONS_HTML_SAVE),
            new Twig_Function('class', [$this, 'functionClass'], self::OPTIONS_HTML_SAVE),
            new Twig_Function('deepMerge', [$this, 'functionDeepMerge'], self::OPTIONS_HTML_SAVE),
            new Twig_Function('dotDeepMerge', [$this, 'functionDotDeepMerge'], self::OPTIONS_HTML_SAVE),
            new Twig_Function('dotSet', [$this, 'functionDotSet'], self::OPTIONS_HTML_SAVE_WITH_CONTEXT),
            new Twig_Function('full_url', [$this, 'functionFullUrl'], self::OPTIONS_HTML_SAVE),
            new Twig_Function('select_compare', [$this, 'functionSelectCompare'], self::OPTIONS_HTML_SAVE),
            new Twig_Function('style', [$this, 'functionStyle'], self::OPTIONS_HTML_SAVE),
            new Twig_Function('url', [$this, 'functionUrl'], self::OPTIONS_HTML_SAVE),
            new Twig_Function('addToFooter', [$this, 'functionAddToFooter'], self::OPTIONS_HTML_SAVE),
            new Twig_Function('includeInFooter', [$this, 'functionIncludeInFooter'], self::OPTIONS_HTML_SAVE),
        ];
    }

    /** @inheritDoc */
    public function getTests() {
        return [
            new Twig_Test('array', [$this, 'testIsArray']),
            new Twig_Test('string', [$this, 'testIsString']),
        ];
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
    public function filterDeepMerge(array $array1, array $array2) : array {
        return ArrayHelper::mergeRecursive($array1, $array2);
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
    public function filterDotDeepMerge(array $array1, array $array2) : array {
        return ArrayHelper::mergeRecursive($array1, ArrayHelper::dotToNested($array2));
    }

    /**
     * @param array $array
     * @param string|array $keyOrArray
     * @param mixed $value
     *
     * @return array
     */
    public function filterDotSet(array $array, $keyOrArray, $value = null) : array {
        if (is_array($keyOrArray)) {
            foreach ($keyOrArray as $index => $value) {
                $array = ArrayHelper::dotSet($array, $index, $value);
            }
        } else {
            $array = ArrayHelper::dotSet($array, $keyOrArray, $value);
        }

        return $array;
    }

    /**
     * @param $array
     * @param $key
     *
     * @return mixed
     */
    public function filterSortBy(array $array, $key) : array {
        usort($array, function ($item1, $item2) use ($key) {
            return $item1[$key] <=> $item2[$key];
        });

        return $array;
    }

    /**
     * @param mixed ...$vars
     *
     * @return string
     */
    public function functionAttr(...$vars) : string {
        if (!isset($vars) || empty($vars)) {
            return '';
        }

        return $this->buildSlimAttrString($vars);
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
    public function functionDeepMerge(array $array1, array $array2) : array {
        return ArrayHelper::mergeRecursive($array1, $array2);
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
    public function functionDotDeepMerge(array $array1, array $array2) : array {
        return ArrayHelper::mergeRecursive(ArrayHelper::dotToNested($array1), ArrayHelper::dotToNested($array2));
    }

    /**
     * @param array $context
     * @param string|array $keyOrArray
     * @param mixed $value
     */
    public function functionDotSet(array &$context, $keyOrArray, $value = null) : void {
        $context = $this->filterDotSet($context, $keyOrArray, $value);
    }

    /**
     * @param string $context
     */
    public function functionAddToFooter(string $context) {
        $this->footerIncludes[$context] = true;
    }

    /**
     * @param string $context
     *
     * @return bool
     */
    public function functionIncludeInFooter(string $context) : bool {
        return ($this->footerIncludes[$context] ?? false);
    }

    /**
     * @param mixed ...$vars
     *
     * @return string
     * @throws ServiceNotFoundException
     */
    public function functionFullUrl(...$vars) : string {
        return RouteHelper::getFullUrl($this->functionUrl(...$vars));
    }

    /**
     * @param mixed $var1
     * @param mixed $var2
     *
     * @return bool
     */
    public function functionSelectCompare($var1, $var2) : bool {
        return ((string) $var1) === ((string) $var2);
    }

    /**
     * @param string|array $input
     *
     * @return string
     */
    public function functionStyle($input) : string {
        return $this->buildSlimAttrString(['style' => $input]);
    }

    /**
     * @param string|array $input
     *
     * @return string
     */
    public function functionClass($input) : string {
        if (is_array($input)) {
            array_walk_recursive($input, function (&$value, $key) {
                if (!is_bool($value) && !is_string($value)) {
                    $value = boolval($value);
                }
            });
        }
        return $this->buildSlimAttrString(['class' => $input]);
    }

    /**
     * @param mixed ...$vars
     *
     * @return string
     * @throws ServiceNotFoundException
     */
    public function functionUrl(...$vars) : string {
        $name        = ArrayHelper::get($vars, 0);
        $namedParams = ArrayHelper::get($vars, 1, []);
        $queryParams = ArrayHelper::get($vars, 2, []);

        $urlService = Oforge()->Services()->get('url');

        return $urlService->getUrl($name, $namedParams, $queryParams);
    }

    /**
     * Twig test '... is array' to php is_array.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function testIsArray($value) : bool {
        return is_array($value);
    }

    /**
     * Twig test '... is string' to php is_string.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function testIsString($value) : bool {
        return is_string($value);
    }

    /**
     * @param array $array
     *
     * @return string
     */
    protected function buildSlimAttrString(array $array, ?string $prefix = null) : string {
        $result = '';
        foreach ($array as $index => $value) {
            $currentPrefix = (empty($prefix) ? '' : (ltrim($prefix, '-') . '-'));
            if (empty($value) && $value !== 0) {
                continue;
            } elseif ($index === 'style' || $index === 'class') {
                $glue       = $index === 'style' ? ';' : ' ';
                $prefixGlue = '-';
                $subResult  = $this->buildSlimAttrJoinedString($glue, $value, null, $prefixGlue);
                if (!empty($subResult)) {
                    $result .= " $index=\"$subResult\"";
                }
            } elseif (is_int($value) || is_string($value)) {
                $result .= is_numeric($index) ? " $value" : " $currentPrefix$index=\"$value\"";
            } elseif (is_bool($value)) {
                if ($value && is_string($index)) {
                    $result .= " $currentPrefix$index";
                }
            } elseif (is_array($value)) {
                if (is_numeric($index)) {
                    $result .= $this->buildSlimAttrString($value, $currentPrefix);
                } else {
                    $result .= $this->buildSlimAttrString($value, $currentPrefix . $index);
                }
            }
        }

        return $result;
    }

    /**
     * @param string $glue
     * @param string|array $input
     * @param null $prefix
     * @param string $prefixGlue
     *
     * @return string
     */
    protected function buildSlimAttrJoinedString(string $glue, $input, $prefix = null, $prefixGlue = '-') {
        $result = '';
        if (is_array($input)) {
            foreach ($input as $index => $value) {
                if (is_int($index)) {
                    $index = '';
                }
                $currentKey = (empty($prefix) ? $index : (ltrim($prefix . $prefixGlue . $index, $prefixGlue)));
                if ($value === null || $value === '') {
                    continue;
                }
                if (is_array($value)) {
                    $result .= $this->buildSlimAttrJoinedString($glue, $value, $currentKey);
                } elseif (is_bool($value)) {
                    if ($value) {
                        $result .= $currentKey . $glue;
                    }
                } else {
                    $result .= empty($currentKey) ? "$value$glue" : "$currentKey: $value$glue";
                }
            }
        } else {
            $result .= $input;
        }

        return trim($result);
    }

}
