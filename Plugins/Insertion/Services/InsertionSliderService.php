<?php

namespace Insertion\Services;

use Doctrine\ORM\Query;
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

        //Select a bunch of random insertions from the Database
        $randomKeys = array_rand($ids, sizeof($ids) < self::MAX_INSERTIONS ? sizeof($ids) : self::MAX_INSERTIONS);
        foreach ($randomKeys as $randomKey) {
            $insertion = $this->repository()->findOneBy(["id" => $ids[$randomKey], "deleted" => false, "active" => true, "moderation" => "true"], null, 1);
            if ($insertion != null) {
                array_push($result, $insertion->toArray(3));
            }
        }

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