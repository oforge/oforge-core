<?php

namespace Oforge\Engine\Modules\Core\Helper;

use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * Class TreeHelper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class TreeHelper {

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * @param AbstractModel[] $array
     * @param string $parentKey
     * @param string $rootValue
     * @param string $elementKey
     * @param string $childrenKey
     *
     * @return array
     */
    public static function modelArrayToTree($array, string $parentKey, string $rootValue, string $elementKey, string $childrenKey = 'children') : array {
        $grouped = [];
        if (sizeof($array) > 0) {
            foreach ($array as $item) {
                $itemData = $item->toArray();
                if (!isset($grouped[$itemData[$parentKey]])) {
                    $grouped[$itemData[$parentKey]] = [];
                }
                $grouped[$itemData[$parentKey]][] = $itemData;
            }

            return self::arrayToTreeSub($grouped[$rootValue], $grouped, $elementKey, $childrenKey);
        }

        return [];
    }

    /**
     * @param array $array
     * @param string $parentKey
     * @param string $rootValue
     * @param string $elementKey
     * @param string $childrenKey
     *
     * @return array
     */
    public static function arrayToTree(array $array, string $parentKey, string $rootValue, string $elementKey, string $childrenKey = 'children') : array {
        $grouped = [];
        foreach ($array as $item) {
            if (!isset($grouped[$item[$parentKey]])) {
                $grouped[$item[$parentKey]] = [];
            }
            $grouped[$item[$parentKey]][] = $item;
        }

        return self::arrayToTreeSub($grouped[$rootValue], $grouped, $elementKey, $childrenKey);
    }

    /**
     * @param array $root
     * @param array $grouped
     * @param string $elementKey
     * @param string $childrenKey
     *
     * @return array
     */
    private static function arrayToTreeSub(array $root, array $grouped, string $elementKey, string $childrenKey) {
        foreach ($root as $key => $groupItem) {
            $id = $groupItem[$elementKey];
            if (isset($grouped[$id])) {
                $groupItem[$childrenKey] = self::arrayToTreeSub($grouped[$id], $grouped, $elementKey, $childrenKey);
            }
            $root[$key] = $groupItem;
        }

        return $root;
    }

}
