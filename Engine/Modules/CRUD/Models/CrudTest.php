<?php

namespace Oforge\Engine\Modules\CRUD\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * Class CrudTest
 *
 * @ORM\Entity
 * @ORM\Table(name="oforge_crud_test")
 * @package Oforge\Engine\Modules\CRUD\Models
 */
class CrudTest extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var ?string $typeString
     * @ORM\Column(name="var_string", type="string", nullable=true)
     */
    private $typeString;
    /**
     * @var ?string $typeText
     * @ORM\Column(name="var_text", type="text", nullable=true)
     */
    private $typeText;
    /**
     * @var ?int $typeInteger
     * @ORM\Column(name="var_integer", type="integer", nullable=true)
     */
    private $typeInteger;
    /**
     * @var ?int $typeSmallint
     * @ORM\Column(name="var_smallint", type="smallint", nullable=true)
     */
    private $typeSmallint;
    /**
     * @var ?int $typeBigint
     * @ORM\Column(name="var_bigint", type="bigint", nullable=true)
     */
    private $typeBigint;
    /**
     * @var bool $typeBoolean
     * @ORM\Column(name="var_boolean", type="boolean", nullable=true, options={"default":false})
     */
    private $typeBoolean = false;
    /**
     * @var ?float $typeDecimal
     * @ORM\Column(name="var_decimal", type="decimal", nullable=true)
     */
    private $typeDecimal;
    /**
     * @var ?float $typeFloat
     * @ORM\Column(name="var_float", type="float", nullable=true, options={"precision":2})
     */
    private $typeFloat;
    /**
     * @var ?\DateTime $typeDate
     * @ORM\Column(name="var_date", type="date", nullable=true)
     */
    private $typeDate;
    /**
     * @var ?\DateTime $typeTime
     * @ORM\Column(name="var_time", type="time", nullable=true)
     */
    private $typeTime;
    /**
     * @var ?\DateTime $typeDatetime
     * @ORM\Column(name="var_datetime", type="datetime", nullable=true)
     */
    private $typeDatetime;
    /**
     * @var ?object $typeObject
     * @ORM\Column(name="var_object", type="object", nullable=true)
     */
    private $typeObject;
    /**
     * @var ?array $typeArray
     * @ORM\Column(name="var_array", type="array", nullable=true)
     */
    private $typeArray;
    /**
     * @var ?string[] $typeSimpleArray
     * @ORM\Column(name="var_simple_array", type="simple_array", nullable=true)
     */
    private $typeSimpleArray;
    /**
     * @var ?array $typeJsonArray
     * @ORM\Column(name="var_json_array", type="json_array", nullable=true)
     */
    private $typeJsonArray;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTypeString() {
        return $this->typeString;
    }

    /**
     * @param mixed $typeString
     *
     * @return CrudTest
     */
    protected function setTypeString($typeString) {
        $this->typeString = $typeString;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeText() {
        return $this->typeText;
    }

    /**
     * @param mixed $typeText
     *
     * @return CrudTest
     */
    protected function setTypeText($typeText) {
        $this->typeText = $typeText;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeInteger() {
        return $this->typeInteger;
    }

    /**
     * @param mixed $typeInteger
     *
     * @return CrudTest
     */
    protected function setTypeInteger($typeInteger) {
        $this->typeInteger = $typeInteger;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeSmallint() {
        return $this->typeSmallint;
    }

    /**
     * @param mixed $typeSmallint
     *
     * @return CrudTest
     */
    protected function setTypeSmallint($typeSmallint) {
        $this->typeSmallint = $typeSmallint;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeBigint() {
        return $this->typeBigint;
    }

    /**
     * @param mixed $typeBigint
     *
     * @return CrudTest
     */
    protected function setTypeBigint($typeBigint) {
        $this->typeBigint = $typeBigint;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTypeBoolean() : bool {
        return $this->typeBoolean;
    }

    /**
     * @param bool $typeBoolean
     *
     * @return CrudTest
     */
    protected function setTypeBoolean(bool $typeBoolean) : CrudTest {
        $this->typeBoolean = $typeBoolean;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeDecimal() {
        return $this->typeDecimal;
    }

    /**
     * @param mixed $typeDecimal
     *
     * @return CrudTest
     */
    protected function setTypeDecimal($typeDecimal) {
        $this->typeDecimal = $typeDecimal;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeFloat() {
        return $this->typeFloat;
    }

    /**
     * @param mixed $typeFloat
     *
     * @return CrudTest
     */
    protected function setTypeFloat($typeFloat) {
        $this->typeFloat = $typeFloat;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeDate() {
        return $this->typeDate;
    }

    /**
     * @param mixed $typeDate
     *
     * @return CrudTest
     */
    protected function setTypeDate($typeDate) {
        $this->typeDate = $typeDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeTime() {
        return $this->typeTime;
    }

    /**
     * @param mixed $typeTime
     *
     * @return CrudTest
     */
    protected function setTypeTime($typeTime) {
        $this->typeTime = $typeTime;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTypeDatetime() : ?DateTime {
        return $this->typeDatetime;
    }

    /**
     * @param DateTime $typeDatetime
     *
     * @return CrudTest
     */
    protected function setTypeDatetime(?DateTime $typeDatetime) : CrudTest {
        $this->typeDatetime = $typeDatetime;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeObject() {
        return $this->typeObject;
    }

    /**
     * @param mixed $typeObject
     *
     * @return CrudTest
     */
    protected function setTypeObject($typeObject) {
        $this->typeObject = $typeObject;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeArray() {
        return $this->typeArray;
    }

    /**
     * @param mixed $typeArray
     *
     * @return CrudTest
     */
    protected function setTypeArray($typeArray) {
        $this->typeArray = $typeArray;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeSimpleArray() {
        return $this->typeSimpleArray;
    }

    /**
     * @param mixed $typeSimpleArray
     *
     * @return CrudTest
     */
    protected function setTypeSimpleArray($typeSimpleArray) {
        $this->typeSimpleArray = $typeSimpleArray;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTypeJsonArray() {
        return $this->typeJsonArray;
    }

    /**
     * @param mixed $typeJsonArray
     *
     * @return CrudTest
     */
    protected function setTypeJsonArray($typeJsonArray) {
        $this->typeJsonArray = $typeJsonArray;

        return $this;
    }

}
