<?php

namespace Oforge\Engine\Modules\Core\Forge;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use Doctrine\ORM\Tools\Setup;
use Oforge\Engine\Modules\Core\Annotation\ORM\Discriminator\DiscriminatorEntryListener;
use Oforge\Engine\Modules\Core\Helper\Statics;

/**
 * Class ForgeDataBase
 *
 * @package Oforge\Engine\Modules\Core\Models
 */
class ForgeDatabase {
    private const PATH_CACHE_FILE = ROOT_PATH . Statics::DB_CACHE_FILE;
    /** @var ForgeDatabase $instance */
    protected static $instance = null;
    /** @var EntityManager $entityManager */
    private $entityManager = null;
    /** @var SchemaTool $schemaTool */
    private $schemaTool = null;
    /** @var SchemaValidator $schemaValidator */
    private $schemaValidator = null;
    /** @var ClassMetadata[] $metaDataCollection */
    private $metaDataCollection = [];
    /** @var array $loadedSchemata */
    private $loadedSchemata = [];
    /** @var array $settings */
    private $settings;
    /** @var Configuration $configuration */
    private $configuration;

    protected function __construct() {
    }

    /**
     * @return ForgeDatabase
     */
    public static function getInstance() : ForgeDatabase {
        if (!isset(self::$instance)) {
            self::$instance = new ForgeDatabase();
        }

        return self::$instance;
    }

    /** Init model schemata.
     *
     * @param string[] $schemata
     * @param bool $forceInit
     */
    public function initModelSchema(array $schemata, $forceInit = false) {
        if (empty($schemata)) {
            return;
        }
        if (empty($this->schemata)) {
            $this->loadLoadedModelSchemata();
        }
        $addedSchemata = [];
        foreach ($schemata as $schema) {
            if (!array_key_exists($schema, $this->loadedSchemata) || $forceInit) {
                $this->addMetaData($schema);
                $this->loadedSchemata[$schema] = 1;

                $addedSchemata[] = $schema;
            }
        }
        if (!empty($addedSchemata)) {
            $this->saveAddedModelSchemata($addedSchemata);
        }
    }

    /**
     * @param array $settings
     *
     * @throws AnnotationException
     */
    public function init(array $settings) {
        $this->settings = $settings;
        $isDevMode      = $settings['dev_mode'];
        $metadataDirs   = $settings['metadata_dirs'];
        $cacheDir       = $settings['cache_dir'];

        $filesystemCache  = new FilesystemCache($cacheDir);
        $annotationReader = new AnnotationReader();
        $cachedReader     = new CachedReader($annotationReader, $filesystemCache, $isDevMode);
        $annotationDriver = new AnnotationDriver($cachedReader, $metadataDirs);

        $this->configuration = Setup::createAnnotationMetadataConfiguration($metadataDirs, $isDevMode, null, $filesystemCache);
        $this->configuration->setMetadataDriverImpl($annotationDriver);
    }

    /**
     * @return EntityManager
     */
    public function getEnityManager() : EntityManager {
        if (!isset($this->entityManager)) {
            $this->entityManager = EntityManager::create($this->settings['connection'], $this->configuration);

            DiscriminatorEntryListener::register($this->entityManager);
        }

        return $this->entityManager;
    }

    /**
     * @return SchemaValidator
     */
    public function getSchemaValidator() : SchemaValidator {
        if (!isset($this->schemaValidator)) {
            $this->schemaValidator = new SchemaValidator($this->getEnityManager());
        }

        return $this->schemaValidator;
    }

    /**
     * @return SchemaTool
     */
    public function getSchemaTool() : SchemaTool {
        if (!isset($this->schemaTool)) {
            $this->schemaTool = new SchemaTool($this->getEnityManager());
        }

        return $this->schemaTool;
    }

    /**
     * Prevend cloning.
     */
    protected function __clone() {
    }

    /**
     * Add new schema or update existing.
     *
     * @param string $schema
     */
    protected function addMetaData(string $schema) {
        $metaData                   = $this->getEnityManager()->getClassMetadata($schema);
        $this->metaDataCollection[] = $metaData;

        $inSync = $this->getSchemaValidator()->schemaInSyncWithMetadata();
        if (!$inSync) {
            $this->getSchemaTool()->updateSchema($this->metaDataCollection, true);
        }
    }

    /**
     * Load model schema from chache file.
     */
    private function loadLoadedModelSchemata() {
        if (file_exists(self::PATH_CACHE_FILE)) {
            $this->loadedSchemata = [];
            if ($file = fopen(self::PATH_CACHE_FILE, "r")) {
                while (!feof($file)) {
                    $line = trim(fgets($file));
                    if (!empty($line)) {
                        $this->loadedSchemata[$line] = 1;
                    }
                }
                fclose($file);
            }
        }
    }

    /**
     * Save model schema to chache file.
     *
     * @param string[] $schema
     */
    private function saveAddedModelSchemata($schema) {
        $content = implode("\n", $schema);
        file_put_contents(self::PATH_CACHE_FILE, $content . "\n", FILE_APPEND);
    }

}
