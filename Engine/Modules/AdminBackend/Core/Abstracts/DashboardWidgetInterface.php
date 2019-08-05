<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Abstracts;

use Exception;

/**
 * Interface DashboardWidgetInterface
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Widgets
 */
interface DashboardWidgetInterface {
    /**
     * @return array
     * @throws Exception
     */
    public function prepareData() : array;
}
