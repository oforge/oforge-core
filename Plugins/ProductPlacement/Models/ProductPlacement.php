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
     * @var string
     * @ORM\Column(name="format", type="string", nullable=false)
     */
    private $format;

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
     * @return string
     */
    public function getFormat() : string {
        return $this->format;
    }

    /**
     * @param string $format
     *
     * @return ProductPlacement
     */
    public function setFormat(string $format) : ProductPlacement {
        $this->format = $format;

        return $this;
    }
}
