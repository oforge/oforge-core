<?php

namespace Insertion\Services;

use FrontendUserManagement\Services\UserService;
use Insertion\Models\InsertionFeedback;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

class InsertionFeedbackService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default' => InsertionFeedback::class,
        ]);
    }

    /**
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function savePostData() {
        if (!empty($_POST)) {

            $data     = ["rating" => $_POST["feedback_rating"], "text" => $_POST["feedback_text"]];

            if (isset($_POST["feedback_user"]) && !empty($_POST["feedback_user"])) {
                /** @var UserService $userService */
                $userService  = Oforge()->Services()->get('frontend.user.management.user');
                $user         = $userService->getUserById($_POST["feedback_user"]);
                $data["user"] = $user;
                /** User didn't submit a rating */
                if ($data["rating"] == null) {
                    $data["rating"] = -1;
                }
            }
            $feedback = InsertionFeedback::create($data);
            $this->entityManager()->create($feedback);
        }
    }

    public function deleteForUser(int $userID) {
        try {
            $bookmarks = $this->repository()->findBy(["user" => $userID]);
            foreach ($bookmarks as $bookmark) {
                $this->entityManager()->remove($bookmark, false);
            }
        } catch (\Exception $exception) {
            Oforge()->Logger()->logException($exception);

            return false;
        }
    }
}
