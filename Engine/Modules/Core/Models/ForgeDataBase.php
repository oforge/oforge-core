<?php

namespace Oforge\Engine\Modules\Core\Models;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use Doctrine\ORM\Tools\Setup;
use Oforge\Engine\Modules\Core\Helper\Statics;

class ForgeDataBase {
    protected static $instance = null;
    /**
     * @var $manager EntityManager
     */
    private $manager = null;
    /**
     * @var $tool SchemaTool
     */
    private $tool = null;
    private $validator = null;
    private $metaDataCollection = [];
    private $loadedSchemata = [];
    private $settings;
    private $config;

    protected function __construct() {
    }

    protected function __clone() {
    }

    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new ForgeDataBase();
        }

        return self::$instance;
    }

    protected function addMetaData(string $text) {
        $metaData = $this->getManager()->getClassMetadata($text);

        array_push($this->metaDataCollection, $metaData);
        $inSync = $this->getValidator()->schemaInSyncWithMetadata();

        if (!$inSync) {
            $this->getSchemaTool()->updateSchema($this->metaDataCollection, true);
        }
    }

    public function initSchema(array $schemata, $forceReinit = false) {
        if (isset($schemata)) {
            if (sizeof($this->loadedSchemata) == 0) {
                $this->loadLoadedSchemata();
            }

            $changed = false;
            foreach ($schemata as $schema) {
                if (!array_key_exists($schema, $this->loadedSchemata) || $forceReinit) {
                    $this->addMetaData($schema);
                    $changed = true;
                }
            }

            if ($changed) {
                $this->saveLoadedSchemata($schemata);
            }
        }
    }

    /**
     * @param array $settings
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function init(Array $settings) {
        $this->settings = $settings;

        $cache = new FilesystemCache($settings['cache_dir']);

        $this->config = Setup::createAnnotationMetadataConfiguration($settings['metadata_dirs'], $settings["dev_mode"], null, $cache);
        $this->config->setMetadataDriverImpl(new AnnotationDriver(new CachedReader(new AnnotationReader(), $cache, $settings["dev_mode"]), $settings['metadata_dirs']));
    }

    /**
     * @return EntityManager
     * @throws \Doctrine\ORM\ORMException
     */
    public function getManager() : EntityManager {
        if (!isset($this->manager)) {
            $this->manager = EntityManager::create($this->settings['connection'], $this->config);

            DiscriminatorEntryListener::register($this->manager);
        }

        return $this->manager;
    }

    public function getValidator() : SchemaValidator {
        if (!isset($this->validator)) {
            $this->validator = new SchemaValidator($this->manager);
        }

        return $this->validator;
    }

    public function getSchemaTool() : SchemaTool {
        if (!isset($this->tool)) {
            $this->tool = new SchemaTool($this->manager);
        }

        return $this->tool;
    }

    private function loadLoadedSchemata() {
        $filePath = ROOT_PATH . Statics::DB_CACHE_FILE;
        if (file_exists($filePath)) {
            $this->loadedSchemata = [];

            if ($file = fopen($filePath, "r")) {
                while (!feof($file)) {
                    $line                        = trim(fgets($file));
                    $this->loadedSchemata[$line] = 1;
                }
                fclose($file);
            }
        }
    }

    private function saveLoadedSchemata($schemata) {
        foreach ($schemata as $schema) {
            $this->loadedSchemata[$schema] = 1;
        }
        $filePath = ROOT_PATH . Statics::DB_CACHE_FILE;

        file_put_contents($filePath, "");
        foreach ($this->loadedSchemata as $key => $value) {
            file_put_contents($filePath, $key . "\n", FILE_APPEND);
        }
    }
}
