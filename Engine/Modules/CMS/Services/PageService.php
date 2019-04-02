<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Page\PageContent;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

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
     * @throws \Doctrine\ORM\ORMException
     */
    public function hasPath(string $path) {
        $data = $this->repository()->findOneBy(["path" => $path]);

        return isset($data);
    }

    /**
     * @param string $path
     *
     * @return PagePath|object|null
     * @throws \Doctrine\ORM\ORMException
     */
    private function getPagePath(string $path) : ?PagePath {
        return $this->repository()->findOneBy(["path" => $path]);
    }

    /**
     * @param PagePath $pagePath
     *
     * @return array
     * @internal param string $path
     */
    public function normalize(PagePath $pagePath) : array {
        $result         = [];
        $result["meta"] = [
            "language" => $pagePath->getLanguage()->getIso(),
            "route"    => ["actual" => $pagePath->getPath()],
            "page"     => [
                "name" => $pagePath->getPage()->getName(),
                "id"   => $pagePath->getPage()->getId(),
            ],
        ];

        $result["content"] = [];

        foreach ($pagePath->getPageContent() as $pageContent) {
            $content = $pageContent->getContent();
            if (isset($content)) {
                //todo call load method from content_type
                array_push($result["content"], [
                    "type" => $content->getType()->getName(),
                    "path" => $content->getType()->getPath(),
                    "data" => $content->getData(),
                ]);
            }
        }

        return $result;
    }

    /**
     * @param string $path
     *
     * @return array | null
     * @throws \Doctrine\ORM\ORMException
     */
    public function loadContentForPagePath($path) {

        $pagePath = $this->getPagePath($path);

        if (isset($pagePath)) {
            $pageContents = $pagePath->getPageContent()->getValues();
            $result["content"] = [];

            foreach ($pageContents as $pageContent) {
                /** @var PageContent $pageContent */
                /** @var Content $content */
                /** @var ContentType $contentType */
                $content = $pageContent->getContent();
                $contentType = $content->getType();
                $data = $content->getData();

                if (!$data) {
                    /** @var AbstractContentType $classPath */
                    $classPath = $contentType->getClassPath();
                    $data = (new $classPath)->load($content->getId());
                }

                array_push($result['content'], [
                    "type" => $contentType->getName(),
                    "path" => $contentType->getPath(),
                    "data" => $data,
                ]);
            }

            return $result;
        }
        return null;
    }
}

