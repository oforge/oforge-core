<?php


namespace Seo\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_seo_url")
 * @ORM\Entity
 */
class SeoUrl extends AbstractModel
{
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="target_url", type="string", nullable=false, unique=true)
     */
    private $target;

    /**
     * @var string
     * @ORM\Column(name="source_url", type="string", nullable=false)
     */
    private $source;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTarget() : string {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target) : void {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getSource() : string {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource(string $source) : void {
        $this->source = $source;
    }

}