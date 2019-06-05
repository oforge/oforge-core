<?php

namespace Insertion\Services;

use Insertion\Models\Insertion;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class InsertionSupplierService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default'                => Insertion::class,
        ]);
    }
    public function getSupplierInsertions($userId) {
        $insertions = $this->repository('default')->findBy(['user' => $userId]);
        return $insertions;
    }
}