<?php

namespace Oforge\Engine\Modules\TemplateEngine\Services;

use Oforge\Engine\Modules\TemplateEngine\Exceptions\InvalidScssVariableException;
use Oforge\Engine\Modules\TemplateEngine\Models\ScssVariable;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;

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
     * @param $context
     *
     * @return array|object[]
     */
    public function get($context, $scope='Frontend') {
        return $this->repo->findBy(['scope' => $scope, 'context' => $context]);
    }

    public function getScope($scope) {
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
    public function update($id, $value, $type, $siteId = "0") {

        $options = [
            'id'     => $id,
            'value'  => $value,
            'type'   => $type,
            'siteId' => $siteId,
        ];

        $element = $this->repo->findOneBy(["id" => $options["id"]]);
        if (!isset($element)) {
            throw new NotFoundException("Element with id " . $options["id"] . " not found!");
        }

        $element->fromArray($options);
        $this->em->persist($element);
        $this->em->flush();
    }

    /**
     * @param $templateVariable
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws InvalidScssVariableException
     */
    public function add($templateVariable) {

        if (!isset($templateVariable['scope'])) {
            $templateVariable['scope'] = 'Frontend';
        }

        if (!isset($templateVariable['siteId'])) {
            $templateVariable['siteId'] = 0;
        }

        $this->isValid($templateVariable);

        $element = $this->repo->findOneBy([
            'name'    => $templateVariable['name'],
            'context' => $templateVariable['context'],
            'scope' => $templateVariable['scope'],
            'siteId'  => $templateVariable['siteId'],
        ]);

        if (!isset($element)) {
            $element = new ScssVariable();

            $element->fromArray($templateVariable);
            $this->em->persist($element);
            $this->em->flush();
        }
    }

    /**
     * private function isScssVariableType($type) : bool {
     * $scssTypes = array(ScssVariableType::BOOL, ScssVariableType::LIST, ScssVariableType::MAP,
     * ScssVariableType::NULL,ScssVariableType::NUMBER, ScssVariableType::STRING);
     * return in_array($type, $scssTypes);
     * }
     * /**
     *
     * @param $templateVariable
     *
     * @return bool
     * @throws InvalidScssVariableException
     */
    private function isValid($templateVariable) {
        $options = [
            'name',
            'value',
            'type',
            'context',
        ];

        foreach ($options as $option) {
            if (!isset($templateVariable[$option])) {
                throw new InvalidScssVariableException($option, $templateVariable);
            }
        }

        if (isset($templateVariable['scope'])) {
            $scopes = ['Frontend', 'Backend'];
            if (!in_array($templateVariable['scope'], $scopes)) {
                throw new InvalidScssVariableException('scope', $templateVariable);
            }
        }
        return true;
    }
}
