<?php

namespace Insertion\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
            'key'                    => AttributeKey::class,
            'group'                  => InsertionTypeGroup::class,
        ]);
    }

    public function search($typeId, $params) : ?array {
        $page     = isset($params["page"]) ? $params["page"] : 1;
        $pageSize = 10;

        $attributeKeys = array_keys($_GET);
        $keys          = $this->repository("key")->findBy(["name" => $attributeKeys]);

        $result = ["filter" => [], "query" => []];

        $queryBuilder = $this->entityManager()->createQueryBuilder()->select('i')->from("Insertion\Models\Insertion", "i")->where("i.insertionType = :type");
        $queryBuilder->setParameter("type", $typeId);

        $keyCount       = 1;
        $attributeCount = 1;
        if (sizeof($result) > 0) {
            foreach ($keys as $attributeKey) {
                if (isset($_GET[$attributeKey->getName()])) {
                    $value                                      = $_GET[$attributeKey->getName()];
                    $result['filter'][$attributeKey->getName()] = $value;

                    if (is_array($value)) {
                        $firstV = true;
                        foreach ($value as $v) {
                            $queryBuilder->leftJoin("i.values", "v$attributeCount");
                            if ($firstV) {
                                $firstV = false;
                                $queryBuilder->andWhere("v$attributeCount.attributeKey = ?" . $keyCount . " and v$attributeCount.value = ?" . ($keyCount + 1)
                                                        . "");

                            } else {
                                $queryBuilder->orWhere("v$attributeCount.attributeKey = ?" . $keyCount . " and v$attributeCount.value = ?" . ($keyCount + 1)
                                                       . "");

                            }
                            $queryBuilder->setParameter($keyCount++, $attributeKey->getId());
                            $queryBuilder->setParameter($keyCount++, $v);

                            $attributeCount++;
                        }

                    } else {
                        $queryBuilder->leftJoin("i.values", "v$attributeCount");

                        $queryBuilder->andWhere("v$attributeCount.attributeKey = ?" . $keyCount . " and v$attributeCount.value = ?" . ($keyCount + 1) . "");

                        $queryBuilder->setParameter($keyCount++, $attributeKey->getId());
                        $queryBuilder->setParameter($keyCount++, $value);

                        $attributeCount++;
                    }
                }
            }
        }

        $query     = $queryBuilder->getQuery()->setFirstResult(($page - 1) * $pageSize)->setMaxResults($pageSize);
        $paginator = new Paginator($query, $fetchJoinCollection = true);

        $result["query"]["count"]     = $paginator->count();
        $result["query"]["pageSize"]  = $pageSize;
        $result["query"]["page"]      = $page;
        $result["query"]["pageCount"] = ceil((1.0) * $paginator->count() / $pageSize);
        $result["query"]["items"]     = [];

        foreach ($paginator as $item) {
            $result["query"]["items"][] = $item->toArray(3);
        }

        //TODO price range

        return $result;
    }

}
