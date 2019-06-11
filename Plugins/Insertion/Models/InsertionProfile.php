<?php

namespace Insertion\Models;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Media\Models\Media;

/**
 * @ORM\Table(name="oforge_insertion_profile")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 */
class InsertionProfile extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="FrontendUserManagement\Models\User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Datetime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var Datetime
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\Media\Models\Media", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="main_media_id", referencedColumnName="id")
     */
    private $main;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\Media\Models\Media", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="background_media_id", referencedColumnName="id")
     */
    private $background;

    /**
     * @var string
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(name="imprint_name", type="string")
     */
    private $imprintName;

    /**
     * @var string
     * @ORM\Column(name="imprint_street", type="string")
     */
    private $imprintStreet;
    /**
     * @var string
     * @ORM\Column(name="imprint_zip_city", type="string")
     */
    private $imprintZipCity;
    /**
     * @var string
     * @ORM\Column(name="imprint_phone", type="string")
     */
    private $imprintPhone;
    /**
     * @var string
     * @ORM\Column(name="imprint_email", type="string")
     */
    private $imprintEmail;
    /**
     * @var string
     * @ORM\Column(name="imprint_company_tax_id", type="string")
     */
    private $imprintCompanyTaxId;
    /**
     * @var string
     * @ORM\Column(name="imprint_company_number", type="string")
     */
    private $imprintCompanyNumber;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist() {
        $date            = new \DateTime('now');
        $this->createdAt = $date;
        $this->updatedAt = $date;
    }

    public function __construct() {
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate() {
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getUser() : ?User {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return InsertionProfile
     */
    public function setUser($user) : InsertionProfile {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Media
     */
    public function getMain() : ?Media {
        return $this->main;
    }

    /**
     * @param Media $main
     *
     * @return InsertionProfile
     */
    public function setMain(Media $main) : InsertionProfile {
        $this->main = $main;

        return $this;
    }

    /**
     * @return Media
     */
    public function getBackground() : ?Media {
        return $this->background;
    }

    /**
     * @param Media $background
     *
     * @return InsertionProfile
     */
    public function setBackground(Media $background) : InsertionProfile {
        $this->background = $background;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() : ?string {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return InsertionProfile
     */
    public function setDescription(string $description) : InsertionProfile {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getImprintName() : ?string {
        return $this->imprintName;
    }

    /**
     * @param string $imprintName
     *
     * @return InsertionProfile
     */
    public function setImprintName(string $imprintName) : InsertionProfile {
        $this->imprintName = $imprintName;

        return $this;
    }

    /**
     * @return string
     */
    public function getImprintStreet() : ?string {
        return $this->imprintStreet;
    }

    /**
     * @param string $imprintStreet
     *
     * @return InsertionProfile
     */
    public function setImprintStreet(string $imprintStreet) : InsertionProfile {
        $this->imprintStreet = $imprintStreet;

        return $this;
    }

    /**
     * @return string
     */
    public function getImprintZipCity() : ?string {
        return $this->imprintZipCity;
    }

    /**
     * @param string $imprintZipCity
     *
     * @return InsertionProfile
     */
    public function setImprintZipCity(string $imprintZipCity) : InsertionProfile {
        $this->imprintZipCity = $imprintZipCity;

        return $this;
    }

    /**
     * @return string
     */
    public function getImprintPhone() : ?string {
        return $this->imprintPhone;
    }

    /**
     * @param string $imprintPhone
     *
     * @return InsertionProfile
     */
    public function setImprintPhone(string $imprintPhone) : InsertionProfile {
        $this->imprintPhone = $imprintPhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getImprintEmail() : ?string {
        return $this->imprintEmail;
    }

    /**
     * @param string $imprintEmail
     *
     * @return InsertionProfile
     */
    public function setImprintEmail(string $imprintEmail) : InsertionProfile {
        $this->imprintEmail = $imprintEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getImprintCompanyTaxId() : ?string {
        return $this->imprintCompanyTaxId;
    }

    /**
     * @param string $imprintCompanyTaxId
     *
     * @return InsertionProfile
     */
    public function setImprintCompanyTaxId(string $imprintCompanyTaxId) : InsertionProfile {
        $this->imprintCompanyTaxId = $imprintCompanyTaxId;

        return $this;
    }

    /**
     * @return string
     */
    public function getImprintCompanyNumber() : ?string {
        return $this->imprintCompanyNumber;
    }

    /**
     * @param string $imprintCompanyNumber
     *
     * @return InsertionProfile
     */
    public function setImprintCompanyNumber(string $imprintCompanyNumber) : InsertionProfile {
        $this->imprintCompanyNumber = $imprintCompanyNumber;

        return $this;
    }

}
