<?php

namespace ProductPlacement\Twig;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use ProductPlacement\Services\ProductPlacementService;
use Twig_Extension;
use Twig_ExtensionInterface;
use Twig_Function;

/**
 * Class ProductPlacementExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class ProductPlacementExtension extends Twig_Extension implements Twig_ExtensionInterface {

    /**
     * @inheritDoc
     */
    public function getFunctions() {
        return [
            new Twig_Function('product_placements', [$this, 'getProductPlacements']),
        ];
    }

    /**
     * Get product placements | advertisements based on some criteria
     *
     * @param int $amount
     * @param string|null $format
     *
     * @return array
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function getProductPlacements(int $amount, ?string $format = null) {
        $result = [];

        /** @var ProductPlacementService $productPlacementService */
        $productPlacementService = Oforge()->Services()->get('product.placement');
        $result = $productPlacementService->get($amount, $format);

        return $result;
    }
}
