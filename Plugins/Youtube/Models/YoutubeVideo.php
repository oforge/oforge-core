<?php

namespace Youtube\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion_video_youtube")
 * @ORM\Entity
 */
class YoutubeVideo extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="inserion_id", type="integer", nullable=false)
     */
    private $insertionId;

    /**
     * @var string
     * @ORM\Column(name="video_key", type="string", nullable=false)
     */
    private $videoKey;

    /**
     * @var string
     * @ORM\Column(name="video_thumbnail", type="string", nullable=true)
     */
    private $thumbnail;

    public function __construct() {
    }

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @param int $insertionId
     *
     * @return YoutubeVideo
     */
    public function setInsertionId($insertionId) : YoutubeVideo {
        $this->insertionId = $insertionId;

        return $this;
    }

    /**
     * @return int
     */
    public function getInsertionId() : int {
        return $this->insertionId;
    }

    /**
     * @param string $videoKey
     *
     * @return YoutubeVideo
     */
    public function setVideoKey($videoKey) : YoutubeVideo {
        $this->videoKey = $videoKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVideoKey() : string {
        return $this->videoKey;
    }

    /**
     * @return string
     */
    public function getThumbnail() : ?string {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     *
     * @return YoutubeVideo
     */
    public function setThumbnail(string $thumbnail) : YoutubeVideo {
        $this->thumbnail = $thumbnail;

        return $this;
    }
}
