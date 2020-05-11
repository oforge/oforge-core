<?php

namespace TestPlugin\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="backend_test_model_relation")
 * @ORM\HasLifecycleCallbacks
 */
class TestModel extends AbstractModel
{
    /**
     * @var int id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $vorname
     * @ORM\Column(name="vorname", type="string", nullable=false)
     */
    private $vorname;

    /**
     * @var string $nachname
     * @ORM\Column(name="nachname", type="string", nullable=false)
     */
    private $nachname;


    public function __construct()
    {
        parent::__construct();
    }

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
    public function getVorname(): string
    {
        return $this->vorname;
    }

    /**
     * @param string $string
     *
     * @return TestModel
     */
    public function setVorname(string $string): TestModel
    {
        $this->vorname = $string;

        return $this;
    }

    /**
     * @return string
     */
    public function getNachname(): string
    {
        return $this->nachname;
    }

    /**
     * @param string $string
     *
     * @return TestModel
     */
    public function setNachname(string $string): TestModel
    {
        $this->nachname = $string;

        return $this;
    }
    /**
     * @return string
     */
    public function getZuordnung(): string
    {
        return $this->zuordnung;
    }

    /**
     * @param string $string
     *
     * @return TestModel
     */
    public function setZuordnung(String $string): TestModel
    {
        $this->zuordnung = $string;

        return $this;
    }
    /**
     * @return int
     */
    public function getAvatar(): int
    {
        return $this->avatar;
    }

    /**
     * @param int $int
     *
     * @return TestModel
     */
    public function setAvatar(int $int): TestModel
    {
        $this->avatar = $int;

        return $this;
    }
    /**
     * @return string
     */
    public function getHintergrundbild(): string
    {
        return $this->avatar;
    }

    /**
     * @param string $string
     *
     * @return TestModel
     */
    public function setHintergrundbild(int $string): TestModel
    {
        $this->hintergrundbild = $string;

        return $this;
    }

    /**
     * @return string
     */
    public function getTelefonnummer(): string
    {
        return $this->telefonnummer;
    }

    /**
     * @param string $string
     *
     * @return TestModel
     */
    public function setTelefonnummer(int $string): TestModel
    {
        $this->telefonnummer = $string;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $string
     *
     * @return TestModel
     */
    public function setEmail(int $string): TestModel
    {
        $this->email = $string;

        return $this;
    }
}
