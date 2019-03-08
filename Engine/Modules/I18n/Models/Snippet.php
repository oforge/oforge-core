<?php

namespace Oforge\Engine\Modules\I18n\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_i18n_snippet",
 *     uniqueConstraints={
 *        @UniqueConstraint(name="scope_name_unique",
 *            columns={"scope", "snippet_name"})
 *    }))
 * @ORM\Entity
 */
class Snippet extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(name="scope", type="string", nullable=false)
     */
    private $scope;
    
    /**
     * @var string
     * @ORM\Column(name="snippet_name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="value", type="string", nullable=false)
     */
    private $value;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     */
    public function setScope(string $scope)
    {
        $this->scope = $scope;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }
}
