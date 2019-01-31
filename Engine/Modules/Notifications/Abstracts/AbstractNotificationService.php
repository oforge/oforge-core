<?php

namespace Oforge\Engine\Modules\Notifications\Abstracts;

abstract class AbstractNotificationService {
    abstract function getNotifications($userId);

    abstract function addNotification($userId, $type, $message, $link);

    abstract function markAsSeen($id);
}
