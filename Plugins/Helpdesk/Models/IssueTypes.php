<?php

namespace Helpdesk\Models;

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
     * @ORM\Column(name="issue_type_name", type="string", nullable=false, unique=true)
     */
    private $issueTypeName;

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
}