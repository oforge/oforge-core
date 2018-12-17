<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 05.12.2018
 * Time: 15:49
 */

namespace Oforge\Engine\Modules\TemplateExtensions\Twig;

use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;
use Oforge\Engine\Modules\I18n\Services\LanguageIdentificationService;
use Twig_Environment;
use Twig_Extension;
use Twig_Function;
use Twig_TemplateWrapper;

class AccessExtension extends Twig_Extension implements \Twig_ExtensionInterface
{
    public function getFunctions()
    {
        return array(
            new Twig_Function('config', array($this, 'get_config'), array('is_safe' => array('html'))),
            new Twig_Function('i18n', array($this, 'get_internationalization'),
                array(
                    'is_safe' => array('html'),
                    'needs_context' => true)
            ),
        );
    }

    public function get_config(...$vars)
    {
        $result = "";
        if (sizeof($vars) == 1) {
            /**
             * @var $configService ConfigService
             */
            $configService = Oforge()->Services()->get("config");

            $result = $configService->get($vars[0]);
        }
        return $result;
    }

    public function get_internationalization($context, ...$vars)
    {
        $result = "";
        if (sizeof($vars) == 1 && isset($vars[0])) {
            /**
             * @var $service InternationalizationService
             */
            $service = Oforge()->Services()->get("i18n");


            /**
             * @var $lService LanguageIdentificationService
             */
            $lService = Oforge()->Services()->get("language.identifier");
            $result = $service->get($vars[0], $lService->getCurrentLanguage($context));
        }

        return $result;
    }
}
