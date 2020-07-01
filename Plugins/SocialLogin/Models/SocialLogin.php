<?php


namespace SocialLogin\Models;

use Doctrine\ORM\Mapping as ORM;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_usermanagement_social")
 * @ORM\Entity
 */
class SocialLogin extends AbstractModel
{
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var User $user
     * @ORM\ManyToOne(targetEntity="FrontendUserManagement\Models\User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var string|null $type
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    private $type;


    /**
     * @var string|null $token
     * @ORM\Column(name="token", type="string", nullable=true)
     */
    private $token;


    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser() : User {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return SocialLogin
     */
    public function setUser(User $user) : SocialLogin {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType() : ?string {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return SocialLogin
     */
    public function setType(?string $type) : SocialLogin {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToken() : ?string {
        return $this->token;
    }

    /**
     * @param string|null $token
     *
     * @return SocialLogin
     */
    public function setToken(?string $token) : SocialLogin {
        $this->token = $token;

        return $this;
    }
}
