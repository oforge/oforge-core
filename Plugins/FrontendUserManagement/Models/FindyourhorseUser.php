<?php

namespace FrontendUserManagement\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="frontend_findyourhorse_user")
 * @ORM\Entity
 */
class FindyourhorseUser extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string $fyhMail
     * @ORM\Column(name="fyh_mail", type="string", nullable=false)
     */
    private $fyhMail;
    /**
     * @var User $user
     * @ORM\OneToOne(targetEntity="User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @return string
     */
    public function getFyhMail(): string
    {
        return $this->fyhMail;
    }

    /**
     * @param string $fyhMail
     * @return FindyourhorseUser
     */
    public function setFyhMail(string $fyhMail): FindyourhorseUser
    {
        $this->fyhMail = $fyhMail;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return FindyourhorseUser
     */
    public function setUser(User $user): FindyourhorseUser
    {
        $this->user = $user;
        return $this;
    }
}
