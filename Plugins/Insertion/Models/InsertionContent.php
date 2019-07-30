<?php

namespace Insertion\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\I18n\Models\Language;

/**
 * @ORM\Table(name="oforge_insertion_content")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 */
class InsertionContent extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Language
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\I18n\Models\Language", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    private $language;

    /**
     * @var Insertion
     * @ORM\ManyToOne(targetEntity="Insertion\Models\Insertion", inversedBy="content", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="insertion_id", referencedColumnName="id")
     */
    private $insertion;


    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", nullable=false)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @return int
     */
    public function getId() : ?int {
        return $this->id;
    }

    /**
     * @return Language
     */
    public function getLanguage() : Language {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language) : void {
        $this->language = $language;
    }

    /**
     * @return Insertion
     */
    public function getInsertion() : ?Insertion {
        return $this->insertion;
    }

    /**
     * @param Insertion $insertion
     */
    public function setInsertion(Insertion $insertion) : void {
        $this->insertion = $insertion;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTitle() : string {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title) : void {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription() : string {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description) : void {
        $this->description = $description;
    }

}
