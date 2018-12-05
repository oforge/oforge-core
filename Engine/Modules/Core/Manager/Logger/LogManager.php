<?php
namespace Oforge\Engine\Modules\Core\Manager\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LogManager {
    private $logger = [];
    private $firstLogger = null;

    public function __construct(Array $settings) {
        foreach($settings as $setting) {
            if(array_key_exists("name", $setting) && array_key_exists("path", $setting) && array_key_exists("level", $setting)) {
                // Create the logger
                $logger = new Logger($setting["name"]);
                // Add file handler
                $logger->pushHandler(new StreamHandler($setting["path"], $setting["level"]));
                if(!isset($this->firstLogger)) $this->firstLogger = $setting["name"];
                $this->logger[$setting["name"]] = $logger;
            }
        }
    }

    public function get(string $name = null) : Logger {
        if(isset($name) && array_key_exists($name, $this->logger)) return $this->logger[$name];
        return $this->logger[$this->firstLogger];
    }
}
