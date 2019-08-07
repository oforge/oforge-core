<?php

namespace Oforge\Engine\Modules\Core\Models\Event;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * Class EventModel
 *
 * @package Oforge\Engine\Modules\Core\Models\Event
 * @ORM\Entity
 * @ORM\Table(name="oforge_core_events")
 */
class EventModel extends AbstractModel {
    /**
     * @var string $id
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Oforge\Engine\Modules\Core\Models\Event\EventIdGenerator")
     */
    private $id;
    /**
     * @var bool $processed
     * @ORM\Column(name="processed", type="boolean", nullable=false, options={"default":false})
     */
    private $processed = false;
    /**
     * @var string $eventName
     * @ORM\Column(name="event_name", type="string", nullable=false)
     */
    private $eventName;
    /**
     * @var array $data
     * @ORM\Column(name="data", type="object", nullable=false)
     */
    private $data = [];
    /**
     * @var mixed|null $returnValue
     * @ORM\Column(name="return_value", type="object", nullable=false, options={"default":null})
     */
    private $returnValue = null;
    /**
     * @var bool $stoppable
     * @ORM\Column(name="stoppable", type="boolean", nullable=false, options={"default":false})
     */
    private $stoppable = false;

    /**
     * @return string
     */
    public function getId() : string {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isProcessed() : bool {
        return $this->processed;
    }

    /**
     * @param bool $processed
     *
     * @return EventModel
     */
    public function setProcessed(bool $processed = true) : EventModel {
        $this->processed = $processed;

        return $this;
    }

    /**
     * @return string
     */
    public function getEventName() : string {
        return $this->eventName;
    }

    /**
     * @param string $eventName
     *
     * @return EventModel
     */
    protected function setEventName(string $eventName) : EventModel {
        $this->eventName = $eventName;

        return $this;
    }

    /**
     * @return array
     */
    public function getData() : array {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return EventModel
     */
    protected function setData(array $data) : EventModel {
        $this->data = $data;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getReturnValue() {
        return $this->returnValue;
    }

    /**
     * @param mixed|null $returnValue
     *
     * @return EventModel
     */
    protected function setReturnValue($returnValue) : EventModel {
        $this->returnValue = $returnValue;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStoppable() : bool {
        return $this->stoppable;
    }

    /**
     * @param bool $stoppable
     *
     * @return EventModel
     */
    protected function setStoppable(bool $stoppable) : EventModel {
        $this->stoppable = $stoppable;

        return $this;
    }

}
