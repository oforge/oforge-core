<?php

namespace ProductPlacement\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
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
        $result = [];
        /** @var ProductPlacement[] $productPlacements */
        $productPlacements = $this->repository()->findAll();
        if ($tags !== null && sizeof($tags) > 0) {
            $tagIds = $this->findOrCreateTags($tags);
            foreach ($productPlacements as $productPlacement) {
                $found = true;
                foreach ($productPlacement->getTags() as $tag) {
                    if (!in_array($tag, $tagIds)) {
                        $found = false;
                        break;
                    }
                }

                if ($found) {
                    $result[] = $productPlacement->toArray();
                }
            }
        }
        shuffle($result);

        return array_slice($result, 0, $amount);
    }

    /**
     * @param array $tags
     *
     * @return array
     * @throws ORMException
     */
    private function findOrCreateTags(array $tags) : array {
        $result = [];
        $newTag = null;
        foreach ($tags as $tag) {
            $tagToFind = $this->repository('tags')->findOneBy(['name' => $tag]);
            if ($tagToFind === null) {
                $newTag = new ProductPlacementTag();
                $newTag->setName($tag);
                $this->entityManager()->create($newTag);
            }
            array_push($result, $this->repository('tags')->findOneBy(['name' => $tag])->getId());
        }

        return $result;
    }

    public function getAllTags() {
        $result = [];
        /** @var ProductPlacementTag[] $tags */
        $tags = $this->repository('tags')->findAll();
        foreach ($tags as $tag) {
            array_push($result, $tag->toArray());
        }

        return $result;
    }
}
