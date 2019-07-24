<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * Class PageService
 *
 * @package Oforge\Engine\Modules\CMS\Services
 */
class PageService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(PagePath::class);
    }

    /**
     * Check if there is a cms url path
     *
     * @param string $path
     *
     * @return bool
     * @throws ORMException
     */
    public function hasPath(string $path) {
        $data = $this->repository()->findOneBy(['path' => $path]);

        return isset($data);
    }

    /**
     * Get a PagePath entity by url path
     *
     * @param string $path
     *
     * @return PagePath|null
     * @throws ORMException
     */
    public function getPagePath(string $path, $languageID = null) : ?PagePath {
        /** @var PagePath|null $pagePath */
        $criteria = ['path' => $path];
        if (isset($languageID)) {
            $criteria['language'] = $languageID;
        }
        $pagePath = $this->repository()->findOneBy($criteria);

        return $pagePath;
    }

    /**
     * Load the for a page path
     *
     * @param PagePath|string $pagePath
     *
     * @return array | null
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function loadContentForPagePath($pagePath) {
        if (is_string($pagePath)) {
            $pagePath = $this->getPagePath($pagePath);
        }

        if (isset($pagePath)) {
            $language = $pagePath->getLanguage()->getId();

            /** @var PageBuilderService $pageBuilderService */
            $pageBuilderService = Oforge()->Services()->get('page.builder.service');
            $pageArray          = $pageBuilderService->getPageArray($pagePath->getPage()->getId());
            $pageContents       = $pageArray['paths'][$language]['pageContent'];

            $data = $pageBuilderService->getContentDataArray($pageContents);

            return $data;
        }

        return null;
    }
}

