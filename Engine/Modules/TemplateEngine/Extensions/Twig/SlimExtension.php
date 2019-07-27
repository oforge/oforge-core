<?php

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Twig;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Twig_Extension;
use Twig_Function;

/**
 * Class SlimExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class SlimExtension extends Twig_Extension {

    /**
     * @inheritDoc
     */
    public function getFunctions() {
        return [
            new Twig_Function('url', [$this, 'getSlimUrl'], [
                'is_safe' => ['html'],
            ]),
            new Twig_Function('full_url', [$this, 'getSlimFullUrl'], [
                'is_safe' => ['html'],
            ]),
            new Twig_Function('css', [$this, 'getSlimCSS'], [
                'is_safe' => ['html'],
            ]),
            new Twig_Function('attr', [$this, 'getSlimAttr'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * @param mixed ...$vars
     *
     * @return string
     */
    public function getSlimCSS(...$vars) : string {
        return $this->getSlimAttr(['css' => $vars]);
    }

    /**
     * @param mixed ...$vars
     *
     * @return string
     */
    public function getSlimAttr(...$vars) : string {
        if (!isset($vars) || empty($vars)) {
            return '';
        }

        return $this->buildSlimAttrString($vars);
    }

    /**
     * @param mixed ...$vars
     *
     * @return string
     * @throws ServiceNotFoundException
     */
    public function getSlimUrl(...$vars) : string {
        $name        = ArrayHelper::get($vars, 0);
        $namedParams = ArrayHelper::get($vars, 1, []);
        $queryParams = ArrayHelper::get($vars, 2, []);

        $urlService = Oforge()->Services()->get('url');

        return RouteHelper::getUrlWithBasePath($urlService->getUrl($name, $namedParams, $queryParams));
    }

    /**
     * @param mixed ...$vars
     *
     * @return string
     * @throws ServiceNotFoundException
     */
    public function getSlimFullUrl(...$vars) : string {
        $name        = ArrayHelper::get($vars, 0);
        $namedParams = ArrayHelper::get($vars, 1, []);
        $queryParams = ArrayHelper::get($vars, 2, []);

        $urlService = Oforge()->Services()->get('url');

        return RouteHelper::getFullUrl($urlService->getUrl($name, $namedParams, $queryParams));
    }

    /**
     * @param array $array
     *
     * @return string
     */
    protected function buildSlimAttrString(array $array) : string {
        $result = '';
        foreach ($array as $index => $value) {
            if (is_bool($value) && $value && is_string($index)) {
                $result .= " $index";
                continue;
            }
            if (is_array($value)) {
                if (is_numeric($index)) {
                    $result .= $this->buildSlimAttrString($value);
                } else {
                    $result .= ' ' . $index . '="';
                    $result .= trim($this->buildSlimAttrString($value));
                    $result .= '"';
                }
                continue;
            }
            if (is_string($value)) {
                $result .= " $value";
            }
        }

        return $result;
    }

}
