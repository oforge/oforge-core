<?php

namespace FrontendUserManagement\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Media\Models\Media;

/**
 * @ORM\Entity
 * @ORM\Table(name="frontend_user_management_user_detail")
 */
class UserDetail extends AbstractModel {
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
     * @var string|null $contactEmail
     * @ORM\Column(name="contact_email", type="string", nullable=false)
     */
    private $contactEmail;
    /**
     * @var string|null $phoneNumber
     * @ORM\Column(name="phone_number", type="string", nullable=true)
     */
    private $phoneNumber;
    /**
     * @var User $user
     * @ORM\OneToOne(targetEntity="User", inversedBy="detail", fetch="EXTRA_LAZY")
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
     * @return UserDetail
     */
    public function setFirstName(?string $firstName) : UserDetail {
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
     * @return UserDetail
     */
    public function setLastName(?string $lastName) : UserDetail {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNickName() : string {
        return $this->nickName;
    }

    /**
     * @param string $nickName
     *
     * @return UserDetail
     */
    public function setNickName(?string $nickName) : UserDetail {
        $this->nickName = $nickName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContactEmail() : ?string {
        return $this->contactEmail;
    }

    /**
     * @param string|null $contactEmail
     *
     * @return UserDetail
     */
    public function setContactEmail(?string $contactEmail) : UserDetail {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber() : ?string {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     *
     * @return UserDetail
     */
    public function setPhoneNumber(?string $phoneNumber) : UserDetail {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser() : User {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return UserDetail
     */
    public function setUser(User $user) : UserDetail {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Media
     */
    public function getImage() : ?Media {
        return $this->image;
    }

    /**
     * @param Media $image
     *
     * @return UserDetail
     */
    public function setImage(Media $image) : UserDetail {
        $this->image = $image;

        return $this;
    }

}
