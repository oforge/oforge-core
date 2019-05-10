<?php
namespace Oforge\Engine\Modules\Core\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;

class PluginAccessService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => Plugin::class]);
    }

    /**
     * @return array|object[]
     */
    public function getActive()
    {
        //find all plugins order by "order"
        $plugins = $this->repository()->findBy(array("active" => 1), array('order' => 'ASC'));
        //create working bucket with all plugins that should be started
        return $plugins;
    }

}
