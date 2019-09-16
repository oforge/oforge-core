<?php

namespace Insertion\Services;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Exception;
use Insertion\Enum\AttributeType;
use Insertion\Models\AttributeKey;
use Insertion\Models\AttributeValue;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Models\InsertionTypeGroup;
use Insertion\Models\InsertionZipCoordinates;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Annotation\Cache\Cache;
use Oforge\Engine\Modules\Core\Annotation\Cache\CacheInvalidation;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Statics;
use function DI\value;

/**
 * Class InsertionListService
 *
 * @package Insertion\Services
 */
class InsertionListService extends AbstractDatabaseAccess
{
    public function __construct()
    {
        parent::__construct([
            'default' => Insertion::class,
            'type' => InsertionType::class,
            'insertionTypeAttribute' => InsertionTypeAttribute::class,
            'key' => AttributeKey::class,
            'group' => InsertionTypeGroup::class,
        ]);
    }

    /**
     * Search insertions by InsertionType and filter them by given params
     *
     * @param $typeId
     * @param $params
     *
     * @return array|null
     * @throws DBALException
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @Cache(slot="insertion", duration="T15M")
     */

    public function search($typeId, $params): ?array
    {
        $page = isset($params["page"]) ? $params["page"] : 1;
        $pageSize = isset($params["pageSize"]) ? $params["pageSize"] : 10;

        if (!isset($params['order'])) {
            $params['order'] = 'date_desc';
        }

        $order = 'id';
        $orderNative = 'id';
        $orderDir = 'asc';

        if (isset($params['order'])) {
            switch ($params['order']) {
                case 'price_asc':
                    $order = 'price';
                    $orderNative = 'price';
                    $orderDir = 'asc';
                    break;
                case 'price_desc':
                    $order = 'price';
                    $orderNative = 'price';
                    $orderDir = 'desc';
                    break;
                case 'date_asc':
                    $order = 'createdAt';
                    $orderNative = 'created_at';
                    $orderDir = 'asc';
                    break;
                case  'date_desc':
                    $order = 'createdAt';
                    $orderNative = 'created_at';
                    $orderDir = 'desc';
                    break;
            }
        }

        /** @var InsertionType $type */
        $typeAttributes = $this->repository("type")->find($typeId)->getAttributes();
        $keys = [];
        /**
         * @var $typeAttribute InsertionTypeAttribute
         */
        foreach ($typeAttributes as $typeAttribute) {
            $keys[] = $typeAttribute->getAttributeKey();
            if ($typeAttribute->getAttributeKey()->getValues() != null) {
                foreach ($typeAttribute->getAttributeKey()->getValues() as $value) {
                    $tmp = $this->getAttributeKeys($value);
                    if (sizeof($tmp) > 0) {
                        $keys = array_merge($keys, $tmp);
                    }
                }
            }
        }

        $result = ["filter" => [], "query" => [], 'order' => $params["order"]];

        $args = [];

        $sqlQuery = "select i.id from oforge_insertion i";

        $sqlQueryWhere = " where i.active = 1 and i.moderation = 1 and i.insertion_type_id = :type";

        $args["type"] = intval($typeId);

        // filter order
        // - price
        // - distance
        // - attributes
        /**
         * filter by price
         */
        if (isset($params["price"]) && is_array($params["price"])) {
            $min = null;
            $max = null;

            if (isset($params["price"]['min'])) {
                $sqlQueryWhere .= " and (i.price >= :min OR (i.min_price IS NOT NULL AND i.min_price >= :min))";
                $min = $params["price"]['min'];
                $args["min"] = $min;
                $result['filter']['price']['min'] = $min;
            }

            if (isset($params["price"]['max'])) {
                $sqlQueryWhere .= " and (i.price <= :max OR (i.min_price IS NOT NULL AND i.min_price <= :max))";
                $max = $params["price"]['max'];
                $args["max"] = $max;
                $result['filter']['price']['max'] = $max;
            }

            if (isset($params["price"]['min']) && isset($params["price"]['max']) && $min > $max) {
                $t = $min;
                $min = $max;
                $max = $t;
                $args["min"] = $min;
                $args["max"] = $max;
                $result['filter']['price']['min'] = $min;
                $result['filter']['price']['max'] = $max;
            }
        }

        /**
         * filter by distance
         */
        if (isset($params["zip"]) && isset($params["zip_range"]) && !empty($params["zip"]) && $params["zip_range"]) {
            $country = $params['country'] ?: "germany";

            $coordinates = Oforge()->Services()->get("insertion.zip")->get($params["zip"], $country);
            if ($coordinates != null) {
                $sqlQuery .= " left join oforge_insertion_contact contact on contact.insertion_id = i.id";
                $sqlQuery .= " left join oforge_insertion_zip_coordinates zip on zip.country = contact.country and zip.zip = contact.zip";

                $sqlQueryWhere .= " and ST_Distance_Sphere(POINT(zip.lng, zip.lat), POINT(:lng,:lat)) / 1000 <= :zip";

                $args["lng"] = $coordinates->getLng();
                $args["lat"] = $coordinates->getLat();
                $args["zip"] = $params["zip_range"];

            }

            $result['filter']['radius'] = [
                'zip' => $params["zip"],
                'zip_range' => $params["zip_range"],
                'country' => $params["country"],
            ];
        }

        if (sizeof($keys) > 0) {
            $attributeCount = 0;

            /** @var AttributeKey $attributeKey */
            foreach ($keys as $attributeKey) {
                $name = $attributeKey->getName();
                $name = str_replace(" ", "_", $name);

                if (isset($params[$name])) {
                    $value = $params[$name];
                    $result['filter'][$attributeKey->getName()] = is_array($value) ? array_unique($value) : $value;

                    if (is_array($value)) { //should always be a multi selection or a range component
                        $sqlQuery .= " left join oforge_insertion_insertion_attribute_value v$attributeCount on v$attributeCount.insertion_id = i.id and v$attributeCount.attribute_key = :v"
                            . $attributeCount . "key";
                        $args["v" . $attributeCount . "key"] = $attributeKey->getId();

                        switch ($attributeKey->getFilterType()) {
                            case AttributeType::RANGE:
                                $min = null;
                                $max = null;

                                $sqlQueryWhere .= " and v$attributeCount.attribute_key = :v" . $attributeCount . "key";

                                if (isset($value['min'])) {
                                    $sqlQueryWhere .= " and v$attributeCount.insertion_attribute_value >= :v" . $attributeCount
                                        . "value";
                                    $min = $value['min'];
                                    $args["v" . $attributeCount . "value"] = $min;
                                }
                                if (isset($value['max'])) {
                                    $sqlQueryWhere .= " and v$attributeCount.insertion_attribute_value <= :v" . $attributeCount
                                        . "value2";
                                    $max = $value['max'];
                                    $args["v" . $attributeCount . "value2"] = $max;
                                }

                                if (isset($value['min']) && isset($value['max']) && $min > $max) {
                                    $t = $min;
                                    $min = $max;
                                    $max = $t;
                                    $args["v" . $attributeCount . "value"] = $min;
                                    $args["v" . $attributeCount . "value2"] = $max;
                                }

                                break;
                            case AttributeType::DATEYEAR:
                                $dateQuery = " and YEAR(CURDATE()) - YEAR(v$attributeCount.insertion_attribute_value) - IF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(v$attributeCount.insertion_attribute_value), '-', DAY(v$attributeCount.insertion_attribute_value)) ,'%Y-%c-%e') > CURDATE(), 1, 0)";
                                $sqlQueryWhere .= " and v$attributeCount.attribute_key = :v" . $attributeCount . "key";

                                if (isset($value['min'])) {
                                    $sqlQueryWhere .= $dateQuery . " >= :v" . $attributeCount . "value";
                                    $min = $value['min'];
                                    $args["v" . $attributeCount . "value"] = $min;
                                }
                                if (isset($value['max'])) {
                                    $sqlQueryWhere .= $dateQuery . " <= :v" . $attributeCount . "value2";
                                    $max = $value['max'];
                                    $args["v" . $attributeCount . "value2"] = $max;
                                }

                                if (isset($value['min']) && isset($value['max']) && $min > $max) {
                                    $t = $min;
                                    $min = $max;
                                    $max = $t;
                                    $args["v" . $attributeCount . "value"] = $min;
                                    $args["v" . $attributeCount . "value2"] = $max;
                                }

                                break;
                            case AttributeType::DATEMONTH:
                                $min = null;
                                $max = null;

                                $dateQuery = " and DATEDIFF(CURDATE(), v$attributeCount.insertion_attribute_value) / 30";
                                $sqlQueryWhere .= " and v$attributeCount.attribute_key = :v" . $attributeCount . "key";

                                if (isset($value['min'])) {
                                    $sqlQueryWhere .= $dateQuery . " >= :v" . $attributeCount . "value";
                                    $min = $value['min'];
                                    $args["v" . $attributeCount . "value"] = $min;
                                }
                                if (isset($value['max'])) {
                                    $sqlQueryWhere .= $dateQuery . " <= :v" . $attributeCount . "value2";
                                    $max = $value['max'];
                                    $args["v" . $attributeCount . "value2"] = $max;
                                }

                                if (isset($value['min']) && isset($value['max']) && $min > $max) {
                                    $t = $min;
                                    $min = $max;
                                    $max = $t;
                                    $args["v" . $attributeCount . "value"] = $min;
                                    $args["v" . $attributeCount . "value2"] = $max;
                                }

                                break;
                            case AttributeType::PEDIGREE:
                                //TODO: Refactoring necessary
                                /** @var AttributeKey $subAttributeKeys */
                                $subAttributeValues = $attributeKey->getValues();
                                $pedigreeAttributeCount = $attributeCount;

                                if (sizeof($subAttributeValues) > 0) {
                                    $sqlQueryWhere .= " and ";

                                    if (isset($value['search_ancestor_1'])) {
                                        $sqlQueryWhere .= "(";
                                        $args["v" . $pedigreeAttributeCount . "value1"] = $value['search_ancestor_1'];
                                        $first = true;
                                        /** @var AttributeValue $subAttributeValue */
                                        foreach ($subAttributeValues as $subAttributeValue) {
                                            $attributeCount++;

                                            $subAttributeKeysId = $subAttributeValue->getSubAttributeKey()->getId();

                                            if (!$first) {
                                                $sqlQueryWhere .= " or ";
                                            }

                                            $first = false;

                                            $sqlQueryWhere .= " v$attributeCount.attribute_key  = :v" . $attributeCount . "key and (";
                                            $sqlQueryWhere .= "v$attributeCount.insertion_attribute_value = :v" . $pedigreeAttributeCount . "value1";
                                            $sqlQueryWhere .= ")";

                                            $sqlQuery .= " left join oforge_insertion_insertion_attribute_value v$attributeCount on v$attributeCount.insertion_id = i.id and v$attributeCount.attribute_key = :v"
                                                . $attributeCount . "key";
                                            $args["v" . $attributeCount . "key"] = "$subAttributeKeysId";
                                        }

                                        $sqlQueryWhere .= ")";
                                    }

                                    if (isset($value['search_ancestor_1']) && isset($value['search_ancestor_2'])) {
                                        $sqlQueryWhere .= " and ";
                                    }

                                    if (isset($value['search_ancestor_2'])) {
                                        $sqlQueryWhere .= " (";
                                        $args["v" . $pedigreeAttributeCount . "value2"] = $value['search_ancestor_2'];
                                        $first = true;
                                        /** @var AttributeValue $subAttributeValue */
                                        foreach ($subAttributeValues as $subAttributeValue) {
                                            $attributeCount++;

                                            $subAttributeKeysId = $subAttributeValue->getSubAttributeKey()->getId();

                                            if (!$first) {
                                                $sqlQueryWhere .= " or ";
                                            }
                                            $first = false;

                                            $sqlQueryWhere .= " v$attributeCount.attribute_key  = :v" . $attributeCount . "key and (";
                                            $sqlQueryWhere .= "v$attributeCount.insertion_attribute_value = :v" . $pedigreeAttributeCount . "value2)";

                                            $sqlQuery .= " left join oforge_insertion_insertion_attribute_value v$attributeCount on v$attributeCount.insertion_id = i.id and v$attributeCount.attribute_key = :v"
                                                . $attributeCount . "key";
                                            $args["v" . $attributeCount . "key"] = "$subAttributeKeysId";
                                        }
                                        $sqlQueryWhere .= ")";
                                    }
                                }

                                break;

                            default: //multi

                                $sqlQueryWhere .= " and v$attributeCount.attribute_key = :v" . $attributeCount . "key and (";

                                foreach ($value as $key => $v) {
                                    if ($key > 0) {
                                        $sqlQueryWhere .= " or ";
                                    }

                                    $sqlQueryWhere .= "v$attributeCount.insertion_attribute_value = :v" . $attributeCount
                                        . "value" . $key;
                                    $args["v" . $attributeCount . "value" . $key] = $v;
                                }

                                $sqlQueryWhere .= ")";
                        }

                        $attributeCount++;
                    } else {
                        switch ($attributeKey->getFilterType()) {
                            case AttributeType::TEXT:
                                $sqlQuery .= " left join oforge_insertion_insertion_attribute_value v$attributeCount on v$attributeCount . insertion_id = i . id and v$attributeCount
                                                                                                                                                              . attribute_key = :v"
                                    . $attributeCount . "key";

                                $sqlQueryWhere .= " and v$attributeCount . attribute_key = :v" . $attributeCount
                                    . "key and v$attributeCount . insertion_attribute_value like :v" . $attributeCount
                                    . "value";
                                $args["v" . $attributeCount . "key"] = $attributeKey->getId();
                                $args["v" . $attributeCount . "value"] = "%" . $value . "%";

                                $attributeCount++;
                                break;
                            default:
                                $sqlQuery .= " left join oforge_insertion_insertion_attribute_value v$attributeCount on v$attributeCount . insertion_id = i . id and v$attributeCount
                                                                                                                                                              . attribute_key = :v"
                                    . $attributeCount . "key and v$attributeCount . insertion_attribute_value = :v" . $attributeCount . "value";

                                $sqlQueryWhere .= " and v$attributeCount . attribute_key = :v" . $attributeCount
                                    . "key and v$attributeCount . insertion_attribute_value = :v" . $attributeCount
                                    . "value";
                                $args["v" . $attributeCount . "key"] = $attributeKey->getId();
                                $args["v" . $attributeCount . "value"] = $value;

                                $attributeCount++;
                        }
                    }
                }
            }
        }

        if (isset($params["after_date"])) {
            $params['after_date'] = date_format($params["after_date"], 'Y-m-d h:i:s');
            $sqlQueryWhere .= " and DATEDIFF(i.created_at, :ad) > 0";
            $args["ad"] = $params["after_date"];
        }

        $sqlQueryOrderBy = " ORDER BY " . $orderNative . " " . $orderDir;

        $sqlResult = $this->entityManager()->getEntityManager()->getConnection()->executeQuery($sqlQuery . $sqlQueryWhere . $sqlQueryOrderBy, $args);
        $ids = $sqlResult->fetchAll();

        $result["query"]["count"] = sizeof($ids);
        $result["query"]["pageSize"] = $pageSize;
        $result["query"]["page"] = $page;
        $result["query"]["pageCount"] = ceil((1.0) * sizeof($ids) / $pageSize);
        $result["query"]["items"] = [];
        /**
         * @var $type InsertionType
         */
        $type = $this->repository("type")->findOneBy(["id" => $typeId]);

        $attributes = $type->getAttributes();

        $valueMap = [];
        $attributeMap = [];

        /**
         * @var $attribute InsertionTypeAttribute
         */
        foreach ($attributes as $attribute) {
            $attributeMap[$attribute->getAttributeKey()->getId()] = [
                "name" => $attribute->getAttributeKey()->getName(),
                "top" => $attribute->isTop(),
            ];

            foreach ($attribute->getAttributeKey()->getValues() as $value) {
                $valueMap += $this->getValueMap($value);
            }
        }

        $findIds = [];

        for ($i = ($page - 1) * $pageSize; $i < $page * $pageSize; $i++) {
            if (sizeof($ids) > $i) {
                $findIds[] = $ids[$i]["id"];
            }
        }

        $items = $this->repository()->findBy(["id" => $findIds], [$order => $orderDir]);

        $result["values"] = $valueMap;

        foreach ($items as $item) {
            $data = [
                "id" => $item->getId(),
                "contact" => $item->getContact() != null ? $item->getContact()->toArray(0) : [],
                "content" => [],
                "media" => [],
                "values" => [],
                "topvalues" => [],
                "price" => $item->getPrice(),
                "minPrice" => $item->getMinPrice(),
                "priceType" => $item->getPriceType(),
                "tax" => $item->isTax(),
                "createdAt" => $item->getCreatedAt(),
            ];

            foreach ($item->getContent() as $content) {
                $data["content"][] = $content->toArray(0);
            }

            foreach ($item->getMedia() as $media) {
                $data["media"][] = $media->toArray(0);
            }

            foreach ($item->getValues() as $value) {
                $data["values"][] = $value->toArray(0);;
            }

            /**
             * @var $attribute InsertionTypeAttribute
             */
            foreach ($attributes as $attribute) {
                if ($attribute->isTop()) {
                    foreach ($data["values"] as $value) {
                        if ($value["attributeKey"] == $attribute->getAttributeKey()->getId()) {
                            $data["topvalues"][] = [
                                "name" => $attribute->getAttributeKey()->getName(),
                                "type" => $attribute->getAttributeKey()->getType(),
                                "attributeKey" => $attribute->getAttributeKey()->getId(),
                                "value" => $value["value"],
                            ];
                        }
                    }
                }
            }

            $result["query"]["items"][] = $data;
        }

        return $result;
    }

    public function getUserInsertions($user, $page = 1, $count = 10): ?array
    {
        $insertions = $this->repository()->findBy(["user" => $user, "deleted" => false], null, $count, ($page - 1) * $count);

        $result = [];
        foreach ($insertions as $insertion) {
            $result[] = $insertion->toArray(2);
        }

        return $result;
    }

    public function saveSearchRadius($params)
    {
        $name = "insertion_search_radius";
        $current = [];

        try {
            // returns null if it cannot be decoded. See https://php.net/manual/en/function.json-decode.php
            $current = json_decode($_COOKIE[$name], true);
        } catch (Exception $e) {
        }
        /**
         * filter by distance
         */
        if (isset($params["zip"]) && isset($params["zip_range"])) {
            $current = ["zip" => $params["zip"], "zip_range" => $params["zip_range"], "country" => ($params['country'] ?: "germany")];

            setcookie($name, json_encode($current), time() + 60 * 60 * 24 * 2);
        }

        if ($current === null) {
            $current = [];
        }

        return $current;
    }

    private function getValueMap($value)
    {
        $temp = [];
        $temp[$value->getId()] = $value->getValue();
        if ($value->getSubAttributeKey() != null) {
            foreach ($value->getSubAttributeKey()->getValues() as $v) {
                $temp += $this->getValueMap($v);
            }
        }

        return $temp;
    }

    private function getAttributeKeys($value)
    {
        $temp = [];

        if ($value->getSubAttributeKey() != null) {
            $temp[] = $value->getSubAttributeKey();

            foreach ($value->getSubAttributeKey()->getValues() as $v) {
                $temp = array_merge($temp, $this->getAttributeKeys($v));
            }
        }

        return $temp;
    }
}
