<?php

namespace FrontendUserManagement\Services;

use FrontendUserManagement\Models\FindyourhorseUser;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class FindYourhorseService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct(['default' => FindyourhorseUser::class]);
    }

    public function createFindyourhorseUser(string $fyhMail, User $user) {
        $fyhUser = new FindyourhorseUser();
        $fyhUser->setFyhMail($fyhMail)->setUser($user);
        $this->entityManager()->create($fyhUser);
    }
}
