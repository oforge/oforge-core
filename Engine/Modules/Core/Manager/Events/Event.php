<?php

namespace Oforge\Engine\Modules\Core\Manager\Events;

/**
 * Class Event
 *
 * @package Oforge\Engine\Modules\Core\Manager\Events
 */
class Event {
    /** @var string $eventName */
    private $eventName;
    /** @var array $data */
    private $data = [];
    /** @var mixed|null $returnValue */
    private $returnValue = null;
    /** @var bool $propagationStoppable */
    private $propagationStoppable = false;
    /** @var bool $propagationStopped */
    private $propagationStopped = false;

    /**
     * Event constructor.
     *
     * @param string $eventName
     * @param array $data
     * @param mixed|null $returnValue
     */
    private function __construct(string $eventName, array $data, $returnValue) {
        $this->eventName            = $eventName;
        $this->data                 = $data;
        $this->returnValue          = $returnValue;
        $this->propagationStopped   = false;
        $this->propagationStoppable = false;
    }

    /**
     * @param string $eventName
     * @param array $data
     * @param mixed|null $returnValue
     *
     * @return Event
     */
    public static function create(string $eventName, array $data = [], $returnValue = null) : Event {
        return new Event($eventName, $data, $returnValue);
    }

    /**
     * @return string
     */
    public function getEventName() : string {
        return $this->eventName;
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
     * @return Event
     */
    public function setData(array $data) : Event {
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
     * @return Event
     */
    public function setReturnValue($returnValue) : Event {
        $this->returnValue = $returnValue;

        return $this;
    }

    /**
     * @param bool $propagationStoppable
     *
     * @return Event
     */
    public function enablePropagationStop(bool $propagationStoppable = true) : Event {
        $this->propagationStoppable = $propagationStoppable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPropagationStopped() : bool {
        return $this->propagationStopped;
    }

    /**
     * @param bool $propagationStopped
     *
     * @return Event
     */
    public function stopPropagation(bool $propagationStopped = true) : Event {
        if ($this->propagationStoppable) {
            $this->propagationStopped = $propagationStopped;
        }

        return $this;
    }

}
