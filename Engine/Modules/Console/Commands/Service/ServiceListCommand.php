<?php

namespace Oforge\Engine\Modules\Console\Commands\Service;

use GetOpt\Option;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Console\Services\ConsoleService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\StringHelper;

/**
 * Class ServiceListCommand
 *
 * @package Oforge\Engine\Modules\Console\Commands\Service
 */
class ServiceListCommand extends AbstractCommand {

    /**
     * ServiceListCommand constructor.
     */
    public function __construct() {
        parent::__construct('oforge:service:list', self::TYPE_EXTENDED);
        $this->setDescription('Display service list');
        $this->addOptions([
            Option::create('e', 'extended')#
                  ->setDescription('Include service function names')#
                  ->setDefaultValue(0),#
        ]);
    }

    /**
     * @inheritdoc
     * @throws ServiceNotFoundException
     */
    public function handle(Input $input, Logger $output) : void {
        $isExtended     = (bool) $input->getOption('extended');
        $serviceManager = Oforge()->Services();
        $serviceNames   = $serviceManager->getServiceNames();
        sort($serviceNames);
        $lines       = [];
        $columnWidth = 0;
        foreach ($serviceNames as $serviceName) {
            if ($isExtended) {
                try {
                    $service = $serviceManager->get($serviceName);

                    $classMethods = get_class_methods($service);
                    foreach ($classMethods as $classMethod) {
                        if (StringHelper::startsWith($classMethod, "__")) {
                            continue;
                        }
                        $reflector  = new \ReflectionClass($service);
                        $comment    = explode("\n", $reflector->getMethod($classMethod)->getDocComment());
                        $methodText = "";
                        if (count($comment) > 1) {
                            if (!StringHelper::contains($comment[1], "@param")
                                && !StringHelper::contains($comment[1], "@throws")
                                && !StringHelper::contains($comment[1], "@return")) {
                                $methodText = strtr($comment[1], [
                                    '*'  => '',
                                    "\n" => '',
                                ]);
                            }
                        }
                        $serviceFunctionName = $serviceName . ':' . $classMethod;
                        $columnWidth         = max([$columnWidth, strlen($serviceFunctionName)]);
                        $lines[]             = [$serviceFunctionName, $methodText];
                    }
                } catch (ServiceNotFoundException $e) {
                } catch (\ReflectionException $e) {
                }
            } else {
                $columnWidth = max([$columnWidth, strlen($serviceName)]);
                $lines[]     = [$serviceName, ''];
            }
        }

        /** @var ConsoleService $consoleService */
        $consoleService = Oforge()->Services()->get('console');
        echo $consoleService->getRenderer()->renderColumns($columnWidth, $lines);
    }

}
