<?php

namespace Blog\Services;

use Blog\Models\Post;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
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
            $language        = $languageService->getCurrentLanguageIso([]);// TODO Get current language???

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

}
