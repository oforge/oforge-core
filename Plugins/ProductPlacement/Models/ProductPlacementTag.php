<?php

namespace ProductPlacement\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_product_placement_tag")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ProductPlacementTag extends AbstractModel {
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Id
     * @var integer
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="tag_name", type="string", nullable=false)
     */
    private $name;

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
     * @return ProductPlacementTag
     */
    public function setName(string $name) : ProductPlacementTag {
        $this->name = $name;

        return $this;
    }

}
