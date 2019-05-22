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
    /** @var AuthService authService */
    private $authService;

    /** @inheritDoc */
    public function __construct() {
        parent::__construct([Post::class => Post::class, Rating::class => Rating::class]);
        $this->authService = Oforge()->Services()->get('blog.auth');
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
        if (!$this->authService->isUserLoggedIn()) {
            throw new UserNotLoggedInException();
        }
        $userID = $this->authService->getUserID();
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
     * @throws PostNotFoundException
     * @throws ORMException
     */
    public function createOrUpdateRating(int $postID, bool $ratingValue) {
        if (!$this->authService->isUserLoggedIn()) {
            throw new UserNotLoggedInException();
        }
        $userID = $this->authService->getUserID();
        /** @var Post|null $post */
        $post = $this->repository(Post::class)->findOneBy(['id' => $postID]);
        if (!isset($post)) {
            throw new PostNotFoundException($postID);
        }
        $entityManager = $this->entityManager();
        /** @var Rating|null $rating */
        $rating = $this->repository(Rating::class)->findOneBy([
            'post'   => $post,
            'userID' => $userID,
        ]);
        if (!isset($rating)) {
            $rating = Rating::create([
                'post'   => $post,
                'userID' => $userID,
                'rating' => $ratingValue,
            ]);
            $entityManager->persist($rating);
        } else {
            $rating = $entityManager->merge($rating);
            $rating->setRating($ratingValue);
        }
        $entityManager->flush($rating);
    }

    /**
     * Evaluate rating data of post.
     *
     * @param array $data
     */
    public function evaluateRating(array &$data) {
        $tmpRatings = $data['ratings'];
        $rating     = ['up' => 0, 'down' => 0];
        foreach ($tmpRatings as $tmpRating) {
            $rating[$tmpRating['rating'] ? 'up' : 'down']++;
        }
        $data['ratings'] = $rating;
    }

}
