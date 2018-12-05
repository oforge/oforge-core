<?php
namespace Oforge\Engine\Modules\Core\Services;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\CouldNotActivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\CouldNotDeactivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\PluginNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Helper;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;

class PluginAccessService
{
    /**
     * @var $em EntityManager
     */
    private $em;
    
    /**
     * @var $repo ObjectRepository|EntityRepository
     */
    private $repo;
    
    public function __construct() {
        $this->em = Oforge()->DB()->getManager();
        $this->repo = $this->em->getRepository(Plugin::class);
    }
    
    /**
     * @return array|object[]
     */
    public function getActive()
    {
        //find all plugins order by "order"
        $plugins = $this->repo->findBy(array("active" => 1), array('order' => 'ASC'));
        //create working bucket with all plugins that should be started
        return $plugins;
    }

}
