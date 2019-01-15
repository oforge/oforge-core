<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 15.01.2019
 * Time: 10:38
 */

namespace Oforge\Engine\Modules\CMS\Models\Page;

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
     * @ORM\Column(name="default_lang", type="string", nullable=false)
     */
    private $defaultLang;
    
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
    public function getDefaultLang(): string {
        return $this->defaultLang;
    }
    
    /**
     * @param string $defaultLang
     */
    public function setDefaultLang( string $defaultLang ): void {
        $this->defaultLang = $defaultLang;
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
}
