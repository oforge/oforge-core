<?php

namespace Blog\Services;

use Blog\Models\Category;
use Blog\Models\Post;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Services\LanguageService;

/**
 * Class CategoryService
 *
 * @package Blog\Services
 */
class CategoryService extends AbstractDatabaseAccess {

    /** @inheritDoc */
    public function __construct() {
        parent::__construct(Category::class);
    }

    /**
     * Get category by ID.
     *
     * @param int $categoryID
     *
     * @return Category|null
     * @throws ORMException
     */
    public function getCategoryByID(int $categoryID) : ?Category {
        /** @var Category|null $category */
        $category = $this->repository()->findOneBy([
            'id' => $categoryID,
        ]);

        return $category;
    }

    /**
     * Get categories for give language or if null by current system/user language.
     *
     * @param string|null $language
     *
     * @return Category[]
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function getCategories(?string $language = null) {
        if (!isset($language)) {
            /**
             * @var LanguageService $languageService
             * @var Category[] $categories
             */
            $languageService = Oforge()->Services()->get('i18n.language');

            $language = $languageService->getCurrentLanguageIso([]);
        }
        $categories = $this->repository()->findBy([
            'language' => $language,
        ]);

        return $categories;
    }

    /**
     * Count posts for every category.
     *
     * @return array
     */
    public function getFilterDataPostCountOfCategories() : array {
        $result  = [];
        $entries = $this->getRepository(Post::class)->createQueryBuilder('p')#
                        ->select('IDENTITY(p.category) as id, COUNT(p) as value')#
                        ->groupBy('p.category')#
                        ->getQuery()->getArrayResult();
        foreach ($entries as $entry) {
            $result[$entry['id']] = $entry['value'];
        }

        return $result;
    }

}
