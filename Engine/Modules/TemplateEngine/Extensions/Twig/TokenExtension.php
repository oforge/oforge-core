<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 12.12.2018
 * Time: 15:58
 */

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Twig;

use Oforge\Engine\Modules\Core\Services\TokenService;
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
     * @param string $action
     *
     * @return string
     */
    public function getToken(string $action = TokenService::DEFAULT_ACTION) {
        /** @var TokenService $tokenService */
        $tokenService = Oforge()->Services()->get('token');

        return $tokenService->get($action);
    }

}
