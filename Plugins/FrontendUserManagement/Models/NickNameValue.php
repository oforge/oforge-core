<?php

namespace FrontendUserManagement\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="frontend_user_nickname_generator")
 * @ORM\Entity
 */
class NickNameValue extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="value", type="string", nullable=false)
     */
    private $value;

    /**
     * @var int
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $order;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getValue() : string {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return NickNameValue
     */
    public function setValue(string $value) : NickNameValue {
        $this->value = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrder() : int {
        return $this->order;
    }

    /**
     * @param int $order
     *
     * @return NickNameValue
     */
    public function setOrder(int $order) : NickNameValue {
        $this->order = $order;

        return $this;
    }
}