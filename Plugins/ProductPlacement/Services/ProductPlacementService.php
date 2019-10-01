<?php

namespace ProductPlacement\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use ProductPlacement\Models\ProductPlacement;
use ProductPlacement\Models\ProductPlacementTag;

class ProductPlacementService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct(['default' => ProductPlacement::class, 'tags' => ProductPlacementTag::class]);
    }

    /**
     * @param int $amount
     * @param array|null $tags
     *
     * @return array|mixed
     * @throws ORMException
     */
    public function get(int $amount, ?array $tags = null) {
        $queryBuilder = $this->repository()->createQueryBuilder('pp');
        if ($tags !== null && sizeof($tags) > 0) {
            $this->findOrCreateTags($tags);
            $queryBuilder = $queryBuilder->where('pp.tags = :tags')->setParameter('tags', $tags);
        }
        $query  = $queryBuilder->select()->getQuery();
        $result = $query->getArrayResult();
        shuffle($result);
        return array_slice($result,0, $amount);
    }

    private function findOrCreateTags(array $tags) {
        // TODO: find or create every tag
    }
}
