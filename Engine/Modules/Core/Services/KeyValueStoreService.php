<?php

namespace Oforge\Engine\Modules\Core\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Models\Store\KeyValue;

/**
 * Class KeyValueStoreService
 *
 * @package Oforge\Engine\Modules\Core\Services
 */
class KeyValueStoreService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(KeyValue::class);
    }

    /**
     * Get the value of a specific key from the key-value table
     *
     * @param string $name
     * @param string|null $default
     *
     * @return mixed
     */
    public function get(string $name, ?string $default = null) {
        try {
            /** @var KeyValue $entity */
            $entity = $this->repository()->findOneBy(['name' => $name]);

            return isset($entity) ? $entity->getValue() : $default;
        } catch (ORMException $exception) {
            Oforge()->Logger()->logException($exception);

            return $default;
        }
    }

    public function remove(string $name) {
        /** @var KeyValue $entity */
        $entity = $this->repository()->findOneBy(['name' => $name]);
        $this->entityManager()->remove($entity);
    }

    /**
     * Create or update a key-value entry
     *
     * @param string $name
     * @param string $value
     *
     * @throws ORMException
     */
    public function set(string $name, string $value) : void {
        /** @var KeyValue $entity */
        $entity = $this->repository()->findOneBy(['name' => $name]);;
        if (isset($entity)) {
            $entity->setValue($value);
            $this->entityManager()->update($entity);
        } else {
            $entity = KeyValue::create([
                'name'  => $name,
                'value' => $value,
            ]);
            $this->entityManager()->create($entity);
        }
    }

}
