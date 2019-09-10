<?php

namespace Insertion\Models;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 */
class Insertion extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="InsertionType", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="insertion_type_id", referencedColumnName="id")
     */
    private $insertionType;

    /**
     * @ORM\ManyToOne(targetEntity="FrontendUserManagement\Models\User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="insertion_user", referencedColumnName="id")
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
     * @var InsertionMedia[]
     * @ORM\OneToMany(targetEntity="InsertionMedia", mappedBy="insertion", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="insertion_id")
     * @ORM\OrderBy({"isMain" = "DESC"})
     */
    private $media;

    /**
     * @var InsertionContent[]
     * @ORM\OneToMany(targetEntity="InsertionContent", mappedBy="insertion", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="insertion_id")
     */
    private $content;

    /**
     * @var InsertionContact
     * @ORM\OneToOne(targetEntity="InsertionContact", mappedBy="insertion", cascade={"all"}, fetch="EXTRA_LAZY")
     */
    private $contact;

    /**
     * @var InsertionAttributeValue[]
     * @ORM\OneToMany(targetEntity="InsertionAttributeValue", mappedBy="insertion", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="insertion_id")
     */
    private $values;

    /**
     * @var float
     * @ORM\Column(name="price", type="float")
     */
    private $price = 0;

    /**
     * @var float
     * @ORM\Column(name="min_price", type="float", nullable=true)
     */
    private $minPrice = null;

    /**
     * @var string
     * @ORM\Column(name="price_type", type="string")
     */
    private $priceType = "fixed";

    /**
     * @var boolean
     * @ORM\Column(name="tax", type="boolean", nullable=true)
     */
    private $tax = false;

    /**
     * @var boolean
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active = true;

    /**
     * @var boolean
     * @ORM\Column(name="deleted", type="boolean", nullable=true, options={"default":false})
     */
    private $deleted = false;

    /**
     * @var boolean
     * @ORM\Column(name="moderation", type="boolean", options={"default":true})
     */
    private $moderation = false;


    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $date = new \DateTime('now');
        $this->createdAt = $date;
        $this->updatedAt = $date;
    }

    public function __construct()
    {
        $this->media = new ArrayCollection();
        $this->content = new ArrayCollection();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getInsertionType()
    {
        return $this->insertionType;
    }

    /**
     * @param mixed $insertionType
     *
     * @return Insertion
     */
    public function setInsertionType($insertionType)
    {
        $this->insertionType = $insertionType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return Insertion
     */
    public function setUser($user): Insertion
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return object|null
     */
    public function getMedia(): ?object
    {
        return $this->media;
    }

    /**
     * @param InsertionMedia[] $media
     */
    public function setMedia(array $media): void
    {
        $this->media = $media;
    }

    /**
     * @return InsertionContent[]
     */
    public function getContent(): ?object
    {
        return $this->content;
    }

    /**
     * @param InsertionContent[] $content
     */
    public function setContent(array $content): void
    {
        $this->content = $content;
    }

    /**
     * @return InsertionContact|null
     */
    public function getContact(): ?InsertionContact
    {
        return $this->contact;
    }

    /**
     * @param InsertionContact $contact
     */
    public function setContact(InsertionContact $contact): void
    {
        $this->contact = $contact;
    }

    /**
     * @return InsertionAttributeValue[]
     */
    public function getValues(): ?object
    {
        return $this->values;
    }

    /**
     * @param InsertionAttributeValue[] $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return bool
     */
    public function isTax(): ?bool
    {
        return $this->tax;
    }

    /**
     * @param bool $tax
     */
    public function setTax(?bool $tax): void
    {
        $this->tax = $tax;
    }

    /**
     * @return bool
     */
    public function isActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return bool
     */
    public function isModeration(): bool
    {
        return $this->moderation;
    }

    /**
     * @param bool $moderation
     */
    public function setModeration(bool $moderation): void
    {
        $this->moderation = $moderation;
    }

    /**
     * @return string
     */
    public function getPriceType(): string
    {
        return $this->priceType;
    }

    /**
     * @param string $priceType
     * @return Insertion
     */
    public function setPriceType(string $priceType): Insertion
    {
        $this->priceType = $priceType;
        return $this;
    }

    /**
     * @return null|float
     */
    public function getMinPrice(): ?float
    {
        return $this->minPrice;
    }

    /**
     * @param float|null $minPrice
     * @return Insertion
     */
    public function setMinPrice(?float $minPrice): Insertion
    {
        $this->minPrice = $minPrice;

        return $this;
    }

}
