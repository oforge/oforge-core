<?php

namespace Helpdesk\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_helpdesk_issue_type_group")
 * @ORM\Entity
 */
class IssueTypeGroup extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="issue_type_group_name", type="string", nullable=false, unique=true)
     */
    private $issueTypeGroupName;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="IssueTypes", mappedBy="issueTypeGroup")
     */
    private $issueTypes;

    public function __construct() {
        $this->issueTypes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIssueTypeGroupName() : string {
        return $this->issueTypeGroupName;
    }

    /**
     * @param string $issueTypeGroupName
     *
     * @return IssueTypeGroup
     */
    public function setIssueTypeGroupName(string $issueTypeGroupName) : IssueTypeGroup {
        $this->issueTypeGroupName = $issueTypeGroupName;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getIssueTypes() {
        return $this->issueTypes;
    }

    /**
     * @param ArrayCollection $issueTypes
     *
     * @return IssueTypeGroup
     */
    public function setIssueTypes($issueTypes) {
        $this->issueTypes = $issueTypes;
        return $this;
    }

    /**
     * @param IssueTypes $issueType
     * @return IssueTypeGroup
     */
    public function addIssueType($issueType) {
        if (!$this->issueTypes->contains($issueType)) {
            $this->issueTypes->add($issueType);
        }

        return $this;
    }

}