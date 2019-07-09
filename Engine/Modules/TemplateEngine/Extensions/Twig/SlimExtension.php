<?php

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Twig;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
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
        ];
    }

    /**
     * @param mixed ...$vars
     *
     * @return string
     * @throws ServiceNotFoundException
     */
    public function getSlimUrl(...$vars) {
        $name        = ArrayHelper::get($vars, 0);
        $namedParams = ArrayHelper::get($vars, 1, []);
        $queryParams = ArrayHelper::get($vars, 2, []);

        $urlService = Oforge()->Services()->get('url');

        return $urlService->getUrl($name, $namedParams, $queryParams);
    }

}
