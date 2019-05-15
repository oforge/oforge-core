<?php

namespace Blog\Services;

use Blog\Models\Category;
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

            $language = $languageService->getCurrentLanguageIso([]);// TODO Get current language ???
        }
        $categories = $this->repository()->findBy([
            'language' => $language,
        ]);

        return $categories;
    }

}
