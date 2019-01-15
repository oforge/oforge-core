<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 15.01.2019
 * Time: 10:34
 */

namespace Oforge\Engine\Modules\CMS\Models\Site;

use Oforge\Engine\Modules\CMS\Models\Page\Page;

/**
 * @ORM\Table(name="oforge_cms_site")
 * @ORM\Entity
 */
class Site extends Page
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
     * @ORM\Column(name="domain", type="string", nullable=false, unique=true)
     */
    private $domain;
    
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
    public function getDomain(): string {
        return $this->domain;
    }
    
    /**
     * @param string $domain
     */
    public function setDomain( string $domain ): void {
        $this->domain = $domain;
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
}
