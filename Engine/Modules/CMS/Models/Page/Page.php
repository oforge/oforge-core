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
     * @var int
     * @ORM\OneToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Layout\Layout")
     * @ORM\JoinColumn(name="layout", referencedColumnName="id")
     * @ORM\Column(name="layout_id", type="integer")
     */
    private $layout;
    
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Site\Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     * @ORM\Column(name="site_id", type="integer")
     */
    private $site;

    /**
     * @var int
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parent;
    
    /**
     * @var string
     * @ORM\Column(name="page_name", type="string", nullable=false, unique=true)
     */
    private $name;
    
    /**
     * @var PagePath[]
     * @ORM\OneToMany(targetEntity="Oforge\Engine\Modules\CMS\Models\Page\PagePath", mappedBy="page", cascade={"all"})
     * @ORM\JoinColumn(name="id", referencedColumnName="page_id")
     */
    private $paths;
    
    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getLayout(): int
    {
        return $this->layout;
    }

    /**
     * @param int $layout
     */
    public function setLayout(int $layout)
    {
        $this->layout = $layout;
    }
    
    /**
     * @return int
     */
    public function getSite(): int
    {
        return $this->site;
    }
    
    /**
     * @param int $site
     */
    public function setSite(int $site)
    {
        $this->site = $site;
    }
    
    /**
     * @return int
     */
    public function getParent(): int
    {
        return $this->parent;
    }
    
    /**
     * @param int $parent
     */
    public function setParent(int $parent)
    {
        $this->parent = $parent;
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
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    
    /**
     * @return PagePath[]
     */
    public function getPaths()
    {
        return $this->paths;
    }
    
    /**
     * @param PagePath[] $paths
     */
    public function setPaths(array $paths)
    {
        $this->paths = $paths;
    }
}
