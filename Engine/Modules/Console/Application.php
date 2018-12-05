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
use Oforge\Engine\Modules\Core\Helper\StringHelper;
use ReflectionClass;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class Application
{
    public function run(Array $args)
    {
        if (sizeof($args) >= 2) {
            $command = explode(":", $args[1]);
            if (sizeof($command) > 0) {
                switch ($command[0]) {
                    case "oforge":
                        if(sizeof($command) == 1) {
                            $this->runOforgeHelp();
                            break;
                        }
                        
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
            case "list":
                $this->runOforgeList();
                break;
            case "service":
                $service = Oforge()->Services()->get($command[2]);
                print_r(call_user_func_array(array($service, $command[3]), $params));
                break;
    
            case "cronjob":
                //$service = Oforge()->CronJobs()->get($command[2]);
                //print_r(call_user_func_array(array($service, $command[3]), $params));
                // break;
            default:
                throw new CommandNotFoundException(implode(":", $command));
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
    
    private function runOforgeHelp() {
        print_r("
The following commands are available:
    oforge:service:{name}:{function} {parameter}  :  Call a specific service function with an additional parameter
        oforge:service:ping:me  :  Checks, if the Oforge core has been set up and configured correclty.
        oforge:service:plugin.state:activate {pluginName}  :  Activate a Plugin
        ");
    }
    
    private function runOforgeList(){
        print_r("
The following commands are available:
\toforge:service:{name}:{function} {parameter}  :  Call a specific service function with an additional parameter");
        
        $serviceNames = Oforge()->Services()->listNames();
        foreach ($serviceNames as $serviceName) {
            $service = Oforge()->Services()->get($serviceName);
            $classMethods = get_class_methods($service);
            foreach ($classMethods as $classMethod) {
                if(!StringHelper::startsWith($classMethod, "__")) {
    
                    $reflector = new ReflectionClass($service);
                    $comment = explode("\n", $reflector->getMethod($classMethod)->getDocComment());
                    $methodText = "";
                    if(sizeof($comment) > 1) {
                        if(!StringHelper::contains($comment[1], "@param") && !StringHelper::contains($comment[1], "@throws") && !StringHelper::contains($comment[1], "@return")) {
                           $methodText = str_replace("*", "", str_replace ("\n", "", $comment[1]));
                          //  print_r($methodText);
                        }
                    }
                    
                    $name = "oforge:service:" . $serviceName .":" . $classMethod;
                    print_r("\t" . $name . "\t" . (strlen($name) < 33 ? "\t": "" ). (strlen($name) < 40 ? "\t": "" ) .  $methodText ."\n");
                }
            }
        }
    }
}
