<?php

namespace Oforge\Engine\Modules\CMS\Models\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_cms_content_type_group")
 * @ORM\Entity
 */
class ContentTypeGroup extends AbstractModel {
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="ContentType", inversedBy="child_types")
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(name="content_type_name", type="string", nullable=false, unique=true)
     */
    private $name;
    
    /**
     * @return int
     */
    public function getId() : ?int {
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
     */
    public function setName(string $name) : void {
        $this->name = $name;
    }
}