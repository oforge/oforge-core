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

class TwigOforgeDebugExtension extends Twig_Extension {
    public function getFunctions() {
        return [
            new Twig_Function('o_dump', [$this, 'oforge_var_dump'], [
                    'is_safe'           => ['html'],
                    'needs_context'     => true,
                    'needs_environment' => true,
                ]),
        ];
    }

    public function oforge_var_dump(Twig_Environment $env, $context, ...$vars) {
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
}
