<?php

namespace Blog\Services;

use Blog\Exceptions\CommentNotFoundException;
use Blog\Exceptions\PostNotFoundException;
use Blog\Exceptions\UserNotLoggedInException;
use Blog\Models\Comment;
use Blog\Models\Post;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\ConfigService;

/**
 * Class CommentService
 *
 * @package Blog\Services
 */
class CommentService extends AbstractDatabaseAccess {
    /** @var AuthService authService */
    private $authService;

    /**
     * @inheritDoc
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        parent::__construct([Comment::class => Comment::class, Post::class => Post::class]);
        $this->authService = Oforge()->Services()->get('blog.auth');
    }

    /**
     * Get comment by ID.
     *
     * @param string $commentID
     *
     * @return Comment|null
     * @throws ORMException
     */
    public function getCommentByID(string $commentID) : ?Comment {
        /** @var Comment|null $comment */
        $comment = $this->repository(Comment::class)->findOneBy(['id' => $commentID]);

        return $comment;
    }

    /**
     * Get comments of post (by ID), with pagination.
     *
     * @param int $postID
     * @param int $page
     *
     * @return Comment[]
     * @throws ORMException
     * @throws PostNotFoundException
     */
    public function getComments(int $postID, int $page) {
        /** @var Post|null $post */
        $post = $this->repository(Post::class)->findOneBy(['id' => $postID]);
        if (!isset($post)) {
            throw new PostNotFoundException($postID);
        }

        return $this->getCommentsOfPost($post, $page);
    }

    /**
     * Get comments of post , with pagination.
     *
     * @param Post $post
     * @param int $page
     *
     * @return Comment[]
     */
    public function getCommentsOfPost(Post $post, int $page = 0) {
        try {
            /** @var ConfigService $service */
            $configService   = Oforge()->Services()->get('config');
            $entitiesPerPage = $configService->get('blog_load_more_epp_comments');
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
            $entitiesPerPage = 5;
        }
        $offset = $page * $entitiesPerPage;

        return $post->getComments()->slice($offset, $entitiesPerPage);
    }

    /**
     * Create comment for post(ID).
     *
     * @param array $data
     *
     * @throws UserNotLoggedInException
     * @throws ORMException
     */
    public function createComment(array $data) {
        if (!$this->authService->isUserLoggedIn()) {
            throw new UserNotLoggedInException();
        }
        if ($this->isValid($data)) {
            $comment = Comment::create($data);
            $this->entityManager()->persist($comment);
            $this->entityManager()->flush($comment);
        }
    }

    /**
     * Delete comment by ID.
     *
     * @param string $commentID
     *
     * @return bool
     * @throws CommentNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteComment(string $commentID) : bool {
        $comment = $this->getCommentByID($commentID);
        if (isset($comment)) {
            $comment = $this->entityManager()->merge($comment);
            $this->entityManager()->remove($comment);
            $this->entityManager()->flush($comment);

            return true;
        } else {
            throw new CommentNotFoundException($commentID);
        }
    }

    /**
     * Validate data.
     *
     * @param array $data
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistException
     * @throws InvalidArgumentException
     */
    protected function isValid(array $data) : bool {
        $requiredKeys = ['content'];
        foreach ($requiredKeys as $dataKey) {
            if (!isset($data[$dataKey]) || $data[$dataKey] === '') {
                throw new ConfigOptionKeyNotExistException($dataKey);
            }
        }
        $dataTypes = [
            'contents' => 'string',
        ];
        foreach ($dataTypes as $dataKey => $dataType) {
            if (!isset($options[$dataKey])) {
                continue;
            }
            switch ($dataType) {
                case 'string':
                    if (!is_string($data[$dataKey])) {
                        throw new InvalidArgumentException("Option '$dataKey' should be of type string.");
                    }
                    break;
                default:
                    if (!is_a($data[$dataKey], $dataType)) {
                        throw new InvalidArgumentException("Option '$dataKey' should be of type $dataType.");
                    }
                    break;
            }
        }

        return true;
    }

}
