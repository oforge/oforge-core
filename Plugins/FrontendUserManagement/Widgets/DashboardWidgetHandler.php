<?php
/**
 * Created by PhpStorm.
 * User: motte
 * Date: 24.04.2019
 * Time: 16:08
 */

namespace FrontendUserManagement\Widgets;

use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\AdminBackend\Core\Widgets\DashboardWidgetInterface;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class DashboardWidgetHandler extends AbstractDatabaseAccess implements DashboardWidgetInterface {

    public function __construct() {
        parent::__construct(["default" => User::class]);
    }

    function getData() : array {
        $users = $this->repository()->count([]);

        return ["count" => $users];
        // TODO: Implement getData() method.
    }
}