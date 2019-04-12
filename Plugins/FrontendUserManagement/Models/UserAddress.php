<?php
namespace FrontendUserManagement\Models;

use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * The User Address
 * Currently a user can have two addresses.
 * One normal address and one differing billing address.
 *
 * @ORM\Table(name="frontend_user_management_user_address")
 * @ORM\Entity
 */
class UserAddress extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="street_name", type="string", nullable=false)
     */
    private $streetName;

    /**
     * @var string
     * @ORM\Column(name="street_number", type="string", nullable=false)
     */
    private $streetNumber;

    /**
     * @var string
     * @ORM\Column(name="post_code", type="string", nullable=false)
     */
    private $postCode;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", nullable=false)
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(name="country", type="string", nullable=false)
     */
    private $country = "Germany";

    /**
     * @var bool
     * @ORM\Column(name="is_billing_address", type="boolean", nullable=false)
     */
    private $isBillingAddress = true;

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
     * @param string $streetName
     * @return UserAddress
     */
    public function setStreetName(string $streetName): UserAddress
    {
        $this->streetName = $streetName;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreetName(): string
    {
        return $this->streetName;
    }

    /**
     * @param string $streetNumber
     * @return UserAddress
     */
    public function setStreetNumber(string $streetNumber): UserAddress
    {
        $this->streetNumber = $streetNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreetNumber(): string
    {
        return $this->streetNumber;
    }

    /**
     * @param string $postCode
     * @return UserAddress
     */
    public function setPostCode(string $postCode): UserAddress
    {
        $this->postCode = $postCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostCode(): string
    {
        return $this->postCode;
    }

    /**
     * @param string $city
     * @return UserAddress
     */
    public function setCity(string $city): UserAddress
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $country
     * @return UserAddress
     */
    public function setCountry(string $country): UserAddress
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param mixed $isBillingAddress
     * @return UserAddress
     */
    public function setIsBillingAddress($isBillingAddress)
    {
        $this->isBillingAddress = $isBillingAddress;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getisBillingAddress()
    {
        return $this->isBillingAddress;
    }

    /**
     * @param mixed $userID
     * @return UserAddress
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
