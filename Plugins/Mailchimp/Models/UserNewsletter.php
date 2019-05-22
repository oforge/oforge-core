<?php


namespace Mailchimp\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_mailchimp")
 * @ORM\Entity
 */
class UserNewsletter extends AbstractModel
{
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     * @ORM\Column(name="subscribed", type="boolean", nullable=false)
     */
    private $subscribed;

    /**
     * @var int $id
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     * @ORM\OneToOne(targetEntity="User")
     */
    private $userId;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * @return bool
     */
    public function isSubscribed(): bool
    {
        return $this->subscribed;
    }

    /**
     * @param bool $subscribed
     * @return UserNewsletter
     */
    public function setSubscribed(bool $subscribed): UserNewsletter
    {
        $this->subscribed = $subscribed;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return UserNewsletter
     */
    public function setUserId(int $userId): UserNewsletter
    {
        $this->userId = $userId;
        return $this;
    }
}