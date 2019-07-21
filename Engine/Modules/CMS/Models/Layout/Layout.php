<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 15.01.2019
 * Time: 10:48
 */

namespace Oforge\Engine\Modules\CMS\Models\Layout;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_cms_layout")
 * @ORM\Entity
 */
class Layout extends AbstractModel
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
     * @ORM\Column(name="layout_name", type="string", nullable=false, unique=true)
     */
    private $name;
    
    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Slot", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="oforge_cms_layout_slot",
     *     joinColumns={@ORM\JoinColumn(name="layout_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="slot_id", referencedColumnName="id")})
     */
    private $slots;
    
    public function __construct()
    {
        $this->slots = new ArrayCollection();
    }
    
    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     * 
     * @return Layout
     */
    public function setName(string $name): Layout
    {
        $this->name = $name;
        return $this;
    }
}
