<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:15
 */

namespace Oforge\Engine\Modules\Auth\Models\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="oforge_auth_backend_user")
 * @ORM\HasLifecycleCallbacks
 */
class BackendUser extends BaseUser {

    /**
     * TODO: This values should not be constants. What if we want to add a new role?
     *
     */
    public const ROLE_SYSTEM        = 0;
    public const ROLE_ADMINISTRATOR = 1;
    public const ROLE_MODERATOR     = 2;
    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;
    /**
     * 0 = admin, 1 = moderator, 2 = other
     *
     * @var int
     * @ORM\Column(name="role", type="integer", nullable=false)
     */
    private $role;

    /**
     * @var BackendUserDetail $detail
     * @ORM\OneToOne(targetEntity="BackendUserDetail", mappedBy="user", fetch="EXTRA_LAZY", cascade={"all"})
     * @ORM\JoinColumn(name="detail_id", referencedColumnName="id")
     */
    private $detail;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getRole() : int {
        return $this->role;
    }

    /**
     * @param $role int
     */
    public function setRole($role) {
        $this->role = $role;
    }

    /**
     * @return BackendUserDetail
     */
    public function getDetail() : ?BackendUserDetail {
        return $this->detail;
    }

    /**
     * @param BackendUserDetail $detail
     */
    public function setDetail(BackendUserDetail $detail) : void {
        $this->detail = $detail;
    }
}
