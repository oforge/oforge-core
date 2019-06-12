<?php

namespace Helpdesk\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_helpdesk_issue_type")
 * @ORM\Entity
 */
class IssueTypes extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="issue_type_name", type="string", nullable=false, unique=false)
     */
    private $issueTypeName;

    /**
     * @ORM\ManyToOne(targetEntity="Helpdesk\Models\IssueTypeGroup", inversedBy="issueTypes")
     * @ORM\JoinColumn(name="issue_type_group_id", referencedColumnName="id")
     */
    private $issueTypeGroup;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIssueTypeName() : string {
        return $this->issueTypeName;
    }

    /**
     * @param string $issueTypeName
     *
     * @return IssueTypes
     */
    public function setIssueTypeName(string $issueTypeName) : IssueTypes {
        $this->issueTypeName = $issueTypeName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIssueTypeGroup() {
        return $this->issueTypeGroup;
    }

    /**
     * @param mixed $issueTypeGroup
     *
     * @return IssueTypes
     */
    public function setIssueTypeGroup($issueTypeGroup) {
        $this->issueTypeGroup = $issueTypeGroup;

        return $this;
    }


}