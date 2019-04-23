<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 05.12.2018
 * Time: 15:49
 */

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Twig;

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
            new Twig_Function('url', [$this, 'getSlimUrl'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTests() {
        return [
            new \Twig_Test('array', [$this, 'isArray']),
            new \Twig_Test('string', [$this, 'isString']),
        ];
    }

    /**
     *
     * @param mixed ...$vars
     *
     * @return string
     */
    public function getSlimUrl(...$vars) {
        if (!isset($this->router)) {
            $this->router = Oforge()->App()->getContainer()->get('router');
        }
        switch (count($vars)) {
            case 1:
                $result = $this->router->pathFor($vars[0]);
                break;
            case 2:
                $result = $this->router->pathFor($vars[0], $vars[1]);
                break;
            case 3:
                $result = $this->router->pathFor($vars[0], $vars[1], $vars[2]);
                break;
            default:
                $result = '';
                break;
        }

        return $result;
    }

    /**
     * Slim test '... is array' to php is_array.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isArray($value) {
        return is_array($value);
    }

    /**
     * Slim test '... is string' to php is_string.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isString($value) {
        return is_string($value);
    }

}
