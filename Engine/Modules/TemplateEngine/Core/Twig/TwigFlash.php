<?php

namespace Oforge\Engine\Modules\TemplateEngine\Core\Twig;

use Exception;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Storage of messages and data for next request / redirect.
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Core\Twig
 */
class TwigFlash {
    public const FLASH    = 'flash';
    public const MESSAGES = 'messages';
    public const DATA     = 'data';

    /**
     * Add a message for the next request / redirect.
     *
     * @param string $type
     * @param string $message
     * @param bool $dismissible
     */
    public function addMessage(string $type, string $message, $dismissible = true) {
        $this->addMessageArray(['type' => $type, 'message' => $message, 'dismissible' => $dismissible]);
    }

    /**
     * Add a error message for the next request / redirect.
     *
     * @param string $type
     * @param Exception $exception
     * @param bool $dismissible
     */
    public function addExceptionMessage(string $type, string $message, Exception $exception, $dismissible = true) {
        $exceptionMessage = $exception->getMessage();
        $exceptionTrace   = $exception->getTraceAsString();
        $clickMessage = I18N::translate('flash_exception_message_click_for_details_note', '(Click for Details)');
        $errorMessage     = "<details><summary>$message $clickMessage</summary><div><p>$exceptionMessage</p><pre>$exceptionTrace</pre></div></details>";
        $this->addMessage($type, $errorMessage, $dismissible);
    }

    /**
     * Add a message config array for the next request / redirect.
     *
     * @param array $array
     */
    public function addMessageArray(array $array) {
        if ($this->init()) {
            $_SESSION[self::FLASH][self::MESSAGES][] = $array;
        }
    }

    /**
     * Removes all stored messages.
     */
    public function clearMessages() {
        if ($this->init()) {
            unset($_SESSION[self::FLASH][self::MESSAGES]);
        }
    }

    /**
     * Get stored messages.
     *
     * @return array
     */
    public function getMessages() {
        return isset($_SESSION[self::FLASH][self::MESSAGES]) ? $_SESSION[self::FLASH][self::MESSAGES] : [];
    }

    /**
     * Are messages stored?
     *
     * @return bool
     */
    public function hasMessages() : bool {
        return isset($_SESSION[self::FLASH][self::MESSAGES]) && !empty($_SESSION[self::FLASH][self::MESSAGES]);
    }

    /**
     * Add data for the next request / redirect.
     *
     * @param string $key
     * @param mixed $data
     */
    public function setData(string $key, $data) {
        if ($this->init()) {
            $_SESSION[self::FLASH][self::DATA][$key] = $data;
        }
    }

    /**
     * Retrieve data by key.
     *
     * @param string $key
     *
     * @return mixed|array Returns empty array if not exist.
     */
    public function getData(string $key) {
        if (isset($_SESSION[self::FLASH][self::DATA][$key])) {
            return $_SESSION[self::FLASH][self::DATA][$key];
        }

        return [];
    }

    /**
     * Exist data by key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasData(string $key) : bool {
        return isset($_SESSION[self::FLASH][self::DATA][$key]);
    }

    /**
     * Removes data.
     *
     * @param string $key
     */
    public function clearData(string $key) {
        if (isset($_SESSION[self::FLASH][self::DATA][$key])) {
            unset($_SESSION[self::FLASH][self::DATA][$key]);
        }
    }

    /**
     * Init Session flash storage.
     *
     * @return bool
     */
    protected function init() : bool {
        if (!isset($_SESSION)) {
            return false;
        }
        if (!isset($_SESSION[self::FLASH])) {
            $_SESSION[self::FLASH] = [];
        }
        if (!isset($_SESSION[self::FLASH][self::MESSAGES])) {
            $_SESSION[self::FLASH][self::MESSAGES] = [];
        }
        if (!isset($_SESSION[self::FLASH][self::DATA])) {
            $_SESSION[self::FLASH][self::DATA] = [];
        }

        return true;
    }

}
