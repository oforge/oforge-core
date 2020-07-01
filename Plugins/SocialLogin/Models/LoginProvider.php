<?php


namespace SocialLogin\Models;

use Doctrine\ORM\Mapping as ORM;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_usermanagement_social_provider")
 * @ORM\Entity
 */
class LoginProvider extends AbstractModel
{
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null $appId
     * @ORM\Column(name="app_id", type="string", nullable=true)
     */
    private $appId;

    /**
     * @var string|null $appKey
     * @ORM\Column(name="app_key", type="string", nullable=true)
     */
    private $appKey;

    /**
     * @var string|null $secret
     * @ORM\Column(name="secret", type="string", nullable=true)
     */
    private $secret;

    /**
     * @var string|null $name
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

    /**
     * @return string|null
     */
    public function getName() : ?string {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return LoginProvider
     */
    public function setName(?string $name) : LoginProvider {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return LoginProvider
     */
    public function setId(int $id) : LoginProvider {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAppId() : ?string {
        return $this->appId;
    }

    /**
     * @param string|null $appId
     *
     * @return LoginProvider
     */
    public function setAppId(?string $appId) : LoginProvider {
        $this->appId = $appId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAppKey() : ?string {
        return $this->appKey;
    }

    /**
     * @param string|null $appKey
     *
     * @return LoginProvider
     */
    public function setAppKey(?string $appKey) : LoginProvider {
        $this->appKey = $appKey;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSecret() : ?string {
        return $this->secret;
    }

    /**
     * @param string|null $secret
     *
     * @return LoginProvider
     */
    public function setSecret(?string $secret) : LoginProvider {
        $this->secret = $secret;

        return $this;
    }

}
