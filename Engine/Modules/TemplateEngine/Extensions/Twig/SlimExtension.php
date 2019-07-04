<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 05.12.2018
 * Time: 15:49
 */

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Twig;

use Exception;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Slim\Router;
use Twig_Extension;
use Twig_Function;

/**
 * Class SlimExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class SlimExtension extends Twig_Extension {
    /** @var Router $router */
    private $router;

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
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function getSlimUrl(...$vars) {
        return Oforge()->Services()->get("url")->getSlimUrl(...$vars);
    }
}
