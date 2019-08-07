<?php

namespace Oforge\Engine\Modules\Mailer\Models;

use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oforge_mailer_entry")
 * @ORM\Entity
 */
class Mail extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="method", type="string", nullable=false)
     */
    private $method;

    /**
     * @var array
     * @ORM\Column(name="params", type="array", nullable=true)
     */
    private $params;

    /**
     * @var boolean
     * @ORM\Column(name="sent", type="boolean", nullable=false)
     */
    private $sent = false;

    /**
     * @param string $method
     */
    public function setMethod(string $method) {
        $this->method = $method;
    }

    /**
     * @return string $method
     */
    public function getMethod() : string {
        return $this->method;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params) {
        $this->params = $params;
    }

    /**
     * @return array|null $params
     */
    public function getParams() : ?array {
        return $this->params;
    }
    /**
     * @param bool $sent
     */
    public function setSent(bool $sent) {
        $this->sent = $sent;
    }

}
