<?php
namespace FrontendUserManagement\Models;

use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="frontend_user_management_user_detail")
 * @ORM\Entity
 */
class UserDetail extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="first_name", type="string", nullable=false)
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", nullable=false)
     */
    private $lastName;

    /**
     * @var string
     * @ORM\Column(name="nick_name", type="string", nullable=false)
     */
    private $nickName;

    /**
     * @var string
     * @ORM\Column(name="contact_email", type="string", nullable=true)
     */
    private $contactEmail;

    /**
     * @var string
     * @ORM\Column(name="phone_number", type="string", nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userID;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return UserDetail
     */
    public function setFirstName(string $firstName): UserDetail
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return UserDetail
     */
    public function setLastName(string $lastName): UserDetail
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @param string $nickName
     * @return UserDetail
     */
    public function setNickName(string $nickName): UserDetail
    {
        $this->nickName = $nickName;
        return $this;
    }

    /**
     * @return string
     */
    public function getNickName(): string
    {
        return $this->nickName;
    }

    /**
     * @param string $contactEmail
     * @return UserDetail
     */
    public function setContactEmail(string $contactEmail): UserDetail
    {
        $this->contactEmail = $contactEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    /**
     * @param string $phoneNumber
     * @return UserDetail
     */
    public function setPhoneNumber(string $phoneNumber): UserDetail
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @param mixed $userID
     * @return UserDetail
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserID()
    {
        return $this->userID;
    }
}