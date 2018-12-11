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

class SlimExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new Twig_Function('url', array($this, 'getSlimUrl'), array('is_safe' => array('html')))
        );
    }

    public function getSlimUrl(...$vars)
    {
        $result = "";
        if (sizeof($vars) == 1) {
            $result = Oforge()->App()->getContainer()->get('router')->pathFor($vars[0]);
        } else if(sizeof($vars) == 2) {
            $result = Oforge()->App()->getContainer()->get('router')->pathFor($vars[0], $vars[1]);
        }
        return $result;
    }

}
