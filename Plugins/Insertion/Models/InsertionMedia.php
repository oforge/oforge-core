<?php

namespace Insertion\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Media\Models\Media;

/**
 * @ORM\Table(name="oforge_insertion_media")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 */
class InsertionMedia extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var Insertion
     * @ORM\ManyToOne(targetEntity="Insertion\Models\Insertion", inversedBy="media", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="insertion_id", referencedColumnName="id")
     */
    private $insertion;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\Media\Models\Media", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     */
    private $content;


    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;


    /**
     * @var int
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    private $order;

    /**
     * @return int
     */
    public function getId() : ?int {
        return $this->id;
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
     * @return Media
     */
    public function getContent() : Media {
        return $this->content;
    }

    /**
     * @param Media $content
     */
    public function setContent(Media $content) : void {
        $this->content = $content;
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
     * @return int
     */
    public function getOrder() : ?int {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order) : void {
        $this->order = $order;
    }

}
