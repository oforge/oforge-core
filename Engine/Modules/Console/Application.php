<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 19.11.2018
 * Time: 21:15
 */

namespace Oforge\Engine\Modules\Console;

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

class Application
{
    public function run(Array $args)
    {
        if (sizeof($args) >= 2) {
            $command = explode(":", $args[1]);
            if (sizeof($command) > 0) {
                switch ($command[0]) {
                    case "oforge":
                        $this->runOforgeCommand($command, array_slice($args, 2));
                        break;
                    default:
                        $this->runDoctrine($args);
                        break;
                }
            }
        }
    }

    private function runOforgeCommand($command, $params)
    {
        switch ($command[1]) {
            case "service":
                $service = Oforge()->Services()->get($command[2]);
                // print_r(call_user_func_array(array($service, $command[3]), $params));
                break;
        }
    }

    private function runDoctrine($args)
    {
        // Setup doctrine commands
        $em = Oforge()->DB()->getManager();
        $helperSet = ConsoleRunner::createHelperSet($em);
        $helperSet->set(new EntityManagerHelper($em), 'em');
        $helperSet->set(new ConnectionHelper($em->getConnection()), 'db');

      //  $_SERVER['argv'] = array_slice($args, 1);
        ConsoleRunner::run($helperSet, []);
    }
}
