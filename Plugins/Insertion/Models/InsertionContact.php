<?php

namespace Insertion\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion_contact")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 */
class InsertionContact extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Insertion
     * @ORM\OneToOne(targetEntity="Insertion\Models\Insertion", inversedBy="contact", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="insertion_id", referencedColumnName="id")
     */
    private $insertion;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;
    /**
     * @var string
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private $phone;

    /**
     * @var string
     * @ORM\Column(name="zip", type="string", nullable=true)
     */
    private $zip;

    /**
     * @var string
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    private $city;

    /**
     * @var string|null
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    private $country;

    /**
     * @var boolean
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @return int
     */
    public function getId() : ?int {
        return $this->id;
    }

    /**
     * @return Insertion
     */
    public function getInsertion() : ?Insertion {
        return $this->insertion;
    }

    /**
     * @param Insertion $insertion
     *
     * @return InsertionContact
     */
    public function setInsertion(Insertion $insertion) : InsertionContact {
        $this->insertion = $insertion;

        return $this;
    }

    /**
     * @return string
     */
    public function getName() : ?string {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return InsertionContact
     */
    public function setName(?string $name) : InsertionContact {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail() : ?string {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return InsertionContact
     */
    public function setEmail(?string $email) : InsertionContact {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone() : string {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return InsertionContact
     */
    public function setPhone(?string $phone) : InsertionContact {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getZip() : ?string {
        return $this->zip;
    }

    /**
     * @param string $zip
     *
     * @return InsertionContact
     */
    public function setZip(?string $zip) : InsertionContact {
        $this->zip = $zip;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity() : ?string {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return InsertionContact
     */
    public function setCity(?string $city) : InsertionContact {
        $this->city = $city;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVisible() : bool {
        return $this->visible;
    }

    /**
     * @param bool $visible
     *
     * @return InsertionContact
     */
    public function setVisible(bool $visible) : InsertionContact {
        $this->visible = $visible;

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
     */
    public function setCountry(?string $country) : void {
        $this->country = $country;
    }
}
