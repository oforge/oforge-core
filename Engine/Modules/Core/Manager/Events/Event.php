<?php

namespace Oforge\Engine\Modules\Core\Manager\Events;

use Oforge\Engine\Modules\Core\Models\Event\EventModel;

/**
 * Class Event
 *
 * @package Oforge\Engine\Modules\Core\Manager\Events
 */
class Event {
    public const SYNC  = 0b0001;
    public const ASYNC = 0b0010;
    public const BOTH  = self::SYNC | self::ASYNC;
    /** @var string $eventName */
    private $eventName;
    /** @var array $data */
    private $data = [];
    /** @var mixed|null $returnValue */
    private $returnValue = null;
    /** @var bool $stoppable */
    private $stoppable = false;
    /** @var bool $stopped */
    private $stopped = false;

    /**
     * Event constructor.
     *
     * @param string $eventName
     * @param array $data
     * @param mixed|null $returnValue
     * @param bool $stoppable
     */
    private function __construct(string $eventName, array $data, $returnValue, bool $stoppable) {
        $this->eventName   = $eventName;
        $this->data        = $data;
        $this->returnValue = $returnValue;
        $this->stopped     = false;
        $this->stoppable   = $stoppable;
    }

    /**
     * @param string $eventName
     * @param array $data
     * @param mixed|null $returnValue
     * @param bool $stoppable
     *
     * @return Event
     */
    public static function create(string $eventName, array $data = [], $returnValue = null, bool $stoppable = false) : Event {
        return new Event($eventName, $data, $returnValue, $stoppable);
    }

    /**
     * @param EventModel $eventModel
     *
     * @return Event
     */
    public static function createFromEventModel(EventModel $eventModel) : Event {
        return self::create($eventModel->getEventName(), $eventModel->getData(), $eventModel->getReturnValue(), $eventModel->isStoppable());
    }

    /**
     * @return EventModel
     */
    public function toEventModel() : EventModel {
        return EventModel::create([
            'eventName'   => $this->eventName,
            'data'        => $this->data,
            'returnValue' => $this->returnValue,
            'stoppable'   => $this->stoppable,
        ]);
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
     * @return bool
     */
    public function isStoppable() : bool {
        return $this->stoppable;
    }

    /**
     * @return bool
     */
    public function isPropagationStopped() : bool {
        return $this->stopped;
    }

    /**
     * @param bool $propagationStopped
     *
     * @return Event
     */
    public function stopPropagation(bool $propagationStopped = true) : Event {
        if ($this->stoppable) {
            $this->stopped = $propagationStopped;
        }

        return $this;
    }

}
