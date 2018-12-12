<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 12.12.2018
 * Time: 15:58
 */

namespace Oforge\Engine\Modules\TemplateExtensions\Twig;

use Twig_Environment;
use Twig_Extension;
use Twig_Function;
use Twig_TemplateWrapper;

class TokenExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return array(
            new Twig_Function('token', array($this, 'getToken'), array('is_safe' => array('html')))
        );
    }
    
    /**
     *
     * @return string
     * @throws \Exception
     */
    public function getToken()
    {
        if (empty($_SESSION['token'])) {
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['token'];
    }
}
