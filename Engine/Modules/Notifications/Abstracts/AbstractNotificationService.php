<?php

namespace Oforge\Engine\Modules\Notifications\Abstracts;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

abstract class AbstractNotificationService extends AbstractDatabaseAccess
{
    public const ALL = "all";
    public const UNSEEN = "unseen";
    public const SEEN = "seen";

    public function __construct($models)
    {
        parent::__construct($models);
    }

    abstract public function addNotification(int $userId, string $type, string $message, ?string $link = null);

    abstract public function getUserNotifications(int $userId, string $selector = AbstractNotificationService::ALL) : array;

    abstract public function markAsSeen(int $id) : bool;

}
