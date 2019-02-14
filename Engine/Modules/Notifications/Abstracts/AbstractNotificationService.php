<?php

namespace Oforge\Engine\Modules\Notifications\Abstracts;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

abstract class AbstractNotificationService extends AbstractDatabaseAccess {
    const ALL    = "all";
    const UNSEEN = "unseen";
    const SEEN   = "seen";

    public function __construct($models) {
        parent::__construct($models);
    }

    abstract function getNotifications($userId, $selector = AbstractNotificationService::ALL);

    abstract function addNotification($userId, $type, $message, $link);

    abstract function markAsSeen($id);
}
