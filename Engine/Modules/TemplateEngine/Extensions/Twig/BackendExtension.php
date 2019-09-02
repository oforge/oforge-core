<?php

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Twig;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\UserFavoritesService;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Notifications\Abstracts\AbstractNotificationService;
use Oforge\Engine\Modules\Notifications\Services\BackendNotificationService;
use Twig_Extension;
use Twig_ExtensionInterface;
use Twig_Function;

/**
 * Class BackendExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class BackendExtension extends Twig_Extension implements Twig_ExtensionInterface {
    public function getFunctions() {
        return [
            new Twig_Function('backend_sidebar_navigation', [$this, 'getSidebarNavigation']),
            new Twig_Function('backend_topbar_navigation', [$this, 'getTopbarNavigation']),
            new Twig_Function('backend_breadcrumbs', [$this, 'get_breadcrumbs']),
            new Twig_Function('backend_breadcrumbs_map', [$this, 'get_breadcrumbs_map']),
            new Twig_Function('backend_notifications', [$this, 'get_backend_notifications']),
            new Twig_Function('backend_favorites', [$this, 'get_favorites']),
            new Twig_Function('backend_dashboard_widgets', [$this, 'getDashboardWidgets']),
            new Twig_Function('isFavorite', [$this, 'is_favorite']),
        ];
    }

    /**
     * @return array|object[]
     * @throws ServiceNotFoundException
     */
    public function get_backend_notifications() {
        /** @var $authService AuthService */
        $authService = Oforge()->Services()->get('auth');
        $user        = $authService->decode($_SESSION['auth']);
        if (isset($user) && isset($user['id'])) {
            /** @var BackendNotificationService $notificationService */
            $notificationService = Oforge()->Services()->get('backend.notifications');

            return $notificationService->getNotifications($user['id'], AbstractNotificationService::UNSEEN);
        }

        return [];
    }

    /**
     * @return array
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function getSidebarNavigation() {
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');

        return $backendNavigationService->getNavigationTree('sidebar');
    }

    /**
     * @return array
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function getTopbarNavigation() {
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');

        $topbarData = $backendNavigationService->getNavigationTree('topbar');
        foreach ($topbarData as $key => $element) {
            $nameSplit = explode('_', $element['name']);
            $nameSplit = array_map('ucfirst', $nameSplit);

            $element['filename'] = implode('', $nameSplit);

            $topbarData[$key] = $element;
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
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');

        if (isset($vars) && sizeof($vars) == 1) {
            return $backendNavigationService->getBreadcrump($vars[0]);
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
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');

        if (isset($vars) && sizeof($vars) == 1) {
            $result = $backendNavigationService->getBreadcrump($vars[0]);
            $output = [];
            foreach ($result as $item) {
                if (isset($item['name'])) {
                    $output[$item['name']] = 1;
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
        $authService = Oforge()->Services()->get('auth');
        $user        = $authService->decode($_SESSION['auth']);
        if (isset($user) && isset($user['id']) && sizeof($vars) == 1) {
            /** @var UserFavoritesService $favoritesService */
            $favoritesService = Oforge()->Services()->get('backend.favorites');

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
        $authService = Oforge()->Services()->get('auth');
        $user        = $authService->decode($_SESSION['auth']);
        if (isset($user) && isset($user['id'])) {
            /** @var UserFavoritesService $favoritesService */
            $favoritesService = Oforge()->Services()->get('backend.favorites');

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
     * @param bool $forDashboard
     *
     * @return array
     * @throws ServiceNotFoundException
     */
    public function getDashboardWidgets(bool $forDashboard = true) {
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');

        $user = Oforge()->View()->get('user');
        if ($user != null) {
            $userID = ArrayHelper::get($user, 'id');
            return $dashboardWidgetsService->getUserWidgets($userID, $forDashboard);
        }

        return [];
    }

}
