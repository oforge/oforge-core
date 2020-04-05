<?php

namespace TestMail\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use ProductPlacement\Models\ProductPlacement;
use ProductPlacement\Models\ProductPlacementTag;

class PayPalService {

    public function createOrder($order, $user) {
    }

    public function confirmOrder($order, $user) {
    }

    public function createRecurringOrder($order, $user) {
        $agreement = new \PayPal\Api\Agreement();

    }
}
