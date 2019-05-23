<?php

namespace Insertion\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Insertion\Models\AttributeKey;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Models\InsertionTypeGroup;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;

class InsertionListService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default'                => Insertion::class,
            'insertionTypeAttribute' => InsertionTypeAttribute::class,
            'group'                  => InsertionTypeGroup::class,
        ]);
    }

    public function search($typeId, $params): ?array {
        $page = isset($params["page"]) ? $params["page"] : 1;
        $pageSize = 20;


        return $_GET;

        //die();
     //   $this->repository()->findBy(["insertionType" => $typeId], [], $pageSize, ($page - 1) * $pageSize);
        return [];
    }

}
