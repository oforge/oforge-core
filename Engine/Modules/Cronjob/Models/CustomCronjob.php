<?php

namespace Oforge\Engine\Modules\Cronjob\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Annotation\ORM\Discriminator\DiscriminatorEntry;
use Oforge\Engine\Modules\Core\Exceptions\InvalidClassException;
use Oforge\Engine\Modules\Cronjob\Abstracts\AbstractCronjobHandler;

/**
 * Class CustomCronjob
 *
 * @ORM\Entity
 * @DiscriminatorEntry()
 * @package Oforge\Engine\Modules\Cronjob\Models
 */
class CustomCronjob extends AbstractCronjob {
    /**
     * @var string $title
     * @ORM\Column(name="class", type="string", nullable=false)
     */
    private $class;

    /**
     * CustomCronjob constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getClass() : string {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return CustomCronjob
     * @see AbstractCronjobHandler
     * @throws InvalidClassException When class not found or not subclass of AbstractCronjobHandler
     */
    public function setClass(string $class) : CustomCronjob {
        if ($this->isEditable()) {
            if (!class_exists($class) || !is_subclass_of($class, AbstractCronjobHandler::class)) {
                throw new InvalidClassException($class, AbstractCronjobHandler::class);
            }
            $this->class = $class;
        }

        return $this;
    }

}
