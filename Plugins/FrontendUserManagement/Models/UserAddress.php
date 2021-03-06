<?php
namespace FrontendUserManagement\Models;

use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * The User Address
 * Currently a user can have two addresses.
 * One normal address and one differing billing address.
 *
 * @ORM\Entity
 * @ORM\Table(name="frontend_user_management_user_address")
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
     * @var string|null $title
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string|null $firstname
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    private $firstname;

    /**
     * @var string|null $lastname
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    private $lastname;

    /**
     * @var string|null $streetName
     * @ORM\Column(name="street_name", type="string", nullable=true)
     */
    private $streetName;
    /**
     * @var string|null $streetNumber
     * @ORM\Column(name="street_number", type="string", nullable=true)
     */
    private $streetNumber;
    /**
     * @var string|null $postCode
     * @ORM\Column(name="post_code", type="string", nullable=true)
     */
    private $postCode;
    /**
     * @var string|null $city
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    private $city;
    /**
     * @var string|null $country
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    private $country = "Germany";

    /**
     * @var string|null $company
     * @ORM\Column(name="company", type="string", nullable=true)
     */
    private $company;

    /**
     * @var bool $isBillingAddress
     * @ORM\Column(name="is_billing_address", type="boolean", nullable=false)
     */
    private $isBillingAddress = true;
    /**
     * @var User $user
     * @ORM\OneToOne(targetEntity="User", inversedBy="address", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getStreetName() : ?string {
        return $this->streetName;
    }

    /**
     * @param string|null $streetName
     *
     * @return UserAddress
     */
    public function setStreetName(?string $streetName) : UserAddress {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStreetNumber() : ?string {
        return $this->streetNumber;
    }

    /**
     * @param string|null $streetNumber
     *
     * @return UserAddress
     */
    public function setStreetNumber(?string $streetNumber) : UserAddress {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostCode() : ?string {
        return $this->postCode;
    }

    /**
     * @param string|null $postCode
     *
     * @return UserAddress
     */
    public function setPostCode(?string $postCode) : UserAddress {
        $this->postCode = $postCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity() : ?string {
        return $this->city;
    }

    /**
     * @param string|null $city
     *
     * @return UserAddress
     */
    public function setCity(?string $city) : UserAddress {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry() : ?string {
        return $this->country;
    }

    /**
     * @param string|null $country
     *
     * @return UserAddress
     */
    public function setCountry(?string $country) : UserAddress {
        $this->country = $country;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBillingAddress() : bool {
        return $this->isBillingAddress;
    }

    /**
     * @param bool $isBillingAddress
     *
     * @return UserAddress
     */
    public function setIsBillingAddress(bool $isBillingAddress) : UserAddress {
        $this->isBillingAddress = $isBillingAddress;

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
     * @return UserAddress
     */
    public function setUser(User $user) : UserAddress {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCompany() : ?string {
        return $this->company;
    }

    /**
     * @param string|null $company
     *
     * @return UserAddress
     */
    public function setCompany(?string $company) : UserAddress {
        $this->company = $company;

        return $this;
    }


    /**
     * @return string|null
     */
    public function getTitle() : ?string {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return UserAddress
     */
    public function setTitle(?string $title) : UserAddress {
        $this->title = $title;

        return $this;
    }


    /**
     * @return string|null
     */
    public function getFirstname() : ?string {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     *
     * @return UserAddress
     */
    public function setFirstname(?string $firstname) : UserAddress {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname() : ?string {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     *
     * @return UserAddress
     */
    public function setLastname(?string $lastname) : UserAddress {
        $this->lastname = $lastname;

        return $this;
    }
}
