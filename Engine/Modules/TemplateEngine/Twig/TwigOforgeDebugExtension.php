<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 05.12.2018
 * Time: 15:49
 */

namespace Oforge\Engine\Modules\TemplateEngine\Twig;

use Twig_Environment;
use Twig_Extension;
use Twig_Function;
use Twig_TemplateWrapper;

class TwigOforgeDebugExtension extends Twig_Extension {
    public function getFunctions() {
        // dump is safe if var_dump is overridden by xdebug
        $isDumpOutputHtmlSafe = extension_loaded( 'xdebug' )
                                // false means that it was not set (and the default is on) or it explicitly enabled
                                && ( false === ini_get( 'xdebug.overload_var_dump' ) || ini_get( 'xdebug.overload_var_dump' ) )
                                // false means that it was not set (and the default is on) or it explicitly enabled
                                // xdebug.overload_var_dump produces HTML only when html_errors is also enabled
                                && ( false === ini_get( 'html_errors' ) || ini_get( 'html_errors' ) )
                                || 'cli' === PHP_SAPI;
        
        return array(
            new Twig_Function( 'o_dump', array($this, 'oforge_var_dump'), array( 'is_safe'           => $isDumpOutputHtmlSafe ? array( 'html' ) : array(),
                                                                   'needs_context'     => true,
                                                                   'needs_environment' => true
            ) ),
        );
    }
    
    public function oforge_var_dump( Twig_Environment $env, $context, ...$vars ) {
        if ( ! $env->isDebug() ) {
            return;
        }
        ob_start();
        if ( ! $vars ) {
            $vars = array();
            foreach ( $context as $key => $value ) {
                if ( ! $value instanceof Twig_TemplateWrapper ) {
                    $vars[ $key ] = $value;
                }
            }
            
            return "<script>console.log(" . json_encode( $vars ) . ") </script>";
        } else {
            return "<script>console.log(" . json_encode( ...$vars ) . ") </script>";
        }
    }
}
