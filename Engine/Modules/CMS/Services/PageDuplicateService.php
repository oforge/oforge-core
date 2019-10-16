<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Layout\Layout;
use Oforge\Engine\Modules\CMS\Models\Page\Page;
use Oforge\Engine\Modules\CMS\Models\Page\PageContent;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\CMS\Models\Site\Site;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\I18n\Models\Language;

/**
 * Class PageDuplicateService
 *
 * @package Oforge\Engine\Modules\CMS\Services
 */
class PageDuplicateService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([
            'language'    => Language::class,
            'layout'      => Layout::class,
            'site'        => Site::class,
            'page'        => Page::class,
            'pagePath'    => PagePath::class,
            'pageContent' => PageContent::class,
            'contentType' => ContentType::class,
            'content'     => Content::class,
        ]);
    }

    /**
     * @param string|int $pageID
     *
     * @return false|array
     */
    public function duplicate($pageID) {
        try {
            /** @var Page|null $srcPage */
            $srcPage = $this->repository('page')->find($pageID);
        } catch (ORMException $exception) {
            Oforge()->Logger()->logException($exception);
        }
        if (isset($srcPage)) {
            try {
                $this->entityManager()->getEntityManager()->beginTransaction();
                $srcPageData = $srcPage->toArray(0, ['id', 'paths']);

                $dstName = $srcPage->getName() . ' - ' . I18N::translate('copy', ['en' => 'copy', 'de' => 'Kopie']);

                $queryBuilder = $this->repository('page')->createQueryBuilder('p');
                $results      = $queryBuilder->select('p.name')#
                                             ->where($queryBuilder->expr()->like('p.name', "'" . $dstName . "%'"))#
                                             ->getQuery()->getScalarResult();
                if (!empty($results)) {
                    $results = array_map(function ($entry) {
                        return $entry['name'];
                    }, $results);
                    for ($i = 2; $i < 100; $i++) {
                        $dstName2 = $dstName . ' ' . $i;
                        if (!in_array($dstName2, $results)) {
                            $dstName = $dstName2;
                            break;
                        }
                    }
                }
                $dstPage = Page::create($srcPageData);
                $dstPage->setName($dstName);
                $this->entityManager()->create($dstPage);
                $this->duplicatePagePaths($srcPage, $dstPage);
                $this->entityManager()->getEntityManager()->commit();
                $newPageID   = $dstPage->getId();
                $newPageName = $dstPage->getName();

                return [
                    'pageID'   => $newPageID,
                    'pageName' => $newPageName,
                ];
            } catch (\Exception $exception) {
                Oforge()->Logger()->logException($exception);
                $this->entityManager()->getEntityManager()->rollback();
            }
        }

        return false;
    }

    /**
     * @param Page $srcPage
     * @param Page $dstPage
     *
     * @throws ORMException
     */
    private function duplicatePagePaths(Page $srcPage, Page $dstPage) {
        /** @var PagePath[] $srcPagePaths */
        $srcPagePaths = $srcPage->getPaths();
        foreach ($srcPagePaths as $srcPagePath) {
            $dstPagePathData = $srcPagePath->toArray(0, ['id', 'page', 'pageContent']);
            // echo "PagePath:";
            // o_print($dstPagePathData);
            $dstPagePath = PagePath::create($dstPagePathData);
            $dstPagePath->setPage($dstPage);
            $this->entityManager()->create($dstPagePath);
            $this->duplicatePageContents($srcPagePath, $dstPagePath);
        }
    }

    /**
     * @param PagePath $srcPagePath
     * @param PagePath $dstPagePath
     *
     * @throws ORMException
     */
    private function duplicatePageContents(PagePath $srcPagePath, PagePath $dstPagePath) {
        /** @var PageContent[] $srcPageContents */
        $srcPageContents = $srcPagePath->getPageContent();
        foreach ($srcPageContents as $srcPageContent) {
            $srcPageContentData = $srcPageContent->toArray(0, ['id', 'pagePath']);
            // echo "PageContent:";
            // o_print($srcPageContentData);
            /** @var Content $srcContent */
            $srcContent = $srcPageContent->getContent();
            /** @var AbstractContentType $srcContentType */
            $srcContentTypeClass = $srcContent->getType()->getClassPath();
            $srcContentType      = new $srcContentTypeClass();
            $srcContentType->load($srcContent);
            $dstContent = $srcContentType->duplicate();
            if ($dstContent === null) {
                continue;
            }

            $dstPageContent = PageContent::create($srcPageContentData);
            $dstPageContent->setPagePath($dstPagePath);

            $dstPageContent->setContent($dstContent);
            $this->entityManager()->create($dstPageContent);
        }
    }

}
