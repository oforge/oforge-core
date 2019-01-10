<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:11
 */

namespace Oforge\Engine\Modules\Import\Services;

use function DI\create;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Symfony\Component\Console\Formatter\OutputFormatter;


/**
 * Class ImportService
 * @package Oforge\Engine\Modules\Import\Services
 */
class ImportService
{

    public function process()
    {
        $metaData = Oforge()->DB()->getManager()->getMetadataFactory()->getAllMetadata();
        $mapping = [];

        foreach ($metaData as $data) {
            $mapping[$data->getTableName()] = $data->getReflectionClass()->getName();
        }

        foreach ($mapping as $name => $model) {
            $fullPath = ROOT_PATH . Statics::IMPORTS_DIR . DIRECTORY_SEPARATOR;
            if (file_exists($fullPath . $name . ".csv")) {
                $this->processFile($fullPath, $name, $model);
            }
        }

        echo "Done.";
    }

    private function processFile($fullPath, $name, $model)
    {
        echo "Found file \"" . $name . ".csv\" for model \"" . $model . "\". \nStart processing. \n";

        $handle = fopen($fullPath . $name . ".csv", "r");
        if ($handle) {
            $first = true;
            $header = [];
            $count = 0;
            while (($line = fgets($handle)) !== false) {
                $split = explode(";", $line);;
                if ($first) {

                    foreach ($split as $item) {
                        array_push($header, trim($item));
                    }

                    $first = false;
                } else {
                    if (sizeof($split) > 0 && sizeof($header) == sizeof($split)) {
                        $data = [];
                        foreach ($split as $index => $value) {
                            $data[$header[$index]] = $value;
                        }

                        $element = $model::create($data);
                        Oforge()->DB()->getManager()->persist($element);
                        $count++;
                    }
                }
            }

            Oforge()->DB()->getManager()->flush();
            echo "Included " . $count . " elements. Finish processing!\n\nTo Renaming file to avoid further imports. New filename . _" . $name . ".csv\n\n";
            fclose($handle);
            rename($fullPath . $name . ".csv", $fullPath . "_" . $name . ".csv");
        }
    }
}
