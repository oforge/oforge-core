<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\CMS\Models\Page\Page;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class PageBuilderService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => Page::class]);
    }

    /**
     * Returns all available page entities
     *
     * @return Page[]|NULL
     */
    public function getPageEntities() {
        $pageEntityArray = $this->repository()->findAll();

        if (isset($pageEntityArray)) {
            return $pageEntityArray;
        } else {
            return null;
        }
    }

    /**
     * Returns all found pages as an associative array
     *
     * @return array|NULL Array filled with available pages
     */
    public function getPageArray() {
        $pageEntities = $this->getPageEntities();

        if (!$pageEntities) {
            return null;
        }

        $pages = [];
        foreach ($pageEntities as $pageEntity) {
            $page           = [];
            $page["id"]     = $pageEntity->getId();
            $page["name"]   = $pageEntity->getName();
            $page["parent"] = $pageEntity->getParent();

            $pathEntities = $pageEntity->getPaths();

            $pathArray = [];
            foreach ($pathEntities as $pathEntity) {
                $pathArray[] = $pathEntity->getPath();
            }

            $page["paths"] = $pathArray;

            $pages[] = $page;
        }

        return $pages;
    }

    /**
     * Generate a jsTree configuration file with page data included
     *
     * @return array|NULL jsTree configuration file as PHP array
     */
    public function generateJsTreeConfigJSON() {
        $pages = $this->getPageArray();

        if (!$pages) {
            return null;
        }

        $jsTreePageArray = [];
        foreach ($pages as $page) {
            $jsTreePageArray[] = [
                "id"     => $page["id"],
                "parent" => $page["parent"] ? $page["parent"] : "#",
                "text"   => $page["name"],
            ];
        }

        $jsTreeJSON = [
            "core" => [
                "animation"      => 0,
                "check_callback" => true,
                "force_text"     => true,
                "themes"         => ["stripes" => false],
                "data"           => $jsTreePageArray,
            ],
        ];

        return $jsTreeJSON;
    }
}