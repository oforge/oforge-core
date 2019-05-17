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
     */
    public function getSlimUrl(...$vars) {
        if (!isset($this->router)) {
            $this->router = Oforge()->App()->getContainer()->get('router');
        }
        $name        = ArrayHelper::get($vars, 0);
        $namedParams = ArrayHelper::get($vars, 1, []);
        $queryParams = ArrayHelper::get($vars, 2, []);
        try {
            $result = $this->router->pathFor($name, $namedParams, $queryParams);
        } catch (Exception $e) {
            $result = $name;
        }

        return $result;
    }
}
