<?php

namespace Oforge\Engine\Modules\Import\Services;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Forge\ForgeEntityManager;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Helper\StringHelper;

/**
 * Class ImportService
 *
 * @package Oforge\Engine\Modules\Import\Services
 */
class ImportDatabaseCsvService {
    /** @var array<string, string>|null $mapClassDatabaseTable */
    private $mapClassDatabaseTable = null;
    /** @var ForgeEntityManager $forgeEntityManager */
    private $forgeEntityManager;

    function __construct() {
        $this->forgeEntityManager = Oforge()->DB()->getForgeEntityManager();
    }

    /**
     * Process all import files.
     *
     * @param bool $echo
     */
    public function process($echo = false) {
        $this->initialize();
        foreach ($this->mapClassDatabaseTable as $dbTableName => $modelClass) {
            $fullPath = ROOT_PATH . Statics::IMPORTS_DIR . DIRECTORY_SEPARATOR;
            if (is_readable($fullPath . $dbTableName . '.csv')) {
                $this->processFile($fullPath, $dbTableName, $echo);
            }
        }
        if ($echo) {
            echo 'Finished';
        }
    }

    /**
     * @param $folderPath
     * @param $dbTableName
     * @param bool $echo
     */
    public function processFile(string $folderPath, $dbTableName, $echo = false) {
        $this->initialize();
        $folderPath = StringHelper::rightTrim($folderPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $filePath   = $folderPath . $dbTableName . '.csv';

        if (isset($this->mapClassDatabaseTable[$dbTableName]) && is_readable($filePath)) {
            $modelClass = $this->mapClassDatabaseTable[$dbTableName];

            if ($echo) {
                echo "Found file '$dbTableName.csv' for model '$modelClass'.\n\tStart processing.\n\t";
            }

            $handle = fopen($folderPath . $dbTableName . '.csv', 'r');
            if ($handle) {
                $first      = true;
                $header     = [];
                $headerSize = 0;
                $added      = 0;
                $failed     = 0;

                while (($line = fgets($handle)) !== false) {
                    $parts = explode(';', $line);
                    if ($first) {
                        foreach ($parts as $item) {
                            $header[] = trim($item);
                        }
                        $headerSize = count($header);
                        $first      = false;
                    } else {
                        if (!empty($parts) && $headerSize === count($parts)) {
                            $data = [];
                            foreach ($parts as $index => $value) {
                                $data[$header[$index]] = $value;
                            }
                            $element = $modelClass::create($data);

                            try {
                                $this->forgeEntityManager->create($element, false);
                                $added++;
                            } catch (ORMException $exception) {
                                $failed++;
                            }
                        }
                    }
                }
                $this->forgeEntityManager->flush();
                fclose($handle);
                rename($filePath, $folderPath . '_' . $dbTableName . '.csv');
                if ($echo) {
                    echo "Finish file processing. Added $added elements, $failed failed.\n\tFile renamed to avoid further imports. New filename . _$dbTableName.csv\n";
                }
            }
        }
    }

    private function initialize() {
        if ($this->mapClassDatabaseTable === null) {
            /** @var ClassMetadata[] $allMetadata */
            $allMetadata = $this->forgeEntityManager->getEntityManager()->getMetadataFactory()->getAllMetadata();

            $this->mapClassDatabaseTable = [];
            foreach ($allMetadata as $metaData) {
                $this->mapClassDatabaseTable[$metaData->getTableName()] = $metaData->getReflectionClass()->getName();
            }
        }
    }

}

