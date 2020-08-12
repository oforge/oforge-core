<?php

namespace Insertion\Services;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use Insertion\Enum\AttributeType;
use Insertion\Models\AttributeKey;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionAttributeValue;
use Insertion\Models\InsertionMedia;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Models\InsertionTypeGroup;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;

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
     */
    public function search($typeId, $params) : ?array {
        $page = isset($params["page"]) ? $params["page"] : 1;

        $page = intval($page);
        if ($page < 1) {
            $page = 1;
        }

        $pageSize = isset($params["pageSize"]) ? $params["pageSize"] : 10;

        $pageSize = intval($pageSize);
        if ($pageSize < 1) {
            $pageSize = 10;
        }

        $order       = 'id';
        $orderNative = 'id';
        $orderDir    = 'asc';
        $args        = [];
        $items       = [];
        $exclude     = ['price', 'country', 'zip', 'zip_range', 'order', 'page', 'pageSize', 'after_date'];

        /** set default order */
        if (!isset($params['order'])) {
            $params['order'] = 'date_desc';
        }
        $result = ["filter" => [], "query" => [], 'order' => $params["order"]];

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

        $sqlQuery      = "select i.id from oforge_insertion i";
        $sqlQueryWhere = " where i.active = 1 and i.moderation = 1 and i.insertion_type_id = :type";
        $args["type"]  = intval($typeId);

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

            /** @var InsertionZipService $insertionZipService */
            $insertionZipService = Oforge()->Services()->get("insertion.zip");
            $coordinates         = $insertionZipService->get($params["zip"], $country);

            if ($coordinates != null) {
                $sqlQuery          .= " left join oforge_insertion_contact contact on contact.insertion_id = i.id";
                $sqlQuery          .= " left join oforge_insertion_zip_coordinates zip on zip.country = contact.country and zip.zip = contact.zip";
                $sqlQueryWhere     .= " and ST_Distance_Sphere(POINT(zip.lng, zip.lat), POINT(:lng, :lat)) / 1000 <= :zip_range";
                $args["lng"]       = $coordinates->getLng();
                $args["lat"]       = $coordinates->getLat();
                $args["zip_range"] = $params["zip_range"];
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

        /**
         * Fetch all ids from previous filter
         */
        $sqlResult = $this->entityManager()->getEntityManager()->getConnection()->executeQuery($sqlQuery . $sqlQueryWhere, $args);
        $ids       = array_column($sqlResult->fetchAll(), 'id');

        /**
         * remove filtered parameters
         */
        foreach ($exclude as $e) {
            unset($params[$e]);
        }
        $pkeys = array_keys($params);

        /**
         * We need the pedigree
         */
        $pedigreeList = [];
        if (isset($params['Pedigree'])) {
            $pedigreeList = $this->getPedigreeList();
        }

        /** @var AttributeService $attributeService */
        $attributeService = $attributeService = Oforge()->Services()->get('insertion.attribute');

        /** @var AttributeKey[] $attributeKeys */
        $attributeKeys    = $this->repository('key')->findAll();
        $keys             = [];
        $hierarchicalKeys = [];
        /**
         * Build an attributeKey map for matching with values
         */
        if (sizeof($pkeys) > 0) {
            foreach ($attributeKeys as $attributeKey) {
                $filterName = str_replace(' ', '_', $attributeKey->getName());

                if (in_array($filterName, $pkeys)) {
                    if (lcfirst($filterName) === 'pedigree') {
                        $keys += $this->addPedigreeToKeys($pedigreeList, $attributeKeys, $params[$filterName]);
                    } else {
                        $filterType                                 = $attributeKey->getFilterType();
                        $keys[$attributeKey->getId()]['name']       = $attributeKey->getName();
                        $keys[$attributeKey->getId()]['filterName'] = $filterName;
                        $keys[$attributeKey->getId()]['filterType'] = $filterType;
                        $keys[$attributeKey->getId()]['values']     = $params[$filterName];

                        if ($params['insertion'][$attributeKey->getId() . '_sort_hierarchical']) {
                            if (in_array($filterType, [AttributeType::SINGLE, AttributeType::MULTI])) {
                                /** @var int[] $higherValues */
                                $higherValues = $attributeService->getAllValuesBetterThan($attributeKey, $params[$filterName]);
                                foreach ($higherValues as &$higherValue) {
                                    $higherValue = strval($higherValue);
                                }

                                $hierarchicalKeys[$attributeKey->getId()]['values'] = array_merge_recursive($higherValues,
                                    $keys[$attributeKey->getId()]['values']);
                                $keys[$attributeKey->getId()]['filterType']         = AttributeType::MULTI;
                            }
                        }
                    }
                }
            }
        }

        /**
         * Fetch all InsertionAttributeValues that match with the AttributeKeys from the params
         */
        $attributeValueSql      = "select v.insertion_id, v.attribute_key, v.insertion_attribute_value from oforge_insertion_insertion_attribute_value as v";
        $attributeValueSqlWhere = " where v.insertion_id in (:ids) and v.attribute_key in (:key_ids) and v.insertion_attribute_value not like ''";
        $args                   = ['ids' => $ids, 'key_ids' => array_keys($keys)];
        $attributeValueQuery    = $this->entityManager()->getEntityManager()->getConnection()->executeQuery($attributeValueSql . $attributeValueSqlWhere, $args,
            ['ids' => Connection::PARAM_INT_ARRAY, 'key_ids' => Connection::PARAM_INT_ARRAY]);
        $attributeValues        = $attributeValueQuery->fetchAll();

        /**
         * Rearrange for matching
         */
        $av = [];
        foreach ($attributeValues as $attributeValue) {
            $av[$attributeValue['insertion_id']][$attributeValue['attribute_key']][] = $attributeValue['insertion_attribute_value'];
        }

        $result['items'] = [];
        /**
         * keys[ id [ name, filterName, filterType, values[] ], ... ]
         * av[ ins_id [ key_id [ values[] ], ...], ...]
         * The keys match, so we don't have to check them
         * We only check the filter type and compare the values
         */
        foreach ($av as $insertionId => $attributeKeys) {
            $checkedPedigree = false;
            foreach ($keys as $key => $value) {
                $result['filter'][$value['name']] = is_array($value['values']) ? array_unique($value['values']) : $value['values'];

                if (isset($hierarchicalKeys[$key])) {
                    $value['values'] = array_merge($hierarchicalKeys[$key]['values'], $value['values']);
                }

                $values = $attributeKeys[$key];
                switch ($value['filterType']) {
                    case AttributeType::RANGE:
                        if ($this->isBetweenMinMax($value['values'], $values[0])) {
                        } else {
                            continue 3;
                        }
                        break;
                    case AttributeType::DATEYEAR:
                        $now         = date_create(date('Y-m-d'));
                        $dateToCheck = date_create($values[0]);
                        if ($dateToCheck) {
                            $interval = date_diff($dateToCheck, $now);
                            if ($this->isBetweenMinMax($value['values'], $interval->format('%y'))) {
                            } else {
                                continue 3;
                            }
                        }
                        break;
                    case AttributeType::DATEMONTH:
                        $now         = date_create(date('Y-m-d'));
                        $dateToCheck = date_create($values[0]);
                        if ($dateToCheck) {
                            $interval = date_diff($dateToCheck, $now);
                            if ($this->isBetweenMinMax($value['values'], $interval->format('%m'))) {
                            } else {
                                continue 3;
                            }
                        }
                        break;
                    case AttributeType::PEDIGREE:
                        if (!$checkedPedigree) {
                            foreach ($value['values'] as $i => $v) {
                                $j                                                        = $i + 1;
                                $result['filter'][$value['name']]["search_ancestor_${j}"] = $v;
                            }
                            $checkedPedigree = true;
                            // hole values
                            // prüfe
                            // weiter
                            if (!$this->validatePedigree($insertionId, $value['values'])) {
                                continue 3;
                            }
                        }
                        break;
                    case AttributeType::MULTI:
                        if (empty(array_intersect($values, $value['values']))) {
                            continue 3;
                        }
                        break;
                    default:
                        if (is_array($value['values']) && !empty(array_intersect($values, $value['values']))) {
                        } elseif (in_array($value['values'], $values)) {
                        } else {
                            continue 3;
                        }
                        break;
                }
            }
            $result['items'][] = $insertionId;
        }
        /**
         * Remove numeric index from Pedigree array
         */
        if (isset($result['filter']['Pedigree'])) {
            foreach ($result['filter']['Pedigree'] as $key => $value) {
                if (is_numeric($key)) {
                    unset($result['filter']['Pedigree'][$key]);
                }
            }
        }

        /**
         * If no filter is set, use the ids
         */
        $items = $result['items'];
        if (sizeof($keys) < 1) {
            $items = $ids;
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
        $valueMap   = [];
        /**
         * @var $attribute InsertionTypeAttribute
         */
        foreach ($attributes as $attribute) {
            foreach ($attribute->getAttributeKey()->getValues() as $value) {
                $valueMap += $this->getValueMap($value);
            }
        }

        $result["values"] = $valueMap;
        /** @var Insertion[] $insertions */
        $insertions = $this->repository()->findBy(['id' => $items], [$order => $orderDir], $pageSize, $pageSize * ($page - 1));

        foreach ($insertions as $item) {
            $data = [
                "id"            => $item->getId(),
                "contact"       => $item->getContact() != null ? $item->getContact()->toArray(0) : [],
                "content"       => [],
                "media"         => [],
                "values"        => [],
                "topvalues"     => [],
                "price"         => $item->getPrice(),
                "minPrice"      => $item->getMinPrice(),
                "priceType"     => $item->getPriceType(),
                "tax"           => $item->isTax(),
                "user"          => $item->getUser()->getId(),
                "createdAt"     => $item->getCreatedAt(),
                "insertionType" => $item->getInsertionType()->toArray(0),
            ];

            foreach ($item->getContent() as $content) {
                $data["content"][] = $content->toArray(0);
            }

            /** @var InsertionMedia $media */
            foreach ($item->getMedia() as $key => $media) {
                $data["media"][]             = $media->toArray(0);
                $data["media"][$key]['type'] = $media->getContent()->getType();
            }

            foreach ($item->getValues() as $value) {
                $data["values"][] = $value->toArray(0);
            }

            $data = $this->getInsertionTopValues($data, $attributes);

            $result["query"]["items"][] = $data;
        }

        return $result;
    }

    /**
     * Get the top values of an insertion type
     *
     * @param $insertionData
     * @param $attributes
     *
     * @return mixed
     */
    public function getInsertionTopValues($insertionData, $attributes) {
        /**
         * @var $attribute InsertionTypeAttribute
         */
        foreach ($attributes as $attribute) {
            if ($attribute->isTop()) {
                $valueList = [];
                foreach ($insertionData["values"] as $value) {
                    if ($value["attributeKey"] == $attribute->getAttributeKey()->getId()) {
                        $valueList[] = $value['value'];
                    }
                }

                if (sizeof($valueList) === 1) {
                    $valueList = $valueList[0];
                }

                $insertionData["topvalues"][] = [
                    "name"         => $attribute->getAttributeKey()->getName(),
                    "type"         => $attribute->getAttributeKey()->getType(),
                    "attributeKey" => $attribute->getAttributeKey()->getId(),
                    "value"        => $valueList,
                ];
            }
        }

        return $insertionData;
    }

    public function getUserInsertions($user, $page = 1, $count = 10) : ?array {
        $insertions = $this->repository()->findBy(["user" => $user, "deleted" => false], null, $count, ($page - 1) * $count);

        $result = [];
        foreach ($insertions as $insertion) {
            $result[] = $insertion->toArray(2);
        }

        return $result;
    }

    /**
     * @param $user
     * @param array $criteria
     *
     * @return int
     */
    public function getUserInsertionCount($user, array $criteria = []) : int {
        $criteria = ArrayHelper::extractByKey($criteria, [
            'active',
            'deleted',
            'moderation',
            'price',
            'minPrice',
            'priceType',
        ]);

        $criteria['user'] = $user;

        return $this->repository()->count($criteria);
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getUserDistinctInsertionTypes(User $user) {
        $insertionTypes = [];
        try {
            /** @var Insertion[] $insertions */
            $insertions = $this->repository()->findBy(['user' => $user, 'active' => true, /*, 'deleted' => false*/]);
            foreach ($insertions as $insertion) {
                /** @var InsertionType $insertionType */
                $insertionType                           = $insertion->getInsertionType();
                $insertionTypes[$insertionType->getId()] = $insertionType->getName();
            }
            ksort($insertionTypes);
        } catch (\Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }

        return $insertionTypes;
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
        if (($min === null || $min <= $value)
            && ($max === null || $value <= $max)) {
            return true;
        }

        return false;
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

    private function getPedigreeList() {
        /** @var AttributeKey $pedigree */
        $result   = [];
        $pedigree = $this->repository('key')->findOneBy(['name' => 'pedigree']);
        $id       = $pedigree->getId();
        $sql      = 'select v.attribute_value_sub_attribute_key_id as id from oforge_insertion_attribute_value as v where v.attribute_key_id = :id';
        $args     = ['id' => $id];
        $result   = array_column($this->entityManager()->getEntityManager()->getConnection()->executeQuery($sql, $args)->fetchAll(), 'id');

        return $result;
    }

    private function addPedigreeToKeys($list, $attributeKeys, $values) {
        /*
        $keys[$attributeKey->getId()]['name']       = $attributeKey->getName();
        $keys[$attributeKey->getId()]['filterName'] = $filterName;
        $keys[$attributeKey->getId()]['filterType'] = $attributeKey->getFilterType();
        $keys[$attributeKey->getId()]['values']     = $params[$filterName];
        */
        $keys       = [];
        $realValues = [];
        foreach ($values as $key => $value) {
            $realValues[] = $value;
        }
        foreach ($list as $item) {
            /** @var AttributeKey[] $attributeKeys */
            $keys[$item]['name']       = 'Pedigree';
            $keys[$item]['filterName'] = 'Pedigree';
            $keys[$item]['filterType'] = AttributeType::PEDIGREE;
            $keys[$item]['values']     = $realValues;
        }

        return $keys;
    }

    private function validatePedigree($insertionId, $values) {
        $sql   = "select count(v.id) as ids from oforge_insertion_insertion_attribute_value as v";
        $where = " where v.insertion_id = :ins_id";
        $where .= " and v.insertion_attribute_value in (:values)";

        $args   = ['ins_id' => $insertionId, 'values' => $values];
        $result = $this->entityManager()->getEntityManager()->getConnection()->executeQuery($sql . $where, $args, ['values' => Connection::PARAM_STR_ARRAY])
                       ->fetch();

        return ($result['ids'] > 0 && $result['ids'] == sizeof($values));
    }
}
