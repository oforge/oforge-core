<?php

namespace Blog\Services;

use Blog\Exceptions\PostNotFoundException;
use Blog\Models\Category;
use Blog\Models\Comment;
use Blog\Models\Post;
use Blog\Models\Rating;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Services\LanguageService;

/**
 * Class PostService
 *
 * @package Blog\Services
 */
class PostService extends AbstractDatabaseAccess {

    /** @inheritDoc */
    public function __construct() {
        parent::__construct(Post::class);
    }

    /**
     * @param int $postID
     *
     * @return Post|null
     * @throws ORMException
     */
    public function getPost(int $postID) : ?Post {
        /** @var Post|null $post */
        $post = $this->repository()->findOneBy([
            'id' => $postID,
        ]);

        return $post;
    }

    /**
     * @param int $page
     * @param int|null $categoryID
     *
     * @return array|Post[]
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws ConfigElementNotFoundException
     */
    public function getPosts(int $page, ?int $categoryID) {
        /**
         * @var Post[] $posts
         * @var ConfigService $configService
         */
        $configService = Oforge()->Services()->get('config');
        if (isset($categoryID)) {
            $criteria = [
                'category' => $categoryID,
            ];
        } else {
            /** @var LanguageService $languageService */
            $languageService = Oforge()->Services()->get('i18n.language');
            $language        = $languageService->getCurrentLanguageIso([]);

            $criteria = [
                'language' => $language,
            ];
        }
        $oderBy = [
            'created' => 'DESC',
        ];
        $limit  = null;
        $offset = null;
        if ($page > -1) {
            $limit  = $configService->get('blog_load_more_epp_posts');
            $offset = $page * $limit;
        }

        $posts = $this->repository()->findBy($criteria, $oderBy, $limit, $offset);

        return $posts;
    }

    /**
     * Get categories of posts (with optional filtering by query param language).
     *
     * @return array
     * @throws ServiceNotFoundException
     */
    public function getFilterDataCategoriesOfPosts() : array {
        /** @var LanguageService $languageService */
        $languageService = Oforge()->Services()->get('i18n.language');
        $languages       = $languageService->getFilterDataLanguages();

        $result             = [];
        $criteria           = [];
        $filteredByLanguage = false;
        if (ArrayHelper::issetNotEmpty($_GET, 'language')) {
            $criteria['language'] = $_GET['language'];
            $filteredByLanguage   = true;
        }
        /** @var Category[] $entities */
        $entities = $this->getRepository(Category::class)->findBy($criteria);
        foreach ($entities as $entity) {
            if ($filteredByLanguage) {
                $result[$entity->getId()] = $entity->getName();
            } else {
                $language     = $entity->getLanguage();
                $languageName = ArrayHelper::get($languages, $entity->getLanguage(), $entity->getLanguage());
                if (!isset($result[$language])) {
                    $result[$language] = [
                        'label'   => $languageName,
                        'options' => [],
                    ];
                }
                $result[$language]['options'][$entity->getId()] = $entity->getName();
            }
        }

        return $result;
    }

    /**
     * Get users of posts.
     *
     * @return array
     */
    public function getFilterDataUsersOfPosts() : array {
        $result = [];
        /** @var BackendUser[] $entities */
        $entities = $this->getRepository(BackendUser::class)->findBy(['active' => true]);
        foreach ($entities as $entity) {
            $name = $entity->getName();
            if (empty(trim($name))) {
                $name = $entity->getEmail();
            }
            $result[$entity->getId()] = $name;
        }

        return $result;
    }

    /**
     * Count comments of posts
     *
     * @return array
     */
    public function getFilterDataCommentsCountOfPosts() : array {
        $result  = [];
        $entries = $this->getRepository(Comment::class)->createQueryBuilder('c')#
                        ->select('IDENTITY(c.post) as id, COUNT(c) as value')#
                        ->groupBy('c.post')#
                        ->getQuery()->getArrayResult();
        foreach ($entries as $entry) {
            $result[$entry['id']] = $entry['value'];
        }

        return $result;
    }

    /**
     * @param string $postID
     *
     * @return array
     * @throws PostNotFoundException
     */
    public function delete(string $postID) : ?array {
        try {
            /** @var Post $post */
            $post = $this->getPost((int) $postID);
            if (!isset($post)) {
                throw new PostNotFoundException($postID);
            }
            $commentsDeleted = $this->entityManager()->createQueryBuilder()#
                                    ->delete(Comment::class, 'c')#
                                    ->where('c.post = :postID')#
                                    ->setParameter('postID', $postID)#
                                    ->getQuery()->getScalarResult();
            $ratingsDeleted  = $this->entityManager()->createQueryBuilder()#
                                    ->delete(Rating::class, 'r')#
                                    ->where('r.post = :postID')#
                                    ->setParameter('postID', $postID)#
                                    ->getQuery()->getScalarResult();
            $this->entityManager()->remove($post);

            return [$commentsDeleted, $ratingsDeleted];
        } catch (ORMException $exception) {
            Oforge()->Logger()->logException($exception);

            return null;
        }
    }

}
