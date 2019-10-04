<?php

namespace ProductPlacement\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_product_placement")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ProductPlacement extends AbstractModel {
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Id
     * @var integer
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="source", type="string", nullable=false)
     */
    private $source;

    /**
     * @var array
     * @ORM\Column(name="tags", type="array", nullable=false)
     */
    private $tags;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSource() : string {
        return $this->source;
    }

    /**
     * @param string $source
     *
     * @return ProductPlacement
     */
    public function setSource(string $source) : ProductPlacement {
        $this->source = $source;

        return $this;
    }

    /**
     * @return array
     */
    public function getTags() : array {
        return $this->tags;
    }

    /**
     * @param array $tags
     *
     * @return ProductPlacement
     */
    public function setTags(array $tags) : ProductPlacement {
        $this->tags = $tags;

        return $this;
    }
}
