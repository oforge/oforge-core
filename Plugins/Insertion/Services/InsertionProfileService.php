<?php

namespace Insertion\Services;

use Insertion\Models\InsertionProfile;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class InsertionProfileService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default'                 => InsertionProfile::class,
        ]);
    }


    public function get(int $user) {

    }

    public function update(int $user, array $params) {

    }

}
