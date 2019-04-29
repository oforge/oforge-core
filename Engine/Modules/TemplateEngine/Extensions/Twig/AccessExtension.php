<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 05.12.2018
 * Time: 15:49
 */

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Twig;

use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Twig_Extension;
use Twig_ExtensionInterface;
use Twig_Function;

/**
 * Class AccessExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class AccessExtension extends Twig_Extension implements Twig_ExtensionInterface {

    /**
     * @inheritDoc
     */
    public function getFunctions() {
        return [
            new Twig_Function('config', [$this, 'getConfig'], ['is_safe' => ['html']]),
            new Twig_Function('i18n', [$this, 'getInternationalization'], [
                'is_safe'       => ['html'],
                'needs_context' => true,
            ]),
        ];
    }

    /**
     * @param mixed ...$vars
     *
     * @return mixed|string
     * @throws ConfigElementNotFoundException
     * @throws ServiceNotFoundException
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
        $result = "";
        if (count($vars) > 0 && isset($vars[0])) {
            $defaultValue = count($vars) > 1 ? $vars[1] : null;

            $result = I18N::twigTranslate($context, $vars[0], $defaultValue);
        }

        return $result;
    }

}
