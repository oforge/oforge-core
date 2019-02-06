<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 15.01.2019
 * Time: 10:34
 */

namespace Oforge\Engine\Modules\CMS\Models\Page;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_cms_site")
 * @ORM\Entity
 */
class Site extends AbstractModel
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
     * @ORM\Column(name="site_name", type="string", nullable=false, unique=true)
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="domain", type="string", nullable=false, unique=true)
     */
    private $domain;
    
    /**
     * @var int
     * @ORM\OneToOne(targetEntity="Oforge\Engine\Modules\I18n\Models\Language")
     * @ORM\JoinColumn(name="default_language_id", referencedColumnName="id")
     * @ORM\Column(name="default_language_id", type="integer")
     */
    private $default_language;
    
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
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }
    
    /**
     * @param string $domain
     */
    public function setDomain(string $domain)
    {
        $this->domain = $domain;
    }
    
    /**
     * @return int
     */
    public function getDefaultLanguage(): int
    {
        return $this->default_language;
    }
    
    /**
     * @param int $defaultLanguage
     */
    public function setDefaultLanguage(int $default_language)
    {
        $this->default_language = $default_language;
    }
}
