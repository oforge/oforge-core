<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 05.12.2018
 * Time: 15:49
 */

namespace Oforge\Engine\Modules\TemplateEngine\Core\Twig;

use Twig_Environment;
use Twig_Extension;
use Twig_Function;
use Twig_TemplateWrapper;

/**
 * Class TwigOforgeDebugExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Core\Twig
 */
class TwigOforgeDebugExtension extends Twig_Extension {

    /**
     * @inheritDoc
     */
    public function getFunctions() {
        return [
            new Twig_Function('o_dump', [$this, 'oforgeVarDump'], [
                'is_safe'           => ['html'],
                'needs_context'     => true,
                'needs_environment' => true,
            ]),
            new Twig_Function('o_print', [$this, 'oforgeVarPrint'], [
                'is_safe'           => ['html'],
                'needs_context'     => true,
                'needs_environment' => true,
            ]),
        ];
    }

    /**
     * @param Twig_Environment $env
     * @param $context
     * @param mixed ...$vars
     *
     * @return string|void
     */
    public function oforgeVarDump(Twig_Environment $env, $context, ...$vars) {
        if (!$env->isDebug()) {
            return;
        }
        if (!$vars) {
            $vars = [];
            foreach ($context as $key => $value) {
                if (!$value instanceof Twig_TemplateWrapper) {
                    $vars[$key] = $value;
                }
            }

            return "<script>console.log(" . json_encode($vars) . ") </script>";
        } else {
            return "<script>console.log(" . json_encode(...$vars) . ") </script>";
        }
    }

    /**
     * @param Twig_Environment $env
     * @param $context
     * @param mixed ...$vars
     *
     * @return string|void
     */
    public function oforgeVarPrint(Twig_Environment $env, $context, ...$vars) {
        if (!$env->isDebug()) {
            return;
        }
        if (!$vars) {
            $vars = [];
            foreach ($context as $key => $value) {
                if (!$value instanceof Twig_TemplateWrapper) {
                    $vars[$key] = $value;
                }
            }
        }
        if (is_array($vars)) {
            array_walk_recursive($vars, function (&$item) {
                if ($item === null) {
                    $item = 'null';
                } elseif (is_bool($item)) {
                    $item = $item ? 'true' : 'false';
                }
            });
        }
        echo '<pre>';
        print_r($vars);
        echo '</pre>';
    }

}
