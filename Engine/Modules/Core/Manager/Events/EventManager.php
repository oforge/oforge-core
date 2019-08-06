<?php

namespace Oforge\Engine\Modules\Core\Manager\Events;

use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;

/**
 * Class EventManager
 *
 * @package Oforge\Engine\Modules\Core\Manager\Events
 */
class EventManager {
    /** @var EventManager $instance */
    private static $instance;
    /** @var array $eventMeta */
    private $eventMeta = [];
    /** @var array<string, array> $syncListeners */
    private $listeners = [];
    /** @var array<string, int> $asynclistenerCounter */
    private $asynclistenerCounter = [];

    protected function __construct() {
        $this->attach('test1', true, 'trim');
        $this->attach('test3', false, 'trim', 3);
        $this->attach('test4', false, 'rtrim', 1);
        echo "<pre>";
        print_r($this->listeners);
        print_r($this->asynclistenerCounter);
        $this->detach('test3', 'trim');
        print_r($this->asynclistenerCounter);

        print_r($this->getListeners('test3', false));
        die();
    }

    /**
     * @return EventManager
     */
    public static function getInstance() : EventManager {
        if (!isset(self::$instance)) {
            self::$instance = new EventManager();
        }

        return self::$instance;
    }

    public function registerEvent(array $eventMeta) {
    }

    public function getEvents() : array {
        //TODO
        return [];
    }

    public function attach(string $eventName, bool $sync, callable $callable, int $priority = Statics::DEFAULT_ORDER) {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }
        $this->listeners[$eventName][]          = [
            'callable' => $callable,
            'sync'     => $sync,
            'priority' => $priority,
        ];
        $this->asynclistenerCounter[$eventName] = ArrayHelper::get($this->asynclistenerCounter, $eventName, 0) + ($sync ? 0 : 1);
    }

    public function detach(string $eventName, callable $callable, ?bool $sync = null) {
        $listeners = &$this->listeners;
        if (isset($listeners[$eventName])) {
            $callables = &$listeners[$eventName];
            $indices   = [];
            foreach ($callables as $index => $data) {
                if ($data['callable'] === $callable && ($sync === null || $sync === $data['sync'])) {
                    $indices[] = $index;
                    if (!$data['sync']) {
                        $this->asynclistenerCounter[$eventName]--;
                    }
                }
            }
            foreach ($indices as $index) {
                unset($callables[$index]);
            }
        }
    }

    public function getListeners(string $eventName, ?bool $sync = null) : array {
        $list = [];
        if (isset($this->listeners[$eventName])) {
            $callables = $this->listeners[$eventName];
            foreach ($callables as $index => $data) {
                if ($sync === null || $sync === $data['sync']) {
                    $list[] = $data;
                }
            }
        }
        usort($list, function ($a, $b) {
            return $a['priority'] - $b['priority'];
        });

        return $list;
    }

    public function clearListeners(string $eventName) {
        if (isset($this->listeners[$eventName])) {
            unset($this->listeners[$eventName]);
        }
    }

    public function trigger(string $eventName, array $data = []) {
        $this->triggerEvent(Event::create($eventName, $data));
    }

    public function triggerEvent(Event $event) {
        //TODO
    }

}
