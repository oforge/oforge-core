<?php

namespace Faq\Models;

use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="oforge_faq")
 * @ORM\Entity
 */
class FaqModel extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="question", type="string", nullable=false, unique=true)
     */
    private $question;

    /**
     * @var string
     * @ORM\Column(name="answer", type="text", nullable=false)
     */
    private $answer;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getQuestion() : string {
        return $this->question;
    }

    /**
     * @param string $question
     *
     * @return FaqModel
     */
    public function setQuestion(string $question) : FaqModel {
        $this->question = $question;

        return $this;
    }

    /**
     * @return string
     */
    public function getAnswer() : string {
        return $this->answer;
    }

    /**
     * @param string $answer
     *
     * @return FaqModel
     */
    public function setAnswer(string $answer) : FaqModel {
        $this->answer = $answer;

        return $this;
    }
}
