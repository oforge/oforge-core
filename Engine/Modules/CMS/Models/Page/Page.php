<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 15.01.2019
 * Time: 10:38
 */

namespace Oforge\Engine\Modules\CMS\Models\Page;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_cms_page")
 * @ORM\Entity
 */
class Page extends AbstractModel
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
     * @ORM\Column(name="page_name", type="string", nullable=false, unique=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\OneToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Layout\Layout")
     * @ORM\JoinColumn(name="layout", referencedColumnName="id")
     * @ORM\Column(name="layout", type="object")
     */
    private $layout;
    
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     * @ORM\Column(name="site_id", type="integer")
     */
    private $site;

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
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName( string $name ): void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     */
    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }
    
    /**
     * @return int
     */
    public function getSite() : int {
        return $this->site;
    }
    
    /**
     * @param int $site
     */
    public function setSite(int $site) : void {
        $this->site = $site;
    }
}
