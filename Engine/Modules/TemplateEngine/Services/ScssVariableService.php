<?php

namespace Oforge\Engine\Modules\TemplateEngine\Services;

use Oforge\Engine\Modules\TemplateEngine\Models\ScssVariable;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\TemplateEngine\Models\ScssVariableType;

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
     * @return Array of SCSS Variables
     */
    public function get($scope) {
        return $this->repo->findBy(['scope' => $scope]);
    }
    
    /*s
     */
    public function post($id, $value, $type, $siteId = "0") {
        $options = array(
            'id' => $id,
            'value' => $value,
            'type' => $type,
            'siteId' => $siteId
        );
        
        $element = $this->repo->findOneBy(["id" => $options["id"]]);
        if (!isset($element)) {
            throw new NotFoundException("Element with id " . $options["id"] . " not found!");
        }
        
        $element->fromArray($options);
        $this->em->persist($element);
        $this->em->flush();
    }
}
