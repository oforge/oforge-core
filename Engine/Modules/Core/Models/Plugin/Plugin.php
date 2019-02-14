<?php

namespace Oforge\Engine\Modules\Core\Models\Plugin;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_core_plugins")
 * @ORM\Entity
 */
class Plugin extends AbstractModel
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
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = false;

    /**
     * @var bool
     * @ORM\Column(name="installed", type="boolean")
     */
    private $installed = false;


    /**
     * @var int
     * @ORM\Column(name="orderby", type="integer", nullable=true)
     */
    private $order;

    /**
     * @var Middleware[]
     * @ORM\OneToMany(targetEntity="Middleware", mappedBy="plugin", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="plugin_id")
     */
    private $middlewares;

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
     * @param bool $installed
     *
     * @return Plugin
     */
    public function setInstalled($installed)
    {
        $this->installed = $installed;
        return $this;
    }

    /**
     * @return bool
     */
    public function getInstalled()
    {
        return $this->installed;
    }

    /**
     * @param Middleware[] $middlewares
     *
     * @return Plugin
     */
    public function setMiddlewares($middlewares)
    {
        $this->middlewares = $middlewares;
        return $this;
    }
    /**
     * @return Middleware[]
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder(int $order)
    {
        $this->order = $order;
    }

}
