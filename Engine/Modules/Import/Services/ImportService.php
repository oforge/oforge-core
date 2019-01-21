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

    function __construct()
    {
        $this->metaData = Oforge()->DB()->getManager()->getMetadataFactory()->getAllMetadata();
        $this->mapping = [];

        foreach ($this->metaData as $data) {
            $this->mapping[$data->getTableName()] = $data->getReflectionClass()->getName();
        }
    }

    public function process()
    {
        foreach ($this->mapping as $name => $model) {
            $fullPath = ROOT_PATH . Statics::IMPORTS_DIR . DIRECTORY_SEPARATOR;
            if (file_exists($fullPath . $name . ".csv")) {
                $this->processFile($fullPath, $name);
            }
        }

        echo "Done.";
    }


    public function processFile($fullPath, $name, $echo = true)
    {
        if (isset($this->mapping[$name]) && file_exists($fullPath . $name . ".csv")) {
            $model = $this->mapping[$name];

            if ($echo) echo "Found file \"" . $name . ".csv\" for model \"" . $model . "\". \nStart processing. \n";

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
                if ($echo) echo "Included " . $count . " elements. Finish processing!\n\nTo Renaming file to avoid further imports. New filename . _" . $name . ".csv\n\n";
                fclose($handle);
                rename($fullPath . $name . ".csv", $fullPath . "_" . $name . ".csv");
            }
        }
    }
}

