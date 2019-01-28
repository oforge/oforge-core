<?php

namespace Oforge\Engine\Modules\TemplateEngine\Services;

use GetOpt\ArgumentException;
use Oforge\Engine\Modules\TemplateEngine\Models\ScssVariable;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\TemplateEngine\Models\ScssVariableType;
use phpDocumentor\Reflection\Types\Context;

/**
 * Class ScssVariableService
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Services
 */
class ScssVariableService {
    private $em;
    private $repo;
    
    public function __construct() {
        $this->em   = Oforge()->DB()->getManager();
        $this->repo = $this->em->getRepository(ScssVariable::class);
    }

    /**
     * @param $scope
     *
     * @return array|object[]
     */
    public function get($scope) {
        return $this->repo->findBy(['scope' => $scope]);
    }

    /**
     * @param $id
     * @param $value
     * @param $type
     * @param string $siteId
     *
     * @throws NotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function post($id, $value, $type, $siteId = "0") {
        if(!$this->isScssVariableType($type)) {
            throw new ArgumentException();
        }

        $options = array(
            'id'     => $id,
            'value'  => $value,
            'type'   => $type,
            'siteId' => $siteId,
        );
        
        $element = $this->repo->findOneBy(["id" => $options["id"]]);
        if (!isset($element)) {
            throw new NotFoundException("Element with id " . $options["id"] . " not found!");
        }
        
        $element->fromArray($options);
        $this->em->persist($element);
        $this->em->flush();
    }

    /**
     * @param $name
     * @param $value
     * @param $scope
     * @param $type
     * @param int $siteId
     * @param string $context
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function put($name, $value, $scope, $type, $siteId = null, $context = null) {
        if(!$this->isScssVariableType($type)) {
            throw new \InvalidArgumentException('type must be ScssVariableType');
        }

        $options = array(
            'name'    => $name,
            'value'   => $value,
            'scope'   => $scope,
            'type'    => $type,
            'siteId'  => $siteId,
            'context' => $context,
        );

        $element = $this->repo->findOneBy(["name" => $options["name"]]);
        if (!isset($element)) {
            $element = new ScssVariable();
        }

        $element->fromArray($options);
        $this->em->persist($element);
        $this->em->flush();
    }

    /**
     * @param $type
     *
     * @return bool
     */
    private function isScssVariableType($type) : bool {
        $scssTypes = array(ScssVariableType::BOOL, ScssVariableType::LIST, ScssVariableType::MAP,
                           ScssVariableType::NULL,ScssVariableType::NUMBER, ScssVariableType::STRING);
        return in_array($type, $scssTypes);
    }
}
