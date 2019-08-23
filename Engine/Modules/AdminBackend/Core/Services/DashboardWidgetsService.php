<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\DashboardWidgetInterface;
use Oforge\Engine\Modules\AdminBackend\Core\Enums\DashboardWidgetPosition;
use Oforge\Engine\Modules\AdminBackend\Core\Models\DashboardWidget;
use Oforge\Engine\Modules\AdminBackend\Core\Models\UserDashboardWidget;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class DashboardWidgetsService
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Services
 */
class DashboardWidgetsService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([
            'default' => DashboardWidget::class,
            'user'    => UserDashboardWidget::class,
        ]);
    }

    /**
     * @param string $widgetName
     *
     * @return DashboardWidget|null
     */
    public function getDashboardWidgetByName(string $widgetName) : ?DashboardWidget {
        /** @var DashboardWidget $widget */
        try {
            $widget = $this->repository()->findOneBy(['name' => $widgetName]);
        } catch (ORMException $exception) {
            Oforge()->Logger()->logException($exception);
            $widget = null;
        }

        return $widget;
    }

    /**
     * @param array $widget
     *
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     */
    public function install(array $widget) {
        if ($this->isValid($widget)) {
            // Check if the element is already within the system
            $element = $this->getDashboardWidgetByName($widget['name']);
            if (!isset($element)) {
                if (isset($widget['label'])) {
                    if (is_array($widget['label'])) {
                        $labelKey = 'backend_dashboard_widget_' . $widget['name'];
                        I18N::translate($labelKey, $widget['label']);
                        $widget['label'] = $labelKey;
                    } else {
                        I18N::translate($widget['label'], $widget['label']);
                    }
                }
                $instance = DashboardWidget::create($widget);
                $this->entityManager()->create($instance);
            }
        }
    }

    /**
     * @param string $widgetName
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function uninstall(string $widgetName) {
        $dashboardWidget = $this->getDashboardWidgetByName($widgetName);
        if (isset($dashboardWidget)) {
            $this->entityManager()->remove($dashboardWidget);
        }
    }

    /**
     * @param string $widgetName
     *
     * @throws ORMException
     */
    public function activate(string $widgetName) {
        $this->setActive($widgetName, true);
    }

    /**
     * @param string $widgetName
     *
     * @throws ORMException
     */
    public function deactivate(string $widgetName) {
        $this->setActive($widgetName, false);
    }

    /**
     * Get widgets for current user.
     *
     * @param string|int|null $userID
     * @param bool $forDashboard
     *
     * @return array
     * @throws Exception
     */
    public function getUserWidgets($userID, bool $forDashboard = true) : array {
        $result = [];
        try {
            /** @var DashboardWidget[] $widgets */
            $widgets = $this->repository()->findBy(['active' => true]);
            foreach ($widgets as $widget) {
                $widgetData = $widget->toArray();

                $widgetData['order'] = 0;

                $result[$widget->getId()] = $widgetData;
            }
            if ($userID !== null) {
                /** @var UserDashboardWidget[] $widgets */
                $widgets = $this->repository('user')->findBy(['userId' => $userID]);
                foreach ($widgets as $widget) {
                    $widgetData = &$result[$widget->getWidgetId()];
                    $tmp        = $widget->toArray();
                    if ($forDashboard && !$tmp['active']) {
                        unset($result[$widget->getWidgetId()]);
                    }
                    $widgetData['active']   = $tmp['active'];
                    $widgetData['order']    = $tmp['order'];
                    $widgetData['cssClass'] = empty($tmp['cssClass']) ? $widgetData['cssClass'] : $tmp['cssClass'];
                    $widgetData['position'] = empty($tmp['position']) ? $widgetData['position'] : $tmp['position'];
                }
            }
        } catch (ORMException $exception) {
        }
        $tmp = $result;
        usort($tmp, function ($a, $b) {
            return $a['order'] > $b['order'];
        });
        $result = [
            DashboardWidgetPosition::TOP    => [],
            DashboardWidgetPosition::LEFT   => [],
            DashboardWidgetPosition::RIGHT  => [],
            DashboardWidgetPosition::BOTTOM => [],
        ];
        foreach ($tmp as $widget) {
            $handlerClass = $widget['handler'];
            if (!empty($handlerClass) && is_subclass_of($handlerClass, DashboardWidgetInterface::class)) {
                /** @var DashboardWidgetInterface $handler */
                $handler        = new $handlerClass();
                $widget['data'] = $handler->prepareData();
            }
            $result[$widget['position']][] = $widget;
        }

        return $result;
    }

    /**
     * @param array $data
     */
    public function saveUserSettings(array $data) {
        $user = Oforge()->View()->get('user');
        if (!isset($user['id'])) {
            return;
        }
        foreach ($data as $widgetID => $userSettings) {
            $dataKeys       = ['active', 'order', 'position', 'cssClass'];
            $userSettings   = ArrayHelper::filterByKeys($dataKeys, $userSettings);
            if (isset($userSettings['order']) && $userSettings['order'] === '') {
                $userSettings['order'] = 0;
            }
            $baseWidgetData = [
                'widgetId' => $widgetID,
                'userId'   => $user['id'],
            ];
            /** @var UserDashboardWidget|null $userDashboardWidget */
            try {
                $userDashboardWidget = $this->repository('user')->findOneBy($baseWidgetData);
                if (isset($userDashboardWidget)) {
                    // Oforge()->Logger()->get()->debug('x1', $userSettings);
                    $userDashboardWidget->fromArray($userSettings);
                    $this->entityManager()->update($userDashboardWidget);
                } else {
                    /** @var DashboardWidget|null $dashboardWidget */
                    $dashboardWidget     = $this->repository()->findOneBy(['id' => $widgetID, 'active' => true]);
                    $tmp                 = $dashboardWidget->toArray();
                    $tmp                 = ArrayHelper::filterByKeys($dataKeys, $tmp);
                    $userDashboardWidget = UserDashboardWidget::create($baseWidgetData);
                    $userDashboardWidget->fromArray($tmp);
                    $userDashboardWidget->fromArray($userSettings);
                    $this->entityManager()->create($userDashboardWidget);
                }
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }
    }

    /**
     * @param $userId
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @deprecated
     */
    public function initUserWidgets($userId) {
        /** @var DashboardWidget[] $widgets */
        $widgets = $this->repository()->findAll();

        $position = 0;
        foreach ($widgets as $widget) {
            $userWidget = UserDashboardWidget::create([
                'userId'   => $userId,
                'widgetId' => $widget->getId(),
                'order'    => $position++,
                'position' => $widget->getPosition(),
            ]);
            $this->entityManager()->create($userWidget, false);
        }

        if (count($widgets) > 0) {
            $this->entityManager()->flush();
        }
    }

    /**
     * @param $userId
     * @param $data
     *
     * @throws ORMException
     * @deprecated
     */
    public function updateUserWidgets($userId, $data) {
        //remove and add currently not possible
        $widgets = $this->repository('user')->findBy(['userId' => $userId], ['order' => 'ASC']);

        /**
         * @var $widget UserDashboardWidget
         */
        foreach ($widgets as $widget) {
            $id = $widget->getWidgetID();
            if (isset($data[$id]) && isset($data[$id]['site']) && isset($data[$id]['order'])) {
                $widget->setPosition($data[$id]['site']);
                $widget->setOrder($data[$id]['order']);
                $this->entityManager()->update($widget);
            }
        }

    }

    /**
     * @param string $widgetName
     * @param bool $active
     *
     * @throws ORMException
     */
    protected function setActive(string $widgetName, bool $active) {
        $dashboardWidget = $this->getDashboardWidgetByName($widgetName);
        if (isset($dashboardWidget)) {
            $dashboardWidget->setActive($active);
            $this->entityManager()->update($dashboardWidget);
        }
    }

    /**
     * @param array $config
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     */
    private function isValid(array $config) : bool {
        // Check if required keys are within the options
        $keys = ['name', 'template', 'handler'];
        foreach ($keys as $key) {
            if (!isset($config[$key])) {
                throw new ConfigOptionKeyNotExistException($key);
            }
        }
        //Check if correct type are set
        $keys = ['name', 'template', 'handler', 'name', 'position', 'cssClass'];
        foreach ($keys as $key) {
            if (isset($config[$key]) && !is_string($config[$key])) {
                throw new InvalidArgumentException('"$key" value should be of type string.');
            }
        }
        if (isset($config['handler']) && !empty($config['handler']) && !is_subclass_of($config['handler'], DashboardWidgetInterface::class)) {
            throw new InvalidArgumentException('"handler" value should be subclass of DashboardWidgetInterface.');
        }
        if (isset($config['label'])) {
            if (!is_string($config['label']) && !is_array($config['label'])) {
                throw new InvalidArgumentException('"label" value should be of type i18n label string or array with languageIso=>defaultValue.');
            }
        }

        return true;
    }

}
