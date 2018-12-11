<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 05.12.2018
 * Time: 15:49
 */

namespace Oforge\Engine\Modules\TemplateExtensions\Twig;

use Twig_Environment;
use Twig_Extension;
use Twig_Function;
use Twig_TemplateWrapper;

class AccessConfigExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new Twig_Function('config', array($this, 'get_config'), array('is_safe' => array('html'))),
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
}
