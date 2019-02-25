<?php

namespace Oforge\Engine\Modules\TemplateEngine\Manager;

use Oforge\Engine\Modules\Core\Abstracts\AbstractViewManager;

class ViewManager extends AbstractViewManager {
    protected static $instance;
    private $viewData = [];

    /**
     * Create a singleton instance of the ViewManager
     *
     * @return ViewManager
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new ViewManager();
        }

        return self::$instance;
    }

    /**
     * Assign Data from a Controller to a Template
     *
     * @param array $data
     *
     * @return ViewManager
     */
    public function assign($data) {
        $this->viewData = $this->array_merge_recursive_ex($this->viewData, $data);
        return $this;
    }

    /**
     * Fetch View Data. This function should be called from the route middleware
     * so that it can transport the data to the TemplateEngine
     *
     * @return array
     */
    public function fetch() {
        return $this->viewData;
    }

    /**
     * @param string $type
     * @param string $message
     *
     * @return mixed|void
     */
    public function addFlashMessage(string $type, string $message) {
        if (isset($_SESSION)) {
            $_SESSION['flashMessage'] = ['type' => $type, 'message' => $message];
        }
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    function array_merge_recursive_ex(array & $array1, array & $array2) {
        $merged = $array1;

        foreach ($array2 as $key => & $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->array_merge_recursive_ex($merged[$key], $value);
            } elseif (is_numeric($key)) {
                if (!in_array($value, $merged)) {
                    $merged[] = $value;
                }
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Get a specific key value from the viewData
     *
     * @param string $key
     *
     * @return int
     */
    public function get(string $key) {
        return key_exists($key, $this->viewData) ? $this->viewData[$key] : null;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key) {
        return isset($this->viewData[$key]) && !empty($this->viewData[$key]);
    }
}