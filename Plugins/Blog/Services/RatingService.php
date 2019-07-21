<?php

namespace Blog\Services;

use Blog\Exceptions\UserNotLoggedInException;
use Blog\Exceptions\UserRatingForPostNotFoundException;
use Blog\Models\Post;
use Blog\Models\Rating;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\FrontendUserService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class RatingService
 *
 * @package Blog\Services
 */
class RatingService extends AbstractDatabaseAccess {
    /** @var FrontendUserService $frontendUserService */
    private $frontendUserService;

    /** @inheritDoc */
    public function __construct() {
        parent::__construct([Post::class => Post::class, Rating::class => Rating::class]);
        $this->frontendUserService = Oforge()->Services()->get('frontend.user');
    }

    /**
     * Get user rating value for post.
     *
     * @param Post $post
     *
     * @return bool
     * @throws ORMException
     * @throws UserNotLoggedInException
     * @throws UserRatingForPostNotFoundException
     */
    public function getUserRatingOfPost(Post $post) : bool {
        if (!$this->frontendUserService->isLoggedIn()) {
            throw new UserNotLoggedInException();
        }
        $user = $this->frontendUserService->getUser();
        if (isset($user)) {
            $userID = $user->getID();
            /** @var Rating|null $rating */
            $rating = $this->repository(Rating::class)->findOneBy([
                'post'   => $post,
                'userID' => $userID,
            ]);
        }
        if (!isset($rating)) {
            throw new UserRatingForPostNotFoundException($post->getId());
        }

        return $rating->isRating();
    }

    /**
     * Create or update user rating of post.
     *
     * @param int $postID
     * @param bool $ratingValue
     *
     * @throws UserNotLoggedInException
     * @throws ORMException
     */
    public function createOrUpdateRating(int $postID, bool $ratingValue) {
        if (!$this->frontendUserService->isLoggedIn()) {
            throw new UserNotLoggedInException();
        }
        $userID        = $this->frontendUserService->getUser()->getID();
        $entityManager = $this->entityManager();
        /** @var Rating|null $rating */
        $rating = $this->repository(Rating::class)->findOneBy([
            'post'   => $postID,
            'userID' => $userID,
        ]);
        if (isset($rating)) {
            $rating->setRating($ratingValue);
            $rating = $entityManager->update($rating);
        } else {
            $rating = Rating::create([
                'post'   => $postID,
                'userID' => $userID,
                'rating' => $ratingValue,
            ]);
            $entityManager->create($rating);
        }
    }

    /**
     * Evaluate rating data of post.
     *
     * @param array $data
     */
    public function evaluateRating(array &$data) {
        if (isset($data['ratings'])) {
            $tmpRatings = $data['ratings'];
            $rating     = ['up' => 0, 'down' => 0];
            foreach ($tmpRatings as $tmpRating) {
                if (isset($tmpRating['rating'])) {
                    $rating[$tmpRating['rating'] ? 'up' : 'down']++;
                }
            }
            $data['ratings'] = $rating;
        }
    }

}
