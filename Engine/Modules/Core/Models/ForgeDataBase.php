<?php
namespace Oforge\Engine\Modules\Core\Models;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use Doctrine\ORM\Tools\Setup;

class ForgeDataBase {
    protected static $instance = null;
    /**
     * @var $manager EntityManager
     */
    private $manager = null;
    private $tool = null;
    private $validator = null;
    private $metaDataCollection = [];

    protected function __construct() {}
    protected function __clone() {}

    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new ForgeDataBase();
        }
        return self::$instance;
    }

    protected function addMetaData(string $text) {
        $metaData = $this->manager->getClassMetadata($text);
        //TODO check if 
        array_push($this->metaDataCollection, $metaData);  
        $inSync = true;

        foreach($this->metaDataCollection as $meta) {
            if(!$this->validator->schemaInSyncWithMetadata($meta)) {
                $inSync = false;
                break;
            }
        }         

        if(!$inSync) {
            $this->tool->updateSchema($this->metaDataCollection, true);
        }
    }

    public function initSchema(array $schemata) {
        if(isset($schemata)) {
            foreach($schemata as $schema) {
                $this->addMetaData($schema);
            }
        }
    }
    
    /**
     * @param array $settings
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function init(Array $settings){
        $config = Setup::createAnnotationMetadataConfiguration(
            $settings['metadata_dirs'],
            $settings['dev_mode']
        );
    
        $config->setMetadataDriverImpl(
            new AnnotationDriver(new AnnotationReader, $settings['metadata_dirs'])
        );
    
        $config->setMetadataCacheImpl(
            new FilesystemCache($settings['cache_dir'])
        );
    
        $this->manager = EntityManager::create(
            $settings['connection'],
            $config
        );
        
        $this->validator = new SchemaValidator($this->manager);
        $this->tool = new SchemaTool($this->manager);
    }

    public function getManager() : EntityManager {
        return $this->manager;
    }

    public function getValidator(): SchemaValidator {
        return $this->validator;
    }

    public function getSchemaTool(): SchemaTool {
        return $this->tool;
    }
}
