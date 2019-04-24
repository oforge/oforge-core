<?php
/**
 * Created by PhpStorm.
 * User: Matthäus Schmedding
 * Date: 24.04.2019
 * Time: 16:03
 */

namespace Oforge\Engine\Modules\AdminBackend\Core\Widgets;

interface DashboardWidgetInterface {
     function getData(): array;
}