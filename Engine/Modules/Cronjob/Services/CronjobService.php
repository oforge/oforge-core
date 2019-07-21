<?php

namespace Oforge\Engine\Modules\Cronjob\Services;

use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use Monolog\Handler\RotatingFileHandler;
use Oforge\Engine\Modules\Console\Services\ConsoleService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\InvalidClassException;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Manager\Logger\LoggerManager;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\Cronjob\Abstracts\AbstractCronjobHandler;
use Oforge\Engine\Modules\Cronjob\CronjobStatics;
use Oforge\Engine\Modules\Cronjob\Models\AbstractCronjob;
use Oforge\Engine\Modules\Cronjob\Models\CommandCronjob;
use Oforge\Engine\Modules\Cronjob\Models\CustomCronjob;

/**
 * Class CronjobService
 *
 * @package Oforge\Engine\Modules\Cronjob\Services
 */
class CronjobService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(AbstractCronjob::class);
    }

    /**
     * Add cronjob.
     *
     * @param array $options
     *
     * @throws ConfigOptionKeyNotExistException
     * @throws InvalidClassException
     * @throws ORMException
     */
    public function addCronjob(array $options) {
        if ($this->isValid($options)) {
            if ($options['type'] === CommandCronjob::class) {
                $this->addCronjobInstances(CommandCronjob::create($options));
            } elseif ($options['type'] === CustomCronjob::class) {
                $this->addCronjobInstances(CustomCronjob::create($options));
            }
        }
    }

    /**
     * Add cronjobs.
     *
     * @param AbstractCronjob|AbstractCronjob[] $cronjobs
     *
     * @throws ORMException
     */
    public function addCronjobInstances($cronjobs) {
        if (!is_array($cronjobs)) {
            $cronjobs = [$cronjobs];
        }
        //Check if the element is already within the system
        foreach ($cronjobs as $cronjob) {
            $element = $this->repository()->findOneBy(['name' => $cronjob->getName()]);
            if (!isset($element)) {
                try {
                    $this->entityManager()->create($cronjob, false);
                } catch (Exception $exception) {
                    Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
                }
            }
        }
        try {
            $this->entityManager()->flush();
            $this->repository()->clear();
        } catch (Exception $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
        }
    }

    /**
     * Get cronjob by name.
     *
     * @param string $name
     *
     * @return AbstractCronjob
     * @throws NotFoundException
     * @throws ORMException
     */
    public function getCronjob(string $name) : AbstractCronjob {
        $name = trim($name);
        if (empty($name)) {
            throw new NotFoundException("Cronjob with name '$name' not found");
        }
        /** @var null|AbstractCronjob $cronjob */
        $cronjob = $this->repository()->findOneBy([
            'name' => $name,
        ]);
        if (is_null($cronjob)) {
            throw new NotFoundException("Cronjob with name '$name' not found");
        }

        return $cronjob;
    }

    /**
     * Get all cronjobs.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return AbstractCronjob[]
     * @throws ORMException
     */
    public function getCronjobs(array $criteria = [], ?array $orderBy = null, ?int $limit = null, ?int $offset = null) {
        return $this->repository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Run single cronjob manuel.
     * The next execution time will not be updated.
     *
     * @param string $name
     *
     * @return bool False on error.
     */
    public function run(string $name) {
        try {
            $cronjob = $this->getCronjob($name);
            $success = $this->runCronjob($cronjob);
            $this->entityManager()->update($cronjob);
            $this->repository()->clear();

            return $success;
        } catch (Exception $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
        }

        return false;
    }

    /**
     * Run all cronjobs with next execution time before now.
     *
     * @throws ORMException
     */
    public function runAll() {
        $query = $this->repository()->createQueryBuilder('c')#
                                  ->where('c.active = 1')#
                                  ->andWhere('c.nextExecutionTime IS NOT NULL')#
                                  ->andWhere('c.nextExecutionTime < CURRENT_TIMESTAMP()')#
                                  ->orderBy('c.order', 'ASC')#
                                  ->getQuery();
        /** @var AbstractCronjob[] $cronjobs */
        $cronjobs = $query->getResult();
        if (count($cronjobs) === 0) {
            return;
        }
        foreach ($cronjobs as $cronjob) {
            try {
                if ($this->runCronjob($cronjob)) {
                    $executionInterval = $cronjob->getExecutionInterval();
                    $lastExecutionTime = $cronjob->getNextExecutionTime() ?? new DateTimeImmutable();
                    $nextExecutionTime = $lastExecutionTime->add(new DateInterval('PT' . $executionInterval . 'S'));
                    $cronjob->setNextExecutionTime($nextExecutionTime);
                    $this->entityManager()->update($cronjob, false);
                }
            } catch (Exception $exception) {
                Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
            }
        }
        try {
            $this->entityManager()->flush();
        } catch (Exception $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
        }
        $this->repository()->clear();
    }

    /**
     * Validate cronjob options.
     *
     * @param array $options
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistException
     * @throws InvalidClassException
     */
    protected function isValid(array $options) : bool {
        if (!isset($options['type'])) {
            throw new ConfigOptionKeyNotExistException('type');
        }
        if (!in_array($options['type'], [CommandCronjob::class, CustomCronjob::class])) {
            $type = $options['type'];
            throw new InvalidArgumentException("Option type '$type' is not valid.");
        }
        $type = $options['type'];
        /**
         * Check if required keys are within the options
         */
        $requiredKeys = ['type', 'name', 'executionInterval'];
        if ($type === CommandCronjob::class) {
            $requiredKeys = array_merge($requiredKeys, ['command']);
        } elseif ($type === CustomCronjob::class) {
            $requiredKeys = array_merge($requiredKeys, ['class']);
        }
        foreach ($requiredKeys as $dataKey) {
            if (!isset($options[$dataKey]) || empty($options[$dataKey])) {
                throw new ConfigOptionKeyNotExistException($dataKey);
            }
        }
        /**
         * Check if correct type are set
         */
        $dataTypes = [
            'name'                 => 'string',
            'active'               => 'bool',
            'editable'             => 'bool',
            'logfile'              => 'bool',
            'title'                => 'string',
            'description'          => 'bool',
            'executionInterval'    => 'int',
            'lastExecutionTime'    => DateTimeImmutable::class,
            'lastExecutionSuccess' => 'bool',
            'nextExecutionTime'    => DateTimeImmutable::class,
            'type'                 => 'string',
            'order'                => 'int',
        ];
        if ($type === CommandCronjob::class) {
            $dataTypes = array_merge($requiredKeys, [
                'command'     => 'string',
                'commandArgs' => 'string',
            ]);
        } elseif ($type === CustomCronjob::class) {
            $dataTypes = array_merge($dataTypes, [
                'class' => 'string',
            ]);
        }
        foreach ($dataTypes as $dataKey => $dataType) {
            if (!isset($options[$dataKey])) {
                continue;
            }
            switch ($dataType) {
                case 'bool':
                    if (!is_bool($options[$dataKey])) {
                        throw new InvalidArgumentException("Option '$dataKey' should be of type string.");
                    }
                    break;
                case 'int':
                    if (!is_int($options[$dataKey])) {
                        throw new InvalidArgumentException("Option '$dataKey' should be of type string.");
                    }
                    break;
                case 'string':
                    if (!is_string($options[$dataKey])) {
                        throw new InvalidArgumentException("Option '$dataKey' should be of type string.");
                    }
                    break;
                default:
                    if (!is_a($options[$dataKey], $dataType)) {
                        throw new InvalidArgumentException("Option '$dataKey' should be of type $dataType.");
                    }
                    break;
            }
        }
        if ($type === CustomCronjob::class) {
            $className = AbstractCronjobHandler::class;
            if (!is_subclass_of($options['class'], $className) || !class_exists($options['class'])) {
                throw new InvalidClassException($options['class'], $className);
            }
        }

        return true;
    }

    /**
     * Run single Cronjob
     *
     * @param AbstractCronjob $cronjob
     *
     * @return bool
     * @throws Exception
     */
    protected function runCronjob(AbstractCronjob $cronjob) {
        $success = false;
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $startTime     = new DateTimeImmutable();

        if ($cronjob instanceof CommandCronjob) {
            try {
                /** @var ConsoleService $consoleService */
                $consoleService = Oforge()->Services()->get('console');
                $filePath       = $this->getLogFilePath($cronjob->getId());
                $maxFiles       = $configService->get(CronjobStatics::SETTING_LOGFILE_DAYS);
                $level          = $cronjob->getLogfileLevel();
                $fileHandler    = new RotatingFileHandler($filePath, $maxFiles, $level);
                $consoleService->addOutputLoggerHandler($fileHandler);
                try {
                    $command     = $cronjob->getCommand();
                    $commandArgs = $cronjob->getCommandArgs();
                    $consoleService->runCommand($command, $commandArgs);
                    $success = true;
                } catch (Exception $exception) {
                }
                $consoleService->removeOutputLoggerHandler($fileHandler);
            } catch (ServiceNotFoundException $exception) {
                Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
            }
        } elseif ($cronjob instanceof CustomCronjob) {
            try {
                $logger = Oforge()->Logger()->initLogger('cronjobs');
                $class  = $cronjob->getClass();
                if (class_exists($class)) {
                    if (is_subclass_of($class, AbstractCronjobHandler::class)) {
                        $cronjobLogger = Oforge()->Logger()->initLogger(str_replace(':', '_', $cronjob->getId()), [
                            'max_files' => $configService->get(CronjobStatics::SETTING_LOGFILE_DAYS),
                            'level'     => $cronjob->getLogfileLevel(),
                            'path'      => $this->getLogFilePath($cronjob->getId()),
                        ]);
                        /**
                         * @var AbstractCronjobHandler $instance
                         */
                        $instance = new $class();
                        $instance->handle($cronjobLogger);
                        $success = true;
                    } else {
                        $logger->err('Cronjob handler class is not a subclass of AbstractCronjobHandler.');
                    }
                } else {
                    $logger->err('Cronjob handler class does not exist.');
                }
            } catch (Exception $exception) {
                Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
            }
        }
        $updateData = [
            'lastExecutionSuccess'  => $success,
            'lastExecutionTime'     => new DateTimeImmutable(),
            'lastExecutionDuration' => $startTime->diff(new DateTimeImmutable()),
        ];
        if (!$success) {
            $updateData['active'] = false;
        }
        $cronjob->fromArray($updateData);

        return $success;
    }

    /**
     * Get full path for cronjob log file.
     *
     * @param string $cronjobName
     *
     * @return string
     */
    protected function getLogFilePath(string $cronjobName) : string {
        return CronjobStatics::CRONJOB_LOGS_DIR_ABS . DIRECTORY_SEPARATOR . str_replace(':', '_', $cronjobName) . LoggerManager::FILE_EXTENSION;
    }

}
