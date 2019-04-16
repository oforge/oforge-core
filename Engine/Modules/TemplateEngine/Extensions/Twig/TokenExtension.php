<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 12.12.2018
 * Time: 15:58
 */

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Twig;

use Twig_Extension;
use Twig_Function;

/**
 * Class TokenExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class TokenExtension extends Twig_Extension {

    /**
     * @inheritDoc
     */
    public function getFunctions() {
        return [
            new Twig_Function('token', [$this, 'getToken'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getToken() {
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['token'];
    }

}
