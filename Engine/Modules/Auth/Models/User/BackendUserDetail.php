<?php

namespace Oforge\Engine\Modules\Auth\Models\User;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Media\Models\Media;

/**
 * @ORM\Entity
 * @ORM\Table(name="oforge_auth_backend_user_detail")
 */
class BackendUserDetail extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string|null $firstName
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    private $firstName;
    /**
     * @var string|null $lastName
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    private $lastName;
    /**
     * @var string|null $nickName
     * @ORM\Column(name="nick_name", type="string", nullable=true)
     */
    private $nickName;

    /**
     * @var BackendUser $user
     * @ORM\OneToOne(targetEntity="BackendUser", inversedBy="detail", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\Media\Models\Media", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getFirstName() : ?string {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     *
     * @return BackendUserDetail
     */
    public function setFirstName(?string $firstName) : BackendUserDetail {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName() : ?string {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     *
     * @return BackendUserDetail
     */
    public function setLastName(?string $lastName) : BackendUserDetail {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNickName() : ?string {
        return $this->nickName;
    }

    /**
     * @param string|null $nickName
     *
     * @return BackendUserDetail
     */
    public function setNickName(?string $nickName) : BackendUserDetail {
        $this->nickName = $nickName;

        return $this;
    }

    /**
     * @return BackendUser
     */
    public function getUser() : BackendUser {
        return $this->user;
    }

    /**
     * @param BackendUser
     *
     * @return BackendUserDetail
     */
    public function setUser(BackendUser $user) : BackendUserDetail {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Media
     */
    public function getImage() : Media {
        return $this->image;
    }

    /**
     * @param Media $image
     *
     * @return BackendUserDetail
     */
    public function setImage(Media $image) : BackendUserDetail {
        $this->image = $image;

        return $this;
    }

}
