<?php

namespace Oforge\Engine\Modules\Core\Models\Config;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Exceptions\EncryptionException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\EncryptionService;

/**
 * @ORM\Entity
 * @ORM\Table(name="oforge_core_config_values")
 */
class Value extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var Config $config
     * @ORM\ManyToOne(targetEntity="Config", inversedBy="values")
     * @ORM\JoinColumn(name="config_id", referencedColumnName="id")
     */
    private $config;
    /**
     * @var mixed $value
     * @ORM\Column(name="value", type="object", nullable=true, options={"default":null})
     */
    private $value = null;
    /**
     * @var string $scope
     * @ORM\Column(name="scope", type="string", nullable=true, options={"default":null})
     */
    private $scope = null;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return Config
     */
    public function getConfig() : Config {
        return $this->config;
    }

    /**
     * @param Config $config
     *
     * @return Value
     */
    public function setConfig(Config $config) : Value {
        $this->config = $config;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        if (!is_null($this->config) && $this->config->getType() === ConfigType::PASSWORD) {
            try {
                /** @var EncryptionService $encryptionService */
                $encryptionService = Oforge()->Services()->get('encryption');

                return $encryptionService->decrypt($this->value);
            } catch (ServiceNotFoundException $exception) {
                Oforge()->Logger()->logException($exception);
            } catch (EncryptionException $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }

        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return Value
     */
    public function setValue($value) : Value {
        if (!is_null($this->config) && $this->config->getType() === ConfigType::PASSWORD) {
            $this->value = base64_encode($value);
            try {
                /** @var EncryptionService $encryptionService */
                $encryptionService = Oforge()->Services()->get('encryption');
                $this->value       = $encryptionService->encrypt($this->value);
            } catch (ServiceNotFoundException $exception) {
                Oforge()->Logger()->logException($exception);
                $this->value = $value;
            } catch (EncryptionException $exception) {
                Oforge()->Logger()->logException($exception);
                $this->value = $value;
            }
        } else {
            $this->value = $value;
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getScope() : ?string {
        return $this->scope;
    }

    /**
     * @param string $scope
     *
     * @return Value
     */
    public function setScope(string $scope) : Value {
        $this->scope = $scope;

        return $this;
    }

}
