<?php

namespace Oforge\Engine\Modules\Media\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_media")
 * @ORM\Entity
 */
class Media extends AbstractModel
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
    private $type;
    
    /**
     * @var string
     * @ORM\Column(name="snippet_name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="value", type="string", nullable=false)
     */
    private $path;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
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
     * @return Media
     */
    public function setType(string $type) : Media {
        $this->type = $type;
        return $this;
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
     * @return Media
     */
    public function setName(string $name) : Media {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath() : string {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return Media
     */
    public function setPath(string $path) : Media {
        $this->path = $path;
        return $this;
    }

}
