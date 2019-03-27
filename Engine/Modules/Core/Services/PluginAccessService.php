<?php
namespace Oforge\Engine\Modules\Core\Services;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\CouldNotActivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\CouldNotDeactivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\PluginNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Helper;
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
