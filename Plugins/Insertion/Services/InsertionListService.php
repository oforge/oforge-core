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
use Insertion\Models\InsertionAttributeValue;
use Insertion\Models\InsertionContent;
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
class InsertionListService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default'                 => Insertion::class,
            'type'                    => InsertionType::class,
            'insertionTypeAttribute'  => InsertionTypeAttribute::class,
            'key'                     => AttributeKey::class,
            'group'                   => InsertionTypeGroup::class,
            'insertionAttributeValue' => InsertionAttributeValue::class,
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

    public function search($typeId, $params) : ?array {
        $page     = isset($params["page"]) ? $params["page"] : 1;
        $pageSize = isset($params["pageSize"]) ? $params["pageSize"] : 10;

        if (!isset($params['order'])) {
            $params['order'] = 'date_desc';
        }

        $order       = 'id';
        $orderNative = 'id';
        $orderDir    = 'asc';

        if (isset($params['order'])) {
            switch ($params['order']) {
                case 'price_asc':
                    $order       = 'price';
                    $orderNative = 'price';
                    $orderDir    = 'asc';
                    break;
                case 'price_desc':
                    $order       = 'price';
                    $orderNative = 'price';
                    $orderDir    = 'desc';
                    break;
                case 'date_asc':
                    $order       = 'createdAt';
                    $orderNative = 'created_at';
                    $orderDir    = 'asc';
                    break;
                case  'date_desc':
                    $order       = 'createdAt';
                    $orderNative = 'created_at';
                    $orderDir    = 'desc';
                    break;
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
                $sqlQueryWhere                    .= " and (i.price >= :min OR (i.min_price IS NOT NULL AND i.min_price >= :min))";
                $min                              = $params["price"]['min'];
                $args["min"]                      = $min;
                $result['filter']['price']['min'] = $min;
            }

            if (isset($params["price"]['max'])) {
                $sqlQueryWhere                    .= " and (i.price <= :max OR (i.min_price IS NOT NULL AND i.min_price <= :max))";
                $max                              = $params["price"]['max'];
                $args["max"]                      = $max;
                $result['filter']['price']['max'] = $max;
            }

            if (isset($params["price"]['min']) && isset($params["price"]['max']) && $min > $max) {
                [$min, $max] = [$max, $min];
                $args["min"]                      = $min;
                $args["max"]                      = $max;
                $result['filter']['price']['min'] = $min;
                $result['filter']['price']['max'] = $max;
            }
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

        if (isset($params["after_date"])) {
            $params['after_date'] = date_format($params["after_date"], 'Y-m-d h:i:s');
            $sqlQueryWhere        .= " and DATEDIFF(i.created_at, :ad) > 0";
            $args["ad"]           = $params["after_date"];
        }

        $sqlQueryOrderBy = " ORDER BY " . $orderNative . " " . $orderDir;

        $sqlResult = $this->entityManager()->getEntityManager()->getConnection()->executeQuery($sqlQuery . $sqlQueryWhere . $sqlQueryOrderBy, $args);
        $ids       = $sqlResult->fetchAll();

        /** ******************************************************************************************************** */
        /** ******************************************************************************************************** */
        /** ******************************************************************************************************** */


        $exclude        = ['country', 'zip', 'zip_range', 'order'];
        $idsOnly        = [];
        $items         = [];


        foreach ($ids as $id) {
            $idsOnly[] = $id['id'];
        }

        foreach ($exclude as $e) {
            unset($params[$e]);
        }
        $pkeys = array_keys($params);

        $insertions = $this->repository()->findBy(['id' => $idsOnly], [$order => $orderDir]);

        $getOut = false;
        $matches = 0;

        if (sizeof($params) > 0) {
            /** @var Insertion $insertion */
            foreach ($insertions as $insertion) {
                $getOut = false;
                /** @var InsertionAttributeValue $insertionAttributeValue */

                // TODO: this doesnt work for multiple attribute values like breed = 2 and breed = 3 (not filter type!)
                $intersection = [];
                foreach ($insertion->getValues() as $insertionAttributeValue) {
                    $intersection[] = str_replace(' ', '_', $insertionAttributeValue->getAttributeKey()->getName());
                }
                $intersectionSize = array_intersect($intersection, $pkeys);
                if (sizeof(array_intersect($intersection, $pkeys)) === sizeof($pkeys)) {
                    foreach ($insertion->getValues() as $insertionAttributeValue) {
                        if ($getOut === true) {
                            $matches = 0;
                            break 1;
                        }

                        $keyName    = str_replace(' ', '_', $insertionAttributeValue->getAttributeKey()->getName());
                        $filterName = $insertionAttributeValue->getAttributeKey()->getName();
                        $value      = $insertionAttributeValue->getValue();


                        $filterType = $insertionAttributeValue->getAttributeKey()->getFilterType();

                        if (in_array($keyName, $pkeys)) {
                            $result['filter'][$filterName] = is_array($params[$keyName]) ? array_unique($params[$keyName]) : $params[$keyName];
                            switch ($filterType) {
                                case AttributeType::RANGE:
                                    if ($this->isBetweenMinMax($params[$keyName], $value)) {
                                        $matches++;
                                    } else {
                                        $matches = 0;
                                        $getOut  = true;
                                    }
                                    break;
                                case AttributeType::DATEYEAR:
                                    $now         = date_create(date('Y-m-d'));
                                    $dateToCheck = date_create($value);
                                    if ($dateToCheck) {
                                        $interval = date_diff($dateToCheck, $now);
                                        if ($this->isBetweenMinMax($params[$keyName], $interval->format('%y'))) {
                                            $matches++;
                                        } else {
                                            $matches = 0;
                                            $getOut  = true;
                                        }
                                    }
                                    break;

                                case AttributeType::DATEMONTH:
                                    $now         = date_create(date('Y-m-d'));
                                    $dateToCheck = date_create($value);
                                    if ($dateToCheck) {
                                        $interval = date_diff($dateToCheck, $now);
                                        if ($this->isBetweenMinMax($params[$keyName], $interval->format('%m'))) {
                                            $matches++;
                                        } else {
                                            $matches = 0;
                                            $getOut  = true;
                                        }
                                    }
                                    break;
                                case AttributeType::PEDIGREE:
                                case AttributeType::MULTI:
                                    if (in_array($value, $params[$keyName])) {
                                        $matches++;
                                    } else {
                                        $matches = 0;
                                        $getOut  = true;
                                    }
                                    break;
                                default:
                                    if (is_array($params[$keyName]) && in_array($value, $params[$keyName])) {
                                        $matches++;
                                    } elseif ($params[$keyName] === $value) {
                                        $matches++;
                                    } else {
                                        $matches = 0;
                                        $getOut  = true;
                                    }
                                    break;
                            }
                        }
                    }
                }

                if ($matches > 0) {
                    $items[] = $insertion;
                    $matches = 0;
                }
            }
        } else {
            $items = $insertions;
        }

        $result["query"]["count"]     = sizeof($items);
        $result["query"]["pageSize"]  = $pageSize;
        $result["query"]["page"]      = $page;
        $result["query"]["pageCount"] = ceil((1.0) * sizeof($items) / $pageSize);
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
                $valueMap += $this->getValueMap($value);
            }
        }

        $findIds = [];

        for ($i = ($page - 1) * $pageSize; $i < $page * $pageSize; $i++) {
            if (sizeof($ids) > $i) {
                $findIds[] = $ids[$i]["id"];
            }
        }

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
                "minPrice"  => $item->getMinPrice(),
                "priceType" => $item->getPriceType(),
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
                $data["values"][] = $value->toArray(0);
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


    private function isBetweenMinMax($param, $value) {
        $min = null;
        $max = null;
        if (isset($param['min'])) {
            $min = $param['min'];
        }
        if (isset($param['max'])) {
            $max = $param['max'];
        }
        if ($min > 0 && $max > 0 && $min > $max) {
            [$min, $max] = [$max, $min];
        }
        if (($min === null || $min <= $value) &&
            ($max === null || $value <= $max)) {
            return true;
        }
        return false;
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
            if (isset($_COOKIE[$name])) {
                // returns null if it cannot be decoded. See https://php.net/manual/en/function.json-decode.php
                $current = json_decode($_COOKIE[$name], true);
                if ($current === null) {
                    $current = [];
                }
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

    private function getAttributeKeys($value) {
        $temp = [];

        if ($value->getSubAttributeKey() != null) {
            $temp[] = $value->getSubAttributeKey()->toArray();

            foreach ($value->getSubAttributeKey()->getValues() as $v) {
                $temp = array_merge($temp, $this->getAttributeKeys($v));
            }
        }

        return $temp;
    }
}
