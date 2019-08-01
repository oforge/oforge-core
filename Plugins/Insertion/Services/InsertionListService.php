<?php

namespace Insertion\Services;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Insertion\Enum\AttributeType;
use Insertion\Models\AttributeKey;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Models\InsertionTypeGroup;
use Insertion\Models\InsertionZipCoordinates;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Annotation\Cache\Cache;
use Oforge\Engine\Modules\Core\Annotation\Cache\CacheInvalidation;
use Oforge\Engine\Modules\Core\Helper\Statics;

/**
 * Class InsertionListService
 * @Cache()
 *
 * @package Insertion\Services
 */
class InsertionListService extends AbstractDatabaseAccess {
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
     * @param $typeId
     * @param $params
     * @Cache(slot="insertion", duration="T15M")
     *
     * @return array|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */

    public function search($typeId, $params) : ?array {
        $page     = isset($params["page"]) ? $params["page"] : 1;
        $pageSize = isset($params["pageSize"]) ? $params["pageSize"] : 10;

        $keys = $this->repository("key")->findAll();

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
        if (isset($params["price"]) && is_array($params["price"]) && sizeof($params["price"]) == 2) {
            $sqlQueryWhere .= " and i.price between :min and :max ";

            $min = $params["price"][0];
            $max = $params["price"][1];

            if ($min > $max) {
                $t   = $min;
                $min = $max;
                $max = $t;
            }

            $args["min"] = $min;
            $args["max"] = $max;

            $result['filter']['price'] = [$min, $max];
        }

        /**
         * filter by distance
         */
        if (isset($params["zip"]) && isset($params["zip_range"]) && !empty($params["zip"]) && $params["zip_range"]) {
            $country = $params['country'] ? : "germany";

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
                'zip'       => $params["zip"],
                'zip_range' => $params["zip_range"],
                'country'   => $params["country"],
            ];
        }

        if (sizeof($keys) > 0) {
            $attributeCount = 0;

            foreach ($keys as $attributeKey) {
                $name = $attributeKey->getName();
                $name = str_replace(" ", "_", $name);

                if (isset($params[$name])) {
                    $value                                      = $params[$name];
                    $result['filter'][$attributeKey->getName()] = is_array($value) ? array_unique($value) : $value;

                    if (is_array($value)) { //should always be a multi selection or a range component
                        $sqlQuery                             .= " left join oforge_insertion_insertion_attribute_value v$attributeCount on v$attributeCount.insertion_id = i.id and v$attributeCount.attribute_key = :v"
                                                                 . $attributeCount . "key";
                        $args[":v" . $attributeCount . "key"] = $attributeKey->getId();

                        switch ($attributeKey->getFilterType()) {
                            case AttributeType::RANGE:
                                $sqlQueryWhere .= " and v$attributeCount.attribute_key = :v" . $attributeCount
                                                  . "key and v$attributeCount.insertion_attribute_value between :v" . $attributeCount . "value and :v"
                                                  . $attributeCount . "value2";

                                $min = $value[0];
                                $max = $value[1];

                                if ($min > $max) {
                                    $t   = $min;
                                    $min = $max;
                                    $max = $t;
                                }

                                $args[":v" . $attributeCount . "value"]  = $min;
                                $args[":v" . $attributeCount . "value2"] = $max;

                                break;
                            case AttributeType::DATEYEAR:

                                $sqlQueryWhere .= " and v$attributeCount.attribute_key = :v" . $attributeCount
                                                  . "key and YEAR(CURDATE()) - YEAR(v$attributeCount.insertion_attribute_value) - IF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(v$attributeCount.insertion_attribute_value), '-', DAY(v$attributeCount.insertion_attribute_value)) ,'%Y-%c-%e') > CURDATE(), 1, 0)  between :v"
                                                  . $attributeCount . "value and :v" . $attributeCount . "value2";

                                $min = $value[0];
                                $max = $value[1];

                                if ($min > $max) {
                                    $t   = $min;
                                    $min = $max;
                                    $max = $t;
                                }

                                $args[":v" . $attributeCount . "value"]  = $min;
                                $args[":v" . $attributeCount . "value2"] = $max;

                                break;
                            case AttributeType::DATEMONTH:

                                $sqlQueryWhere .= " and v$attributeCount.attribute_key = :v" . $attributeCount
                                                  . "key and DATEDIFF(CURDATE(), v$attributeCount.insertion_attribute_value) / 30  between :v" . $attributeCount
                                                  . "value and :v" . $attributeCount . "value2";

                                $min = $value[0];
                                $max = $value[1];

                                if ($min > $max) {
                                    $t   = $min;
                                    $min = $max;
                                    $max = $t;
                                }

                                $args[":v" . $attributeCount . "value"]  = $min;
                                $args[":v" . $attributeCount . "value2"] = $max;

                                break;
                            default: //multi

                                $sqlQueryWhere .= " and v$attributeCount.attribute_key = :v" . $attributeCount . "key and (";

                                foreach ($value as $key => $v) {
                                    if ($key > 0) {
                                        $sqlQueryWhere .= " or ";
                                    }

                                    $sqlQueryWhere                                .= "v$attributeCount.insertion_attribute_value = :v" . $attributeCount
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

                                $sqlQueryWhere                          .= " and v$attributeCount . attribute_key = :v" . $attributeCount
                                                                           . "key and v$attributeCount . insertion_attribute_value like :v" . $attributeCount
                                                                           . "value";
                                $args[":v" . $attributeCount . "key"]   = $attributeKey->getId();
                                $args[":v" . $attributeCount . "value"] = "%" . $value . "%";

                                $attributeCount++;
                                break;
                            default:
                                $sqlQuery .= " left join oforge_insertion_insertion_attribute_value v$attributeCount on v$attributeCount . insertion_id = i . id and v$attributeCount
                                                                                                                                                              . attribute_key = :v"
                                             . $attributeCount . "key and v$attributeCount . insertion_attribute_value = :v" . $attributeCount . "value";

                                $sqlQueryWhere                          .= " and v$attributeCount . attribute_key = :v" . $attributeCount
                                                                           . "key and v$attributeCount . insertion_attribute_value = :v" . $attributeCount
                                                                           . "value";
                                $args[":v" . $attributeCount . "key"]   = $attributeKey->getId();
                                $args[":v" . $attributeCount . "value"] = $value;

                                $attributeCount++;
                        }
                    }
                }
            }
        }

        $sqlResult = $this->entityManager()->getEntityManager()->getConnection()->executeQuery($sqlQuery . $sqlQueryWhere, $args);

        print_r($sqlResult);

        echo "\n";

        $ids = $sqlResult->fetchAll();

        $result["query"]["count"]     = sizeof($ids);
        $result["query"]["pageSize"]  = $pageSize;
        $result["query"]["page"]      = $page;
        $result["query"]["pageCount"] = ceil((1.0) * sizeof($ids) / $pageSize);
        $result["query"]["items"]     = [];
        /**
         * @var $type InsertionType
         */
        $type = $this->repository("type")->findOneBy(["id" => $typeId]);

        $attributes = $type->getAttributes();

        $valueMap     = [];
        $attributeMap = [];

        /**
         * @var $attribute InsertionTypeAttribute
         */
        foreach ($attributes as $attribute) {
            $attributeMap[$attribute->getAttributeKey()->getId()] = [
                "name" => $attribute->getAttributeKey()->getName(),
                "top"  => $attribute->isTop(),
            ];

            foreach ($attribute->getAttributeKey()->getValues() as $value) {
                $valueMap  +=  $this->getValueMap($value);
            }
        }

        $order    = 'id';
        $orderDir = 'desc';

        if (isset($params['order'])) {
            switch ($params['order']) {
                case 'price_asc':
                    $order    = 'price';
                    $orderDir = 'asc';
                    break;
                case 'price_desc':
                    $order    = 'price';
                    $orderDir = 'desc';
                    break;
                case 'date_asc':
                    $order    = 'createdAt';
                    $orderDir = 'asc';
                    break;
                case  'date_desc':
                    $order    = 'createdAt';
                    $orderDir = 'desc';
                    break;
                case 'rand':
                    $orderDir = 'RAND()';
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
                "id"        => $item->getId(),
                "contact"   => $item->getContact() != null ? $item->getContact()->toArray(0) : [],
                "content"   => [],
                "media"     => [],
                "values"    => [],
                "topvalues" => [],
                "price"     => $item->getPrice(),
                "tax"       => $item->isTax(),
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
                                "name"         => $attribute->getAttributeKey()->getName(),
                                "type"         => $attribute->getAttributeKey()->getType(),
                                "attributeKey" => $attribute->getAttributeKey()->getId(),
                                "value"        => $value["value"],
                            ];
                        }
                    }
                }
            }

            $result["query"]["items"][] = $data;
        }

        return $result;
    }

    public function getUserInsertions($user, $page = 1, $count = 10) : ?array {
        $insertions = $this->repository()->findBy(["user" => $user, "deleted" => false], null, $count, ($page - 1) * $count);

        $result = [];
        foreach ($insertions as $insertion) {
            $result[] = $insertion->toArray(2);
        }

        return $result;
    }

    public function saveSearchRadius($params) {
        $name    = "insertion_search_radius";
        $current = [];

        try {
            // returns null if it cannot be decoded. See https://php.net/manual/en/function.json-decode.php
            $current = json_decode($_COOKIE[$name], true);
        } catch (\Exception $e) {
        }
        /**
         * filter by distance
         */
        if (isset($params["zip"]) && isset($params["zip_range"])) {
            $current = ["zip" => $params["zip"], "zip_range" => $params["zip_range"], "country" => ($params['country'] ? : "germany")];

            setcookie($name, json_encode($current), time() + 60 * 60 * 24 * 2);
        }

        if ($current === null) {
            $current = [];
        }

        return $current;
    }

    private function getValueMap($value) {
        $temp                  = [];
        $temp[$value->getId()] = $value->getValue();
        if ($value->getSubAttributeKey() != null) {
            foreach ($value->getSubAttributeKey()->getValues() as $v) {
                $temp += $this->getValueMap($v);
            }
        }

        return $temp;
    }
}
