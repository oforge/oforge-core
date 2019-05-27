<?php

namespace Blog\Services;

use Blog\Exceptions\PostNotFoundException;
use Blog\Exceptions\UserNotLoggedInException;
use Blog\Exceptions\UserRatingForPostNotFoundException;
use Blog\Models\Post;
use Blog\Models\Rating;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class RatingService
 *
 * @package Blog\Services
 */
class RatingService extends AbstractDatabaseAccess {
    /** @var UserService $userService */
    private $userService;

    /** @inheritDoc */
    public function __construct() {
        parent::__construct([Post::class => Post::class, Rating::class => Rating::class]);
        $this->userService = Oforge()->Services()->get('blog.user');
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
        if (!$this->userService->isLoggedIn()) {
            throw new UserNotLoggedInException();
        }
        $userID = $this->userService->getID();
        /** @var Rating|null $rating */
        $rating = $this->repository(Rating::class)->findOneBy([
            'post'   => $post,
            'userID' => $userID,
        ]);
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
        if (!$this->userService->isLoggedIn()) {
            throw new UserNotLoggedInException();
        }
        $userID = $this->userService->getID();
        $entityManager = $this->entityManager();
        /** @var Rating|null $rating */
        $rating = $this->repository(Rating::class)->findOneBy([
            'post'   => $postID,
            'userID' => $userID,
        ]);
        if (isset($rating)) {
            $rating = $entityManager->merge($rating);
            $rating->setRating($ratingValue);
        } else {
            $rating = Rating::create([
                'post'   => $postID,
                'userID' => $userID,
                'rating' => $ratingValue,
            ]);
            $entityManager->persist($rating);
        }
        $entityManager->flush($rating);
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
