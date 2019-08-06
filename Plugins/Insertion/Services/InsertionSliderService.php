<?php

namespace Insertion\Services;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
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

    /**
     * Get a random list of insertions if it is bigger than MAX_INSERTIONS
     *
     * @return array
     * @throws ORMException
     */
    public function getRandomInsertions() {
        $result = [];

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->entityManager()
                             ->createQueryBuilder()
                             ->select('i.id')
                             ->from("Insertion\Models\Insertion", 'i')
                             ->where('i.deleted = 0')
                             ->andWhere('i.active = 1')
                             ->andWhere('i.moderation = 1');

       // "deleted" => false, "active" => true, "moderation" => true

        /** @var Query $query */
        $query = $queryBuilder->getQuery();

        //Get all insertion IDs
        $ids = $query->getScalarResult();

        if (sizeof($ids) > 0) {
            //Select a bunch of random insertions from the Database
            $randomKeys = array_rand($ids, sizeof($ids) < self::MAX_INSERTIONS ? sizeof($ids) : self::MAX_INSERTIONS);

            // If theres only one item, array_rand returns the key which is 0.
            // Make this an array element so that foreach still works...
            if ($randomKeys === 0) {
                $randomKeys = [0];
            }
            foreach ($randomKeys as $randomKey) {
                /** @var Insertion $insertion */
                $insertion = $this->repository()->findOneBy(["id" => $ids[$randomKey]], null);
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
     * @param int|null $notId
     *
     * @return Insertion[]
     * @throws ORMException
     * @throws DBALException
     */
    public function getRandomInsertion(int $limit, int $insertionType = null, int $notId = null) {
        $params = [];
        if (is_null($insertionType)) {
            $query = "
                    SELECT id
                    FROM oforge_insertion
                    WHERE active IS TRUE AND
                          deleted IS FALSE AND
                          moderation IS TRUE
                    ORDER BY RAND()
                    LIMIT " . $limit;

        } elseif (is_null($notId)) {
            $query = "
                    SELECT id
                    FROM oforge_insertion 
                    WHERE active IS TRUE AND
                          deleted IS FALSE AND
                          moderation IS TRUE AND
                          insertion_type_id = ?
                    ORDER BY RAND()
                    LIMIT " . $limit;

            $params[] = $insertionType;
        } else {
            $query = "
                    SELECT id
                    FROM oforge_insertion
                    WHERE active IS TRUE AND
                          deleted IS FALSE AND
                          moderation IS TRUE AND
                          id != ? AND
                          insertion_type_id = ?
                    ORDER BY RAND()
                    LIMIT " . $limit;

            $params[] = $notId;
            $params[] = $insertionType;
        }


        $sqlResult = $this->entityManager()->getEntityManager()->getConnection()->executeQuery($query, $params);

        $ids = $sqlResult->fetchAll();

        $findIds = [];

        foreach ($ids as $id) {
            $findIds[] = $id["id"];
        }

        $data = [];
        if (sizeof($findIds) > 0) {
            /** @var Insertion[] $insertions */
            $insertions = $this->repository()->findBy(["id" => $findIds]);
            foreach ($insertions as $insertion) {
                if ($insertion != null) {
                    $data[] = $insertion->toArray(3);
                }
            }
        }

        return $data;
    }

    public function getPremiumInsertions() {
        /**
         * TODO:
         * Implement getPremiumInsertions()
         */

        return null;
    }
}
