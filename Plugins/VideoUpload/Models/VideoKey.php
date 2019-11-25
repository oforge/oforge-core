<?php


namespace VideoUpload\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion_video_key")
 * @ORM\Entity
 */
class VideoKey extends AbstractModel
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
     * @ORM\Column(name="inserion_id", type="integer", nullable=false)
     */
    private $insertionId;

    /**
     * @var string
     * @ORM\Column(name="video_key", type="string", nullable=false)
     */
    private $videoKey;


    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $insertionId
     *
     * @return VideoKey
     */
    public function setInsertionId($insertionId) : VideoKey
    {
        $this->insertionId = $insertionId;

        return $this;
    }

    /**
     * @return int
     */
    public function getInsertionId() : int
    {
        return $this->insertionId;
    }

    /**
     * @param string $videoKey
     *
     * @return VideoKey
     */
    public function setVideoKey($videoKey) : VideoKey
    {
        $this->videoKey = $videoKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVideoKey() : string
    {
        return $this->videoKey;
    }
}
