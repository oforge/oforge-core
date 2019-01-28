<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 17.01.2019
 * Time: 10:52
 */

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\CMS\Models\Page\PagePath;


class PageService
{

    private $entityManager;
    private $repository;

    public function __construct()
    {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->repository = $this->entityManager->getRepository(PagePath::class);
    }

    /**
     * Check if there is a cms url path
     * @param string $path
     *
     * @return bool
     */
    public function hasPath(string $path)
    {
        $data = $this->repository->findOneBy(["path" => $path]);
        return isset($data);
    }

    /**
     * @param string $path
     *
     * @return PagePath|null
     */
    public function getPage(string $path): ?PagePath
    {
        return $this->repository->findOneBy(["path" => $path]);
    }

    /**
     * @param PagePath $pagePath
     * @return array
     * @internal param string $path
     *
     */
    public function normalize(PagePath $pagePath): array
    {
        $result = [];
        $result["meta"] = [
            "language" => $pagePath->getLanguage()->getIso(),
            "route" => ["actual" => $pagePath->getPath()],
            "page" => [
                "name" => $pagePath->getPage()->getName(),
                "id" => $pagePath->getPage()->getId()
            ]
        ];

        $result["content"] = [];

        foreach ($pagePath->getPageContent() as $pageContent) {
            $content = $pageContent->getContent();
            if (isset($content)) {
                //todo call load method from content_type
                array_push($result["content"], ["type" => $content->getType()->getName(), "data" => $content->getData()]);
            }
        }
        return $result;
    }
}