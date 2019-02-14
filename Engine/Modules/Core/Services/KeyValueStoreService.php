<?php

namespace Oforge\Engine\Modules\Core\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Models\Store\KeyValue;

class KeyValueStoreService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => KeyValue::class]);
    }

    /**
     * Get the value of a specific key from the key-value table
     *
     * @param string $name
     *
     * @return string|null
     */
    public function get(string $name) {
        /**
         * @var $element KeyValue
         */
        $element = $this->repository()->findOneBy(["name" => $name]);

        return isset($element) && strlen($element->getValue()) > 0 ? $element->getValue() : null;
    }

    /**
     * Create or update a key-value entry
     *
     * @param string $name
     * @param string $value
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function set(string $name, string $value) : void {
        /**
         * @var $element KeyValue
         */
        $element = $this->repository()->findOneBy(["name" => $name]);;
        if (isset($element)) {
            $element->setValue($value);
        } else {
            $element = KeyValue::create(["name" => $name, "value" => $value]);
        }

        $this->entityManager()->persist($element);
        $this->entityManager()->flush();
    }
}
