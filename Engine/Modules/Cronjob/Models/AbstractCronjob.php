<?php

namespace Oforge\Engine\Modules\Cronjob\Models;

use Doctrine\ORM\Mapping as ORM;
use Monolog\Logger;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Helper\Statics;

/**
 * Class Cronjob
 *
 * @ORM\Entity
 * @ORM\Table(name="oforge_cronjobs")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @package Oforge\Engine\Modules\Cronjob\Models
 */
abstract class AbstractCronjob extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", nullable=false, unique=true)
     */
    private $name;
    /**
     * @var bool $active
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default":false})
     */
    private $active = false;
    /**
     * @var bool $editable
     * @ORM\Column(name="editable", type="boolean", nullable=false, options={"default":true})
     */
    private $editable = true;
    /**
     * @var int $logfileLevel
     * @ORM\Column(name="logfile_level", type="integer", nullable=false, options={"default":100})
     */
    private $logfileLevel = Logger::DEBUG;
    /**
     * @var \DateTimeImmutable|null $lastExecution
     * @ORM\Column(name="next_execution_time", type="datetime_immutable", nullable=true, options={"default":null})
     */
    private $nextExecutionTime = null;
    /**
     * @var int $executionInterval
     * @ORM\Column(name="execution_interval", type="integer", nullable=false)
     */
    private $executionInterval;
    /**
     * @var string $title
     * @ORM\Column(name="title", type="string", nullable=false)
     */
    private $title;
    /**
     * @var string $description
     * @ORM\Column(name="description", type="string", nullable=false, options={"default":""})
     */
    private $description = '';
    /**
     * @var \DateTimeImmutable|null $lastExecutionTime
     * @ORM\Column(name="last_execution_time", type="datetime_immutable", nullable=true, options={"default":null})
     */
    private $lastExecutionTime = null;
    /**
     * @var bool $lastExecutionSuccess
     * @ORM\Column(name="last_execution_success", type="boolean", nullable=false, options={"default":false})
     */
    private $lastExecutionSuccess = false;
    /**
     * @var \DateInterval|null $lastExecutionDuration
     * @ORM\Column(name="last_execution_duration", type="dateinterval", nullable=true, options={"default":null})
     */
    private $lastExecutionDuration = null;
    /**
     * @var int
     * @ORM\Column(name="sort_order", type="integer", nullable=true, options={"default":Statics::DEFAULT_ORDER})
     */
    private $order = Statics::DEFAULT_ORDER;

    /**
     * Cronjob constructor.
     */
    protected function __construct() {
        $this->fromArray();
    }

    /**
     * @return string
     */
    public function getId() : string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return AbstractCronjob
     */
    protected function setName(string $name) : AbstractCronjob {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive() : bool {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return AbstractCronjob
     */
    public function setActive(bool $active) : AbstractCronjob {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEditable() : bool {
        return $this->editable;
    }

    /**
     * @param bool $editable
     */
    protected function setEditable(bool $editable) : void {
        $this->editable = $editable;
    }

    /**
     * @return int
     */
    public function getLogfileLevel() : int {
        return $this->logfileLevel;
    }

    /**
     * @param int $logfileLevel
     */
    public function setLogfileLevel(int $logfileLevel) : void {
        $this->logfileLevel = $logfileLevel;
    }

    /**
     * @return string
     */
    public function getTitle() : string {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return AbstractCronjob
     */
    public function setTitle(string $title) : AbstractCronjob {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() : string {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return AbstractCronjob
     */
    public function setDescription(string $description) : AbstractCronjob {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateInterval|null
     */
    public function getLastExecutionDuration() : ?\DateInterval {
        return $this->lastExecutionDuration;
    }

    /**
     * @param \DateInterval $lastExecutionDuration
     */
    public function setLastExecutionDuration(\DateInterval $lastExecutionDuration) : void {
        $this->lastExecutionDuration = $lastExecutionDuration;
    }

    /**
     * @return int
     */
    public function getExecutionInterval() : int {
        return $this->executionInterval;
    }

    /**
     * @param int $executionInterval
     *
     * @return AbstractCronjob
     */
    public function setExecutionInterval(int $executionInterval) : AbstractCronjob {
        $this->executionInterval = $executionInterval;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getLastExecutionTime() {
        return $this->lastExecutionTime;
    }

    /**
     * @param \DateTimeImmutable $lastExecutionTime
     *
     * @return AbstractCronjob
     */
    public function setLastExecutionTime(\DateTimeImmutable $lastExecutionTime) : AbstractCronjob {
        $this->lastExecutionTime = $lastExecutionTime;

        return $this;
    }

    /**
     * @return bool
     */
    public function isLastExecutionSuccess() : bool {
        return $this->lastExecutionSuccess;
    }

    /**
     * @param bool $lastExecutionSuccess
     *
     * @return AbstractCronjob
     */
    public function setLastExecutionSuccess(bool $lastExecutionSuccess) : AbstractCronjob {
        $this->lastExecutionSuccess = $lastExecutionSuccess;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getNextExecutionTime() {
        return $this->nextExecutionTime;
    }

    /**
     * @param \DateTimeImmutable $nextExecutionTime
     *
     * @return AbstractCronjob
     */
    public function setNextExecutionTime(\DateTimeImmutable $nextExecutionTime) : AbstractCronjob {
        $this->nextExecutionTime = $nextExecutionTime;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrder() : int {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order) : void {
        $this->order = $order;
    }

}
