<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

class PageService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => PagePath::class]);
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
        $data = $this->repository()->findOneBy(["path" => $path]);

        return isset($data);
    }

    /**
     * Get a PagePath entity by url path
     * @param string $path
     *
     * @return PagePath|object|null
     * @throws ORMException
     */
    private function getPagePath(string $path) : ?PagePath {
        return $this->repository()->findOneBy(["path" => $path]);
    }

    /**
     * Load the for a page path
     * @param string $path
     *
     * @return array | null
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function loadContentForPagePath($path) {
        $pagePath = $this->getPagePath($path);

        if (isset($pagePath)) {

            $language = $pagePath->getLanguage()->getId();

            /** @var PageBuilderService $pageBuilderService */
            $pageBuilderService = Oforge()->Services()->get('page.builder.service');
            $pageArray        = $pageBuilderService->getPageArray($pagePath->getPage()->getId());
            $pageContents     = $pageArray["paths"][$language]["pageContent"];

            $data = $pageBuilderService->getContentDataArray($pageContents);

            return $data;
        }
        return null;
    }
}

