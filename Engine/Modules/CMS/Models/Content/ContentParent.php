<?php

namespace Oforge\Engine\Modules\CMS\Models\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\CMS\Models\Page\PageContent;

/**
 * @ORM\Table(name="oforge_cms_content_parent")
 * @ORM\Entity
 */
class ContentParent extends AbstractModel
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
     * @ORM\Column(name="description", type="string", nullable=false)
     */
    private $description;
    
    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @param int $id
     *
     * @return ContentParent
     */
    public function setId(int $id): ContentParent
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
    
    /**
     * @param string $description
     *
     * @return ContentParent
     */
    public function setDescription(string $description): ContentParent
    {
        $this->description = $description;
        return $this;
    }
}
