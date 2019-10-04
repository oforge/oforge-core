<?php

namespace Insertion\Services;

use Insertion\Models\InsertionFeedback;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Doctrine\ORM\ORMException;

class InsertionFeedbackService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default' => InsertionFeedback::class,
        ]);
    }

    /**
     * @throws ORMException
     */
    public function savePostData() {
        if (!empty($_POST)) {

            $data     = ["rating" => $_POST["feedback_rating"], "text" => $_POST["feedback_text"]];
            $feedback = InsertionFeedback::create($data);
            $this->entityManager()->create($feedback);
        }
    }
}
