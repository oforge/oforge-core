<?php
/**
 * Created by PhpStorm.
 * User: motte
 * Date: 21.06.2019
 * Time: 09:50
 */

namespace Insertion\Widgets;

use Insertion\Models\Insertion;
use Oforge\Engine\Modules\AdminBackend\Core\Widgets\DashboardWidgetInterface;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class InsertionCountWidget extends AbstractDatabaseAccess implements DashboardWidgetInterface {

    public function __construct() {
        parent::__construct(["default" => Insertion::class]);
    }

    function getData() : array {
        $users = $this->repository()->count(["deleted" => false, "moderation" => true, "active" => true]);

        return ["count" => $users];
    }
}