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
use FrontendUserManagement\Models\UserDetail;
use FrontendUserManagement\Services\FrontendUserService;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Services\LanguageService;

/**
 * Class CommentService
 *
 * @package Blog\Services
 */
class CommentService extends AbstractDatabaseAccess {
    /** @var FrontendUserService $frontendUserService */
    private $frontendUserService;

    /**
     * @inheritDoc
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        parent::__construct([Comment::class => Comment::class, Post::class => Post::class]);
        $this->frontendUserService = Oforge()->Services()->get('frontend.user');
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
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     */
    public function createComment(array $data) {
        if (!$this->frontendUserService->isLoggedIn()) {
            throw new UserNotLoggedInException();
        }
        if ($this->isValid($data)) {
            $comment = Comment::create($data);
            $this->entityManager()->create($comment);
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
            $this->entityManager()->remove($comment);

            return true;
        } else {
            throw new CommentNotFoundException($commentID);
        }
    }

    /**
     * Get distinct posts of comments (with optional comment author filtering by query param author).
     *
     * @return array
     * @throws ServiceNotFoundException
     */
    public function getFilterDataPostsOfComments() : array {
        /** @var LanguageService $languageService */
        $languageService = Oforge()->Services()->get('i18n.language');
        $languages       = $languageService->getFilterDataLanguages();

        $queryBuilder = $this->getRepository(Post::class)->createQueryBuilder('p')#
                             ->select('p')#
                             ->leftJoin('p.comments', 'c')#
                             ->groupBy('p.id')#
                             ->distinct();
        if (ArrayHelper::issetNotEmpty($_GET, 'author')) {
            $queryBuilder = $queryBuilder->where('c.author = ?1')->setParameter(1, $_GET['author']);
        }
        /** @var Post[] $entities */
        $entities = $queryBuilder->getQuery()->getResult();

        $result = [];
        foreach ($entities as $entity) {
            $language     = $entity->getLanguage();
            $languageName = ArrayHelper::get($languages, $entity->getLanguage(), $entity->getLanguage());
            if (!isset($result[$language])) {
                $result[$language] = [
                    'label'   => $languageName,
                    'options' => [],
                ];
            }
            $result[$language]['options'][$entity->getId()] = $entity->getHeaderTitle();
        }

        return $result;
    }

    /**
     * Collect users for comments (with optional post filtering by query param post).
     *
     * @return array
     */
    public function getFilterDataUserNamesOfComments() : array {
        $result       = [];
        $queryBuilder = $this->getRepository(Comment::class)->createQueryBuilder('c')#
                             ->select('fu.id, fu.email, fud.nickName, fud.firstName, fud.lastName')#
                             ->leftJoin('c.author', 'fu')#
                             ->leftJoin(UserDetail::class, 'fud', 'WITH', 'fud.user = fu.id')#
                             #->groupBy('fu.id')#
                             ->distinct();
        if (ArrayHelper::issetNotEmpty($_GET, 'post')) {
            $queryBuilder = $queryBuilder->where('c.post = ?1')->setParameter(1, $_GET['post']);
        }
        /** @var UserDetail[] $entries */
        $entries = $queryBuilder->getQuery()->getResult();
        foreach ($entries as $entry) {
            $name = $entry['nickName'];
            if (empty($name)) {
                $name = $entry['lastName'] . ', ' . $entry['firstName'];
            }
            if (empty($name) || strlen($name) === 2) {
                $name = $entry['email'];
            }
            $result[$entry['id']] = $name;
        }

        return $result;
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
