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
     * @var string|null $typeString
     * @ORM\Column(name="var_string", type="string", nullable=true)
     */
    private $typeString;
    /**
     * @var string|null $typeText
     * @ORM\Column(name="var_text", type="text", nullable=true)
     */
    private $typeText;
    /**
     * @var string|null $typeHtml
     * @ORM\Column(name="var_html", type="text", nullable=true)
     */
    private $typeHtml;
    /**
     * @var int|null $typeInteger
     * @ORM\Column(name="var_integer", type="integer", nullable=true)
     */
    private $typeInteger;
    /**
     * @var int|null $typeSmallint
     * @ORM\Column(name="var_smallint", type="smallint", nullable=true)
     */
    private $typeSmallint;
    /**
     * @var int|null $typeBigint
     * @ORM\Column(name="var_bigint", type="bigint", nullable=true)
     */
    private $typeBigint;
    /**
     * @var bool $typeBoolean
     * @ORM\Column(name="var_boolean", type="boolean", nullable=true, options={"default":false})
     */
    private $typeBoolean = false;
    /**
     * @var float|null $typeDecimal
     * @ORM\Column(name="var_decimal", type="decimal", nullable=true)
     */
    private $typeDecimal;
    /**
     * @var float|null $typeFloat
     * @ORM\Column(name="var_float", type="float", nullable=true, options={"precision":2})
     */
    private $typeFloat;
    /**
     * @var DateTime|null $typeDate
     * @ORM\Column(name="var_date", type="date", nullable=true)
     */
    private $typeDate;
    /**
     * @var DateTime|null $typeTime
     * @ORM\Column(name="var_time", type="time", nullable=true)
     */
    private $typeTime;
    /**
     * @var DateTime|null $typeDatetime
     * @ORM\Column(name="var_datetime", type="datetime", nullable=true)
     */
    private $typeDatetime;
    /**
     * @var object|null $typeObject
     * @ORM\Column(name="var_object", type="object", nullable=true)
     */
    private $typeObject;
    /**
     * @var array|null $typeArray
     * @ORM\Column(name="var_array", type="array", nullable=true)
     */
    private $typeArray;
    /**
     * @var string[]|null $typeSimpleArray
     * @ORM\Column(name="var_simple_array", type="simple_array", nullable=true)
     */
    private $typeSimpleArray;
    /**
     * @var array|null $typeJsonArray
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
     * @return string|null
     */
    public function getTypeString() : ?string {
        return $this->typeString;
    }

    /**
     * @param string|null $typeString
     *
     * @return CrudTest
     */
    public function setTypeString(?string $typeString) : CrudTest {
        $this->typeString = $typeString;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTypeText() : ?string {
        return $this->typeText;
    }

    /**
     * @param string|null $typeText
     *
     * @return CrudTest
     */
    public function setTypeText(?string $typeText) : CrudTest {
        $this->typeText = $typeText;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTypeHtml() : ?string {
        return $this->typeHtml;
    }

    /**
     * @param string|null $typeHtml
     *
     * @return CrudTest
     */
    public function setTypeHtml(?string $typeHtml) : CrudTest {
        $this->typeHtml = $typeHtml;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTypeInteger() : ?int {
        return $this->typeInteger;
    }

    /**
     * @param int|null $typeInteger
     *
     * @return CrudTest
     */
    public function setTypeInteger(?int $typeInteger) : CrudTest {
        $this->typeInteger = $typeInteger;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTypeSmallint() : ?int {
        return $this->typeSmallint;
    }

    /**
     * @param int|null $typeSmallint
     *
     * @return CrudTest
     */
    public function setTypeSmallint(?int $typeSmallint) : CrudTest {
        $this->typeSmallint = $typeSmallint;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTypeBigint() : ?int {
        return $this->typeBigint;
    }

    /**
     * @param int|null $typeBigint
     *
     * @return CrudTest
     */
    public function setTypeBigint(?int $typeBigint) : CrudTest {
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
    public function setTypeBoolean(bool $typeBoolean) : CrudTest {
        $this->typeBoolean = $typeBoolean;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTypeDecimal() : ?float {
        return $this->typeDecimal;
    }

    /**
     * @param float|null $typeDecimal
     *
     * @return CrudTest
     */
    public function setTypeDecimal(?float $typeDecimal) : CrudTest {
        $this->typeDecimal = $typeDecimal;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTypeFloat() : ?float {
        return $this->typeFloat;
    }

    /**
     * @param float|null $typeFloat
     *
     * @return CrudTest
     */
    public function setTypeFloat(?float $typeFloat) : CrudTest {
        $this->typeFloat = $typeFloat;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTypeDate() : ?DateTime {
        return $this->typeDate;
    }

    /**
     * @param DateTime|null $typeDate
     *
     * @return CrudTest
     */
    public function setTypeDate(?DateTime $typeDate) : CrudTest {
        $this->typeDate = $typeDate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTypeTime() : ?DateTime {
        return $this->typeTime;
    }

    /**
     * @param DateTime|null $typeTime
     *
     * @return CrudTest
     */
    public function setTypeTime(?DateTime $typeTime) : CrudTest {
        $this->typeTime = $typeTime;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTypeDatetime() : ?DateTime {
        return $this->typeDatetime;
    }

    /**
     * @param DateTime|null $typeDatetime
     *
     * @return CrudTest
     */
    public function setTypeDatetime(?DateTime $typeDatetime) : CrudTest {
        $this->typeDatetime = $typeDatetime;

        return $this;
    }

    /**
     * @return object|null
     */
    public function getTypeObject() : ?object {
        return $this->typeObject;
    }

    /**
     * @param object|null $typeObject
     *
     * @return CrudTest
     */
    public function setTypeObject(?object $typeObject) : CrudTest {
        $this->typeObject = $typeObject;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getTypeArray() : ?array {
        return $this->typeArray;
    }

    /**
     * @param array|null $typeArray
     *
     * @return CrudTest
     */
    public function setTypeArray(?array $typeArray) : CrudTest {
        $this->typeArray = $typeArray;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getTypeSimpleArray() : ?array {
        return $this->typeSimpleArray;
    }

    /**
     * @param string[]|null $typeSimpleArray
     *
     * @return CrudTest
     */
    public function setTypeSimpleArray(?array $typeSimpleArray) : CrudTest {
        $this->typeSimpleArray = $typeSimpleArray;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getTypeJsonArray() : ?array {
        return $this->typeJsonArray;
    }

    /**
     * @param array|null $typeJsonArray
     *
     * @return CrudTest
     */
    public function setTypeJsonArray(?array $typeJsonArray) : CrudTest {
        $this->typeJsonArray = $typeJsonArray;

        return $this;
    }

}
