<?php

namespace Insertion\Services;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Insertion\Models\AttributeKey;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Models\InsertionTypeGroup;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class InsertionSliderService extends AbstractDatabaseAccess {

    private const MAX_INSERTIONS = 20;

    public function __construct() {
        parent::__construct([
            'default'                => Insertion::class,
            'type'                   => InsertionType::class,
            'insertionTypeAttribute' => InsertionTypeAttribute::class,
            'key'                    => AttributeKey::class,
            'group'                  => InsertionTypeGroup::class,
        ]);
    }

    public function getRandomInsertions() {
        $result = [];

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->entityManager()->createQueryBuilder()->select('i.id')->from("Insertion\Models\Insertion", 'i');

        /** @var Query $query */
        $query = $queryBuilder->getQuery();

        //Get all insertion IDs
        $ids = array_column($query->getScalarResult(), "id");

        if (sizeof($ids) > 0) {
            //Select a bunch of random insertions from the Database
            $randomKeys = array_rand($ids, sizeof($ids) < self::MAX_INSERTIONS ? sizeof($ids) : self::MAX_INSERTIONS);
            // If theres only one item, array_rand returns the key which is 0.
            // Make this an array element so that foreach still works...
            if ($randomKeys === 0) {
                $randomKeys = [0];
            }
            foreach ($randomKeys as $randomKey) {
                $insertion = $this->repository()->findOneBy(["id" => $ids[$randomKey], "deleted" => false, "active" => true, "moderation" => true], null, 1);
                if ($insertion != null) {
                    array_push($result, $insertion->toArray(3));
                }
            }
        }

        return $result;
    }

    /**
     * @param int $limit
     * @param int|null $insertionType
     *
     * @return Insertion[]
     */
    public function getRandomInsertion(int $limit, int $insertionType = null) {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Insertion::class, 'i');
        $rsm->addFieldResult('i', 'id', 'id');
        $rsm->addFieldResult('i', 'price', 'price');
        $rsm->addFieldResult('i', 'created_at', 'createdAt');
        $rsm->addFieldResult('i', 'updated_at', 'updatedAt');
        $rsm->addFieldResult('i', 'tax', 'tax');
        $rsm->addFieldResult('i', 'active', 'deleted');
        $rsm->addFieldResult('i', 'moderation', 'moderation');
        $rsm->addMetaResult('i', 'insertion_user', 'insertion_user');
        $rsm->addMetaResult('i', 'insertion_type_id', 'insertion_type_id');

        if (is_null($insertionType)) {
            $query = $this->entityManager()->createNativeQuery("
            SELECT *
            FROM oforge_insertion 
            WHERE active IS TRUE AND
                  deleted IS FALSE AND
                  moderation IS TRUE
            ORDER BY RAND()
            LIMIT ?", $rsm);

            $query->setParameter(1, $limit);
        } else {
            $query = $this->entityManager()->createNativeQuery("
            SELECT *
            FROM oforge_insertion AS i
            WHERE active IS TRUE AND
                  deleted IS FALSE AND
                  moderation IS TRUE AND 
                  insertion_type_id = ?
            ORDER BY RAND()
            LIMIT ?", $rsm);

            $query->setParameter(1, $insertionType)->setParameter(2, $limit);
        }

        $result = $query->getResult();

        return $result;
    }

    public function getPremiumInsertions() {
        /**
         * TODO:
         * Implement getPremiumInsertions()
         */

        return null;
    }
}
