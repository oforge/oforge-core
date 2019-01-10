<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:11
 */

namespace Oforge\Engine\Modules\CRUD\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;


/**
 * Class GenericCrudService
 * @package Oforge\Engine\Modules\CRUD\Services;
 */
class GenericCrudService
{
    /**
     * GenericCrudService constructor.
     */
    public function __construct()
    {
        $this->em = Oforge()->DB()->getManager();
    }

    public function list($class, $params = [])
    {
        $repo = $this->getRepo($class);

        /**
         * @var $items AbstractModel[]
         */
        $items = [];
        //$repo->findAll();

        if(sizeof($params) > 0 ) {
            //TODO
        } else {
            $items = $repo->findAll();
        }

        $result = [];
        foreach ($items as $item) {

            array_push($result, $item->toArray());
        }

        return $result;
    }
    
    /**
     * @param int $id
     *
     * @return object|null
     */
    public function getById($class, int $id) {
        $repo = $this->getRepo($class);
        $result = $repo->findOneBy(["id" => $id]);
        return $result;
    }

    public function create($class, array $options)
    {
        $repo = $this->getRepo($class);

        $element = $repo->findOneBy(["id" => $options["id"]]);
        if (isset($element)) {
            throw new ConfigElementAlreadyExists("Element with id " . $options["id"] . " already exists!");
        }

        /**
         * @var $instance AbstractModel
         */
        $instance = new $class();
        $instance->fromArray($options);

        $this->em->persist($instance);
    }

    public function update($class, array $options)
    {
        $repo = $this->getRepo($class);

        $element = $repo->findOneBy(["id" => $options["id"]]);
        if (!isset($element)) {
            throw new NotFoundException("Element with id " . $options["id"] . " not found!");
        }

        $element->fromArray($options);
        $this->em->persist($element);
        $this->em->flush();
    }

    public function delete($class, int $id)
    {
        $repo = $this->getRepo($class);

        $element = $repo->findOneBy(["id" => $id]);

        $this->em->remove($element);
        $this->em->flush();
    }

    /**
     * @param $class
     * @return \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository
     */
    private function getRepo($class)
    {
        return $this->em->getRepository($class);
    }
}
