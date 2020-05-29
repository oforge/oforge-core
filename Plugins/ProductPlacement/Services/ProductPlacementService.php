<?php

namespace ProductPlacement\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use ProductPlacement\Models\ProductPlacement;
use ProductPlacement\Models\ProductPlacementTag;

/**
 * Class ProductPlacementService
 *
 * @package ProductPlacement\Services
 */
class ProductPlacementService extends AbstractDatabaseAccess {

    /**
     * ProductPlacementService constructor.
     */
    public function __construct() {
        parent::__construct(['default' => ProductPlacement::class, 'tags' => ProductPlacementTag::class]);
    }

    /**
     * @param int $amount
     * @param string[]|null $tags
     *
     * @return array|mixed
     * @throws ORMException
     */
    public function get(int $amount, $tags = null) {
        $result = [];
        /** @var ProductPlacement[] $productPlacements */
        $productPlacements = $this->repository()->findAll();
        if ($tags !== null && !empty($tags)) {
            $expectedTagIds = $this->findOrCreateTags($tags);
            foreach ($productPlacements as $productPlacement) {
                $add         = false;
                $currentTags = $productPlacement->getTags();
                if (isset($currentTags)) {
                    $add             = true;
                    $currentTagIdMap = array_fill_keys($currentTags, 1);
                    foreach ($expectedTagIds as $expectedTagId) {
                        if (!isset($currentTagIdMap[$expectedTagId])) {
                            $add = false;
                            break;
                        }
                    }
                }
                if ($add) {
                    $result[] = $productPlacement->toArray();
                }
            }
        }
        shuffle($result);

        return array_slice($result, 0, $amount);
    }

    /**
     * @return array
     * @throws ORMException
     */
    public function getAllTags() {
        $result = [];
        /** @var ProductPlacementTag[] $tags */
        $tags = $this->repository('tags')->findAll();
        foreach ($tags as $tag) {
            //$result[$tag->getId()] = ['id' => $tag->getId(), 'name' => $tag->getName()];
            $result[$tag->getId()] = $tag->getName();
            // array_push($result, $tag->toArray());
        }

        return $result;
    }

    /**
     * @param string[] $tags
     *
     * @return array
     * @throws ORMException
     */
    private function findOrCreateTags($tags) : array {
        $result = [];
        foreach ($tags as $tag) {
            $entity = $this->repository('tags')->findOneBy(['name' => $tag]);
            if ($entity === null) {
                $entity = new ProductPlacementTag();
                $entity->setName($tag);
                $this->entityManager()->create($entity);
            }
            $result[] = $entity->getId();
        }

        return $result;
    }
}
