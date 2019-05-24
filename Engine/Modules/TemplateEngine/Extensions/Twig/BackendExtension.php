<?php

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Twig;

use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\UserFavoritesService;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Notifications\Abstracts\AbstractNotificationService;
use Oforge\Engine\Modules\Notifications\Services\BackendNotificationService;
use Twig_Extension;
use Twig_ExtensionInterface;
use Twig_Function;

class BackendExtension extends Twig_Extension implements Twig_ExtensionInterface {
    public function getFunctions() {
        return [
            new Twig_Function('backend_sidebar_navigation', [$this, 'get_sidebar_navigation']),
            new Twig_Function('backend_topbar_navigation', [$this, 'get_topbar_navigation']),
            new Twig_Function('backend_breadcrumbs', [$this, 'get_breadcrumbs']),
            new Twig_Function('backend_breadcrumbs_map', [$this, 'get_breadcrumbs_map']),
            new Twig_Function('backend_has_visible_children', [$this, 'get_visible_navigation_children']),
            new Twig_Function('backend_notifications', [$this, 'get_backend_notifications']),
            new Twig_Function('backend_favorites', [$this, 'get_favorites']),
            new Twig_Function('backend_widgets', [$this, 'get_widgets']),
            new Twig_Function('backend_widget_data', [$this, 'get_widgets_data']),
            new Twig_Function('isFavorite', [$this, 'is_favorite']),
        ];
    }

    /**
     * @return array|object[]
     * @throws ServiceNotFoundException
     */
    public function get_backend_notifications() {
        /** @var $authService AuthService */
        $authService = Oforge()->Services()->get("auth");
        $user        = $authService->decode($_SESSION["auth"]);
        if (isset($user) && isset($user['id'])) {
            /** @var BackendNotificationService $notificationService */
            $notificationService = Oforge()->Services()->get("backend.notifications");

            return $notificationService->getNotifications($user['id'], AbstractNotificationService::UNSEEN);
        }

        return [];
    }

    /**
     * @return mixed
     * @throws ServiceNotFoundException
     */
    public function get_sidebar_navigation() {
        /** @var $sidebarNavigation BackendNavigationService */
        $sidebarNavigation = Oforge()->Services()->get("backend.navigation");

        return $sidebarNavigation->get("sidebar");
    }

    /**
     * @return mixed
     * @throws ServiceNotFoundException
     */
    public function get_topbar_navigation() {
        /** @var $topbarNavigation BackendNavigationService */
        $topbarNavigation = Oforge()->Services()->get("backend.navigation");
        $topbarData       = $topbarNavigation->get("topbar");

        foreach ($topbarData as $key => $element) {
            $nameSplit = explode("_", $element['name']);
            foreach ($nameSplit as $index => $subString) {
                $nameSplit[$index] = ucfirst($subString);
            }
            $element['filename'] = implode('', $nameSplit);
            $topbarData[$key]    = $element;
        }

        return $topbarData;
    }

    /**
     * @param mixed ...$vars
     *
     * @return array
     * @throws ServiceNotFoundException
     */
    public function get_breadcrumbs(...$vars) {
        /** @var $sidebarNavigation BackendNavigationService */
        $sidebarNavigation = Oforge()->Services()->get("backend.navigation");

        if (isset($vars) && sizeof($vars) == 1) {
            return $sidebarNavigation->breadcrumbs($vars[0]);
        }

        return [];
    }

    /**
     * @param mixed ...$vars
     *
     * @return array
     * @throws ServiceNotFoundException
     */
    public function get_breadcrumbs_map(...$vars) {
        /** @var $sidebarNavigation BackendNavigationService */
        $sidebarNavigation = Oforge()->Services()->get("backend.navigation");

        if (isset($vars) && sizeof($vars) == 1) {
            $result = $sidebarNavigation->breadcrumbs($vars[0]);
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
     * @throws ServiceNotFoundException
     */
    public function is_favorite(...$vars) {
        /** @var $authService AuthService */
        $authService = Oforge()->Services()->get("auth");
        $user        = $authService->decode($_SESSION["auth"]);
        if (isset($user) && isset($user['id']) && sizeof($vars) == 1) {
            /** @var UserFavoritesService $favoritesService */
            $favoritesService = Oforge()->Services()->get("backend.favorites");

            return $favoritesService->isFavorite($user['id'], $vars[0]);
        }

        return false;
    }

    /**
     * @param mixed ...$vars
     *
     * @return array
     * @throws ServiceNotFoundException
     */
    public function get_favorites(...$vars) {
        /** @var $authService AuthService */
        $authService = Oforge()->Services()->get("auth");
        $user        = $authService->decode($_SESSION["auth"]);
        if (isset($user) && isset($user['id'])) {
            /** @var UserFavoritesService $favoritesService */
            $favoritesService = Oforge()->Services()->get("backend.favorites");

            $instances = $favoritesService->getAll($user['id']);

            $result = [];

            foreach ($instances as $instance) {
                array_push($result, $instance->toArray());
            }

            return $result;
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

    /**
     * @return array|object[]
     * @throws ServiceNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function get_widgets() {
        /** @var $authService AuthService */
        $authService = Oforge()->Services()->get("auth");
        $user        = $authService->decode($_SESSION["auth"]);
        if (isset($user) && isset($user['id'])) {
            /** @var DashboardWidgetsService $widgetService */
            $widgetService = Oforge()->Services()->get("backend.dashboard.widgets");

   //         return $widgetService->getUserWidgets($user['id']);
        }

        return [];
    }

    /**
     * @return array|object[]
     * @throws ServiceNotFoundException
     */
    public function get_widgets_data(...$vars) {
        /** @var $authService AuthService */
        $authService = Oforge()->Services()->get("auth");
        $user        = $authService->decode($_SESSION["auth"]);
        if (isset($user) && isset($user['id']) && isset($vars) && sizeof($vars) > 0) {
            /** @var DashboardWidgetsService $widgetService */
            $widgetService = Oforge()->Services()->get("backend.dashboard.widgets");

            return $widgetService->getWidgetsData($vars[0]);
        }

        return [];
    }
}
