<?php

namespace Oforge\Engine\Modules\Notifications\Abstracts;

abstract class AbstractNotificationService {
    const ALL = "all";
    const UNSEEN = "unseen";
    const SEEN = "seen";

    abstract function getNotifications($userId, $selector = AbstractNotificationService::ALL);

    abstract function addNotification($userId, $type, $message, $link);

    abstract function markAsSeen($id);
}
