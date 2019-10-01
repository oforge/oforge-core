<?php

namespace ProductPlacement\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use ProductPlacement\Models\ProductPlacement;

class ProductPlacementService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct(['default' => ProductPlacement::class]);
    }

    /**
     * @param int $amount
     * @param string|null $format
     *
     * @return array|mixed
     * @throws ORMException
     */
    public function get(int $amount, ?string $format = null) {
        $queryBuilder = $this->repository()->createQueryBuilder('pp');
        if ($format !== null && strlen($format) > 0) {
            $queryBuilder = $queryBuilder->where('pp.format = :format')->setParameter(':format', $format);
        }
        $query  = $queryBuilder->select()->getQuery();
        $result = $query->getArrayResult();
        shuffle($result);
        return array_slice($result,0, $amount);
    }
}
