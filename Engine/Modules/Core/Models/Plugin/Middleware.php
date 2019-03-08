<?php

namespace Oforge\Engine\Modules\Core\Models\Plugin;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_core_middleware")
 * @ORM\Entity
 */
class Middleware extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="path_name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = false;

    /**
     * @var string
     * @ORM\Column(name="class", type="string", nullable=false)
     */
    private $class;

    /**
     * @var string
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position = 0;

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="Plugin", inversedBy="middlewares", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="plugin_id", referencedColumnName="id")
     */
    private $plugin;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Plugin
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set class
     *
     * @param string $class
     *
     * @return Plugin
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set position
     *
     * @param string $position
     *
     * @return Plugin
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param bool $active
     *
     * @return Plugin
     */
    public function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }


    /**
     * @param Plugin $plugin
     *
     * @return Middleware
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }

    /**
     * @return Middleware[]
     */
    public function getPlugin()
    {
        return $this->plugin;
    }


}
