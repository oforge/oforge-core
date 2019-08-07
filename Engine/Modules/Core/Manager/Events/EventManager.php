<?php

namespace Oforge\Engine\Modules\Core\Manager\Events;

use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Models\Event\EventModel;

/**
 * Class EventManager
 *
 * @package Oforge\Engine\Modules\Core\Manager\Events
 */
class EventManager extends AbstractDatabaseAccess {
    /** @var EventManager $instance */
    private static $instance;
    /** @var array $eventMeta */
    private $eventMeta = [];
    /** @var array<string, array> $syncListeners */
    private $listeners = [];
    /** @var array<string, array> $sortedListeners */
    private $sortedListeners = [];

    protected function __construct() {
        parent::__construct(EventModel::class);
    }

    /**
     * @param bool $webStart
     *
     * @return EventManager
     */
    public static function getInstance() : EventManager {
        if (!isset(self::$instance)) {
            self::$instance = new EventManager();
        }

        return self::$instance;
    }

    /**
     * @param string $eventName
     * @param int $sync One of [Event::ASYNC, Event::BOTH, Event::SYNC]
     * @param callable $callable
     * @param int $priority
     */
    public function attach(string $eventName, int $sync, callable $callable, int $priority = Statics::DEFAULT_ORDER) {
        $this->sortedListeners[$eventName] = null;
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }
        $this->listeners[$eventName][] = [
            'callable' => $callable,
            'sync'     => $sync,
            'priority' => $priority,
        ];
    }

    /**
     * Remove single event listener. It can be removed sync, async or both listeners.
     *
     * @param string $eventName
     * @param callable $callable
     * @param int $sync One of [Event::ASYNC, Event::BOTH, Event::SYNC]
     */
    public function detach(string $eventName, callable $callable, int $sync = Event::BOTH) {
        $listeners = &$this->listeners;
        if (isset($listeners[$eventName])) {
            $callables = &$listeners[$eventName];
            $indices   = [];
            foreach ($callables as $index => $data) {
                if ($data['callable'] === $callable) {
                    if ($sync === $data['sync']) {
                        $indices[] = $index;
                    } else {
                        $callables[$index]['sync'] = $data['sync'] - $sync;
                        if ($callables[$index]['sync'] < 0) {
                            $indices[] = $index;
                        }
                    }
                }
            }
            if (!empty($indices)) {
                foreach ($indices as $index) {
                    unset($callables[$index]);
                }
                $this->sortedListeners[$eventName] = null;
            }
        }
    }

    /**
     * Removes all event listeners for event name.
     *
     * @param string $eventName
     */
    public function clearListeners(string $eventName) {
        $this->listeners[$eventName] = [];
    }

    /**
     * @param Event $event
     *
     * @return mixed
     */
    public function trigger(Event $event) {
        $listeners = $this->getListeners($event, Event::ASYNC);
        if (!empty($listeners)) {
            $eventModel = $event->toEventModel();
            try {
                $this->entityManager()->create($eventModel);
            } catch (ORMException $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }
        $listeners = $this->getListeners($event, Event::SYNC);

        return $this->processEvent($event, $listeners);
    }

    public function processAsyncEvents() {
        /** @var EventModel[] $eventModels */
        try {
            $eventModels = $repository = $this->repository()->findBy(['processed' => false], ['id' => 'ASC']);
        } catch (ORMException $exception) {
            Oforge()->Logger()->logException($exception);

            return;
        }
        foreach ($eventModels as $eventModel) {
            $eventModel->setProcessed();
            try {
                $this->entityManager()->update($eventModel);
            } catch (ORMException $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }
        foreach ($eventModels as $eventModel) {
            $event     = Event::createFromEventModel($eventModel);
            $listeners = $this->getListeners($event, Event::ASYNC);
            $success   = true;
            try {
                $this->processEvent($event, $listeners);
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
                $success = false;
                $eventModel->setProcessed(false);
                try {
                    $this->entityManager()->update($eventModel);
                } catch (ORMException $exception) {
                    Oforge()->Logger()->logException($exception);
                }
            }
            if ($success) {
                try {
                    $this->entityManager()->remove($eventModel);
                } catch (Exception $exception) {
                    Oforge()->Logger()->logException($exception);
                }
            }
        }
    }

    /**
     * Registration of Event meta information (for developer preview in backend).
     * Array keys:<br>
     * <ul>
     *   <li><strong>name</strong>(required): Name of Event</li>
     *   <li><strong>stoppable</strong>(required): Is event stoppable</li>
     *   <li><strong>returnType</strong>(recommended): Type of return value</li>
     *   <li><strong>data</strong>(recommended): Array of<ul>
     *      <li>key: Data key name</li>
     *      <li>value: Description of data value. Should contain the data type of value. I18n label key</li>
     *     </ul>
     *   </li>
     * </ul>
     *
     * @param array $meta
     */
    public function registerEventMeta(array $meta) {
        foreach (['name', 'stoppable'] as $key) {
            if (!isset($meta[$key])) {
                throw new InvalidArgumentException("Missing key '$key' in event meta.");
            }
        }
        $this->eventMeta[$meta['name']] = $meta;
    }

    /** @return array */
    public function getEventMeta() : array {
        $eventMeta = $this->eventMeta;
        foreach ($this->listeners as $eventName => $callables) {
            if (!isset($eventMeta[$eventName])) {
                $eventMeta[$eventName] = [
                    'name'       => $eventName,
                    'stoppable'  => null,
                    'returnType' => null,
                    'data'       => null,
                ];
            }
            $eventMeta[$eventName]['callables'] = count($callables);
        }
        ksort($eventMeta);

        return $eventMeta;
    }

    /**
     * @param Event $event
     * @param array $listeners
     *
     * @return mixed|null
     */
    protected function processEvent(Event $event, array $listeners) {
        foreach ($listeners as $listener) {
            if (!$event->isPropagationStopped()) {
                $listener['callable']($event);
            }
        }

        return $event->getReturnValue();
    }

    /**
     * @param Event $event
     * @param int $sync
     *
     * @return array
     */
    protected function getListeners(Event $event, int $sync = Event::SYNC) : array {
        $eventName = $event->getEventName();
        $list      = [];
        if (isset($this->listeners[$eventName])) {
            if (isset($this->sortedListeners[$eventName])) {
                $callables = $this->sortedListeners[$eventName];
            } else {
                $callables = $this->listeners[$eventName];
                usort($callables, function ($a, $b) {
                    return $a['priority'] - $b['priority'];
                });
                $this->sortedListeners[$eventName] = $callables;
            }
            foreach ($callables as $index => $data) {
                if ($sync & $data['sync']) {
                    $list[] = $data;
                }
            }
        }

        return $list;
    }

}
