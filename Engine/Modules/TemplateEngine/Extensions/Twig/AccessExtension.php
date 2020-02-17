<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 05.12.2018
 * Time: 15:49
 */

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Twig;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
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
    /** @var array OPTIONS_HTML_SAVE */
    private const OPTIONS_HTML_SAVE = [
        'is_safe' => ['html'],
    ];
    /** @var array OPTIONS_HTML_SAVE_WITH_CONTEXT */
    private const OPTIONS_HTML_SAVE_WITH_CONTEXT = [
        'is_safe'       => ['html'],
        'needs_context' => true,
    ];

    /** @inheritDoc */
    public function getFunctions() {
        return [
            new Twig_Function('config', [$this, 'getConfig'], self::OPTIONS_HTML_SAVE),
            new Twig_Function('i18n', [$this, 'getInternationalization'], self::OPTIONS_HTML_SAVE_WITH_CONTEXT),
            new Twig_Function('i18nExists', [$this, 'getInternationalizationExists'], self::OPTIONS_HTML_SAVE_WITH_CONTEXT),
            new Twig_Function('has_messages', [$this, 'hasMessages']),
        ];
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

}
