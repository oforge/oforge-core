<?php

namespace Oforge\Engine\Modules\TemplateEngine\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_template_engine_scss_variables")
 * @ORM\Entity
 */
class ScssVariable extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="value", type="string", nullable=false)
     */
    private $value;

    /**
     * @var string
     * @ORM\Column(name="scope", type="string", nullable=false)
     */
    private $scope;

    /**
     * @var int
     * @ORM\Column(name="siteId", type="integer", nullable=true)
     */
    private $siteId = null;

    /**
     * @var string
     * @ORM\Column(name="context", type="string", nullable=true)
     */
    private $context = null;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return ScssVariable
     */
    public function setName(string $name) {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue() : string {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return ScssVariable
     */
    public function setValue(string $value) {
        $this->value = $value;

        return $this;
    }

    /**
     * @return int $siteId
     */
    public function getSiteId() : int {
        return $this->siteId;
    }

    /**
     * @param int $siteId
     *
     * @return ScssVariable
     */
    public function setSiteId(int $siteId) {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * @return string
     */
    public function getScope() : string {
        return $this->scope;
    }

    /**
     * @param string $scope
     *
     * @return ScssVariable
     */
    public function setScope(string $scope) {
        $this->scope = $scope;

        return $this;
    }

    /**
     * @return string
     */
    public function getType() : string {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return ScssVariable
     */
    public function setType(string $type) : ScssVariable {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContext() {
        return $this->context;
    }

    /**
     * @param mixed $context
     *
     * @return ScssVariable
     */
    public function setContext($context) : ScssVariable {
        $this->context = $context;

        return $this;
    }

}
