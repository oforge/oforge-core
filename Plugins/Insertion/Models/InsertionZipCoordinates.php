<?php

namespace Insertion\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion_zip_coordinates")
 * @ORM\Entity
 */
class InsertionZipCoordinates extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="zip", type="string", nullable=false)
     */
    private $zip;

    /**
     * @var string
     * @ORM\Column(name="country", type="string", nullable=false)
     */
    private $country;

    /**
     * @var string
     * @ORM\Column(name="lat", type="string", nullable=false)
     */
    private $lat;

    /**
     * @var string
     * @ORM\Column(name="lng", type="string", nullable=false)
     */
    private $lng;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getZip() : string {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip(string $zip) : void {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getLat() : string {
        return $this->lat;
    }

    /**
     * @param string $lat
     */
    public function setLat(string $lat) : void {
        $this->lat = $lat;
    }

    /**
     * @return string
     */
    public function getLng() : string {
        return $this->lng;
    }

    /**
     * @param string $lng
     */
    public function setLng(string $lng) : void {
        $this->lng = $lng;
    }

    /**
     * @return string
     */
    public function getCountry() : string {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(?string $country) : void {
        $this->country = $country;
    }

}