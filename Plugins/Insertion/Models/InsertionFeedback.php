<?php

namespace Insertion\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\I18n\Models\Language;

/**
 * @ORM\Table(name="oforge_insertion_feedback")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 */
class InsertionFeedback extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="rating", type="integer", nullable=false)
     */
    private $rating;

    /**
     * @var string
     * @ORM\Column(name="text", type="string", nullable=false)
     */
    private $text;

    /**
     * @return int
     */
    public function getId() : ?int {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getRating() : int {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating(int $rating) : void {
        $this->rating = $rating;
    }

    /**
     * @return string
     */
    public function getText() : string {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text) : void {
        $this->text = $text;
    }

}




