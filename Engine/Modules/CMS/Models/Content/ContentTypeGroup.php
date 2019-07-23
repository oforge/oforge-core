<?php

namespace Oforge\Engine\Modules\CMS\Models\Content;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="oforge_cms_content_type_group")
 */
class ContentTypeGroup extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string $name
     * @ORM\Column(name="content_type_group_name", type="string", nullable=false, unique=true)
     */
    private $name;
    /**
     * @var ContentType[]
     * @ORM\OneToMany(targetEntity="Oforge\Engine\Modules\CMS\Models\Content\ContentType", mappedBy="group", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="content_type_group_id")
     */
    private $types;

    /**
     * @return int
     */
    public function getId() : ?int {
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
     * @return ContentTypeGroup
     */
    public function setName(string $name) : ContentTypeGroup {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ContentType[]
     */
    public function getContentTypes() {
        return $this->types;
    }

    /**
     * @param ContentType[] $types
     *
     * @return ContentTypeGroup
     */
    public function setContentTypes(array $types) : ContentTypeGroup {
        $this->types = $types;

        return $this;
    }

}
