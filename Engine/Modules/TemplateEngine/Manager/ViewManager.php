<?php

namespace Oforge\Engine\Modules\TemplateEngine\Manager;

use Oforge\Engine\Modules\Core\Abstracts\AbstractViewManager;

class ViewManager extends  AbstractViewManager {
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
     */
    public function assign($data) {
        $this->viewData = array_merge($this->viewData, $data);
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
     * Get a specific key value from the viewData
     *
     * @param $key
     *
     * @return int
     */
    public function get($key) {
        return key_exists($key, $this->viewData) ? $this->viewData[$key] : null;
    }
}