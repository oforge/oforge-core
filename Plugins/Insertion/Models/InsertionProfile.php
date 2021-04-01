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
    private $description = "";

    /**
     * @var string
     * @ORM\Column(name="imprint_name", type="string")
     */
    private $imprintName = "";

    /**
     * @var string
     * @ORM\Column(name="imprint_street", type="string")
     */
    private $imprintStreet = "";
    /**
     * @var string
     * @ORM\Column(name="imprint_zip_city", type="string")
     */
    private $imprintZipCity = "";
    /**
     * @var string
     * @ORM\Column(name="imprint_phone", type="string")
     */
    private $imprintPhone = "";
    /**
     * @var string
     * @ORM\Column(name="imprint_email", type="string")
     */
    private $imprintEmail = "";

    /**
     * @var string
     * @ORM\Column(name="imprint_facebook", type="string")
     */
    private $imprintFacebook = "";

    /**
     * @var string
     * @ORM\Column(name="imprint_website", type="string")
     */
    private $imprintWebsite = "";

    /**
     * @var string
     * @ORM\Column(name="imprint_company_tax_id", type="string")
     */
    private $imprintCompanyTaxId = "";
    /**
     * @var string
     * @ORM\Column(name="imprint_company_number", type="string")
     */
    private $imprintCompanyNumber = "";

    /**
     * @var string
     * @ORM\Column(name="profile_url", type="string")
     */
    private $profileUrl = "";

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

    public function resetBackground() {
        $this->background = null;
    }

    /**
     * @return string
     */
    public function getDescription() : ?string {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return InsertionProfile
     */
    public function setDescription(?string $description) : InsertionProfile {
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
     * @param string|null $imprintName
     *
     * @return InsertionProfile
     */
    public function setImprintName(?string $imprintName) : InsertionProfile {
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
     * @param string|null $imprintStreet
     *
     * @return InsertionProfile
     */
    public function setImprintStreet(?string $imprintStreet) : InsertionProfile {
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
     * @param string|null $imprintZipCity
     *
     * @return InsertionProfile
     */
    public function setImprintZipCity(?string $imprintZipCity) : InsertionProfile {
        $this->imprintZipCity = $imprintZipCity;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImprintPhone() : ?string {
        return $this->imprintPhone;
    }

    /**
     * @param string|null $imprintPhone
     *
     * @return InsertionProfile
     */
    public function setImprintPhone(?string $imprintPhone) : InsertionProfile {
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
     * @param string|null $imprintEmail
     *
     * @return InsertionProfile
     */
    public function setImprintEmail(?string $imprintEmail) : InsertionProfile {
        $this->imprintEmail = $imprintEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getImprintFacebook() : ?string {
        return $this->imprintFacebook;
    }

    /**
     * @param string|null $imprintFacebook
     *
     * @return InsertionProfile
     */
    public function setImprintFacebook(?string $imprintFacebook) : InsertionProfile {
        $this->imprintFacebook = $imprintFacebook;

        return $this;
    }

    /**
     * @return string
     */
    public function getImprintWebsite() : ?string {
        return $this->imprintWebsite;
    }

    /**
     * @param string|null $imprintWebsite
     *
     * @return InsertionProfile
     */
    public function setImprintWebsite(?string $imprintWebsite) : InsertionProfile {
        $this->imprintWebsite = $imprintWebsite;

        return $this;
    }

    /**
     * @return string
     */
    public function getImprintCompanyTaxId() : ?string {
        return $this->imprintCompanyTaxId;
    }

    /**
     * @param string|null $imprintCompanyTaxId
     *
     * @return InsertionProfile
     */
    public function setImprintCompanyTaxId(?string $imprintCompanyTaxId) : InsertionProfile {
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
     * @param string|null $imprintCompanyNumber
     *
     * @return InsertionProfile
     */
    public function setImprintCompanyNumber(?string $imprintCompanyNumber) : InsertionProfile {
        $this->imprintCompanyNumber = $imprintCompanyNumber;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProfileUrl() : ?string {
        return $this->profileUrl;
    }

    /**
     * @param string|null $profileUrl
     *
     * @return InsertionProfile
     */
    public function setProfileUrl(?string $profileUrl) {
        $this->profileUrl = $profileUrl;

        return $this;
    }

}
