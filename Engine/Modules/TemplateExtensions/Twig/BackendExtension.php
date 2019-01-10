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

class BackendExtension extends Twig_Extension implements \Twig_ExtensionInterface
{
    public function getFunctions()
    {
        return array(
            new Twig_Function('backend_sidebar_navigation', array($this, 'get_sidebar_navigation')),
            new Twig_Function('backend_breadcrumbs', array($this, 'get_breadcrumbs')),
            new Twig_Function('backend_breadcrumbs_map', array($this, 'get_breadcrumbs_map')),
            new Twig_Function('backend_has_visible_children', array($this, 'get_visible_navigation_children')),
        );
    }

    public function get_sidebar_navigation()
    {
        $configService = Oforge()->Services()->get("backend.navigation");

        return $configService->get();
    }

    public function get_breadcrumbs(...$vars)
    {
        $configService = Oforge()->Services()->get("backend.navigation");

        if (isset($vars) && sizeof($vars) == 1) {
            return $configService->breadcrumbs($vars[0]);
        }

        return [];
    }

    public function get_breadcrumbs_map(...$vars)
    {
        $configService = Oforge()->Services()->get("backend.navigation");

        if (isset($vars) && sizeof($vars) == 1) {
            $result = $configService->breadcrumbs($vars[0]);
            $output = [];
            foreach ($result as $item) {
                if (isset($item["name"])) {
                    $output[$item["name"]] = 1;
                }
            }

            return $output;
        }

        return [];
    }

    public function get_visible_navigation_children(...$vars)
    {
        if (isset($vars) && sizeof($vars) == 1 && is_array($vars[0])) {
            $contains = false;
            foreach ($vars[0] as $item) {
                if (isset($item["visible"]) && $item["visible"] == true) {
                    $contains = true;
                    break;
                }
            }

            return $contains;
        }

        return false;
    }
}
