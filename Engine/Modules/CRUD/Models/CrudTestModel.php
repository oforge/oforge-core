<?php

namespace Oforge\Engine\Modules\CRUD\Models;

use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * Class CrudTestModel
 *
 * @package Oforge\Engine\Modules\CRUD\Models
 */
class CrudTestModel extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string $typeString
     * @ORM\Column(name="var_string", type="string", nullable=false)
     */
    private $typeString;
    /**
     * @var string $typeText
     * @ORM\Column(name="var_text", type="text", nullable=false)
     */
    private $typeText;
    /**
     * @var string $typeInteger
     * @ORM\Column(name="var_text", type="integer", nullable=false)
     */
    private $typeInteger;
    /**
     * @var string $typeSmallint
     * @ORM\Column(name="var_smallint", type="smallint", nullable=false)
     */
    private $typeSmallint;
    /**
     * @var string $typeBigint
     * @ORM\Column(name="var_bigint", type="bigint", nullable=false)
     */
    private $typeBigint;
    /**
     * @var string $typeBoolean
     * @ORM\Column(name="var_boolean", type="boolean", nullable=false)
     */
    private $typeBoolean;
    /**
     * @var string $typeDecimal
     * @ORM\Column(name="var_decimal", type="decimal", nullable=false)
     */
    private $typeDecimal;
    /**
     * @var string $typeFloat
     * @ORM\Column(name="var_float", type="float", nullable=false)
     */
    private $typeFloat;
    /**
     * @var string $typeDate
     * @ORM\Column(name="var_date", type="date", nullable=false)
     */
    private $typeDate;
    /**
     * @var string $typeTime
     * @ORM\Column(name="var_time", type="time", nullable=false)
     */
    private $typeTime;
    /**
     * @var string $typeDatetime
     * @ORM\Column(name="var_datetime", type="datetime", nullable=false)
     */
    private $typeDatetime;
    /**
     * @var string $typeObject
     * @ORM\Column(name="var_object", type="object", nullable=false)
     */
    private $typeObject;
    /**
     * @var string $typeArray
     * @ORM\Column(name="var_array", type="array", nullable=false)
     */
    private $typeArray;
    /**
     * @var string $typeSimpleArray
     * @ORM\Column(name="var_simple_array", type="simple_array", nullable=false)
     */
    private $typeSimpleArray;
    /**
     * @var string $typeJsonArray
     * @ORM\Column(name="var_json_array", type="json_array", nullable=false)
     */
    private $typeJsonArray;

}
