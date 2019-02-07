<?php

namespace Oforge\Engine\Modules\TemplateExtensions\Twig;

use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Notifications\Services\BackendNotificationService;
use Twig_Extension;
use Twig_Function;

class BackendExtension extends Twig_Extension implements \Twig_ExtensionInterface {
    public function getFunctions() {
        return [
            new Twig_Function('backend_sidebar_navigation', [$this, 'get_sidebar_navigation']),
            new Twig_Function('backend_breadcrumbs', [$this, 'get_breadcrumbs']),
            new Twig_Function('backend_breadcrumbs_map', [$this, 'get_breadcrumbs_map']),
            new Twig_Function('backend_has_visible_children', [$this, 'get_visible_navigation_children']),
            new Twig_Function('backend_notifications', [$this, 'get_backend_notifications']),
        ];
    }

    /**
     * @return array|object[]
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function get_backend_notifications() {
        /** @var $authService AuthService */
        $authService = Oforge()->Services()->get("auth");
        $user        = $authService->decode($_SESSION["auth"]);
        if (isset($user) && isset($user['id'])) {
            /** @var BackendNotificationService $notificationService */
            $notificationService = Oforge()->Services()->get("backend.notifications");

            return $notificationService->getNotifications($user['id']);
        }

        return [];
    }

    /**
     * @return mixed
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function get_sidebar_navigation() {
        $configService = Oforge()->Services()->get("backend.navigation");

        return $configService->get();
    }

    /**
     * @param mixed ...$vars
     *
     * @return array
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function get_breadcrumbs(...$vars) {
        $configService = Oforge()->Services()->get("backend.navigation");

        if (isset($vars) && sizeof($vars) == 1) {
            return $configService->breadcrumbs($vars[0]);
        }

        return [];
    }

    /**
     * @param mixed ...$vars
     *
     * @return array
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function get_breadcrumbs_map(...$vars) {
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

    /**
     * @param mixed ...$vars
     *
     * @return bool
     */
    public function get_visible_navigation_children(...$vars) {
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
