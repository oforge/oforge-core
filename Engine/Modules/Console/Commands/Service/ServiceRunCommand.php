<?php

namespace Oforge\Engine\Modules\Console\Commands\Service;

use GetOpt\Operand;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * Class ServiceRunCommand
 *
 * @package Oforge\Engine\Modules\Console\Commands\Service
 */
class ServiceRunCommand extends AbstractCommand {
    /**
     * Array with validation messages (by key).
     *
     * @var array $validationMessage
     */
    private $validationMessage = [];

    /**
     * ServiceRunCommand constructor.
     */
    public function __construct() {
        parent::__construct('oforge:service:run', self::TYPE_EXTENDED);
        $this->setDescription('Call a specific service function with optional parameters');
        $this->addOperands([
            Operand::create('ServiceNameFunction', Operand::REQUIRED)#
                   ->setDescription('Name of service & function name (Format: "service-name:function"')#
                   ->setValidation([$this, 'validateOperandServiceNameFunction'], [$this, 'validationMessageOperandServiceNameFunction']),#
            Operand::create('parameters', Operand::MULTIPLE)#
                   ->setDescription('Parameters of service function'),#
        ]);
    }

    /**
     * Validation message function for validateOperandServiceNameFunction.
     *
     * @param Operand $operand
     * @param $value
     *
     * @return mixed
     */
    public function validationMessageOperandServiceNameFunction(Operand $operand, $value) {
        return $this->validationMessage['ServiceNameFunction'];
    }

    /**
     * Validate operand "ServiceNameFunction" value.
     *
     * @param $value
     *
     * @return bool
     */
    public function validateOperandServiceNameFunction($value) {
        $invalid = (strpos($value, ':') === false);
        if ($invalid) {
            $this->validationMessage['ServiceNameFunction'] = 'Operand ServiceNameFunction "' . $value . '" has wrong format!';
        } else {
            $callable = $this->getServiceFunctionCallable($value);
            if ($callable === false) {
                $invalid = true;

                $this->validationMessage['ServiceNameFunction'] = 'No Service function "' . $value . '" found!';
            }
        }

        return !$invalid;
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, Logger $output) : void {
        $callable = $this->getServiceFunctionCallable($input->getOperand('ServiceNameFunction'));
        if (is_callable($callable)) {
            $parameters = $input->getOperand('parameters');
            $return     = call_user_func_array($callable, $parameters);
            if (!empty($return) || is_numeric($return)) {
                $output->notice(is_scalar($return) ? $return : print_r($return, true));
            }
        }
    }

    /**
     * Create service function callable array or error on error.
     *
     * @param string $serviceNameFunction String of format "serviceName:classFunction".
     *
     * @return array|bool Callable array or false on error.
     */
    private function getServiceFunctionCallable(string $serviceNameFunction) {
        $serviceInfos    = explode(':', $serviceNameFunction, 2);
        $serviceName     = $serviceInfos[0];
        $serviceFunction = $serviceInfos[1];

        try {
            $service  = Oforge()->Services()->get($serviceName);
            $callable = [$service, $serviceFunction];
            if (!is_callable($callable)) {
                return false;
            }

            return $callable;
        } catch (ServiceNotFoundException $e) {
            return false;
        }
    }

}
