<?php
/**
 * Created by PhpStorm.
 * User: motte
 * Date: 24.04.2019
 * Time: 16:08
 */

namespace FrontendUserManagement\Widgets;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\DashboardWidgetInterface;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class DashboardWidgetHandler
 *
 * @package FrontendUserManagement\Widgets
 */
class DashboardWidgetHandler extends AbstractDatabaseAccess implements DashboardWidgetInterface {

    public function __construct() {
        parent::__construct(["default" => User::class]);
    }

    /**
     * @inheritDoc
     * @throws ORMException
     */
    public function prepareData() : array {
        $users = $this->repository()->count([]);

        return ['count' => $users];
    }
}
