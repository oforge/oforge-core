<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\CMS\Models\Content\ContentParent;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\I18n\Helper\I18N;

class ElementTreeService extends AbstractDatabaseAccess {
    private $entityManager;
    private $repository;

    public function __construct() {
        parent::__construct([
            "contentTypeGroup" => ContentTypeGroup::class,
            "contentType"      => ContentType::class,
            "contentParent"    => ContentParent::class,
            "content"          => Content::class,
        ]);
    }

    /**
     * Returns all found content type groups as an associative array
     *
     * @return array|NULL Array filled with available content type groups
     */
    public function getContentTypeGroupArray() {
        $contentTypeGroupEntities = $this->getContentTypeGroupEntities();

        if (!$contentTypeGroupEntities) {
            return null;
        }

        $contentTypeGroups = [];
        foreach ($contentTypeGroupEntities as $contentTypeGroupEntity) {
            $contentTypeGroup         = [];
            $contentTypeGroup["id"]   = $contentTypeGroupEntity->getId();
            $contentTypeGroup["name"] = $contentTypeGroupEntity->getName();

            $contentTypeGroups[] = $contentTypeGroup;
        }

        return $contentTypeGroups;
    }

    /**
     * Returns all found content types as an associative array
     *
     * @return array|NULL Array filled with available content types
     */
    public function getContentTypeArray() {
        $contentTypeEntities = $this->getContentTypeEntities();

        if (!$contentTypeEntities) {
            return null;
        }

        $contentTypes = [];
        foreach ($contentTypeEntities as $contentTypeEntity) {
            $contentType           = [];
            $contentType["id"]     = $contentTypeEntity->getId();
            $contentType["parent"] = $contentTypeEntity->getGroup()->getName();
            $contentType["name"]   = $contentTypeEntity->getName();
            $contentType["hint"]   = $contentTypeEntity->getHint();

            $contentTypes[] = $contentType;
        }

        return $contentTypes;
    }

    /**
     * Returns all found content parents as an associative array
     *
     * @return array|NULL Array filled with available content parents
     */
    public function getContentParentArray() {
        $contentParentEntities = $this->getContentParentEntities();

        if (!$contentParentEntities) {
            return null;
        }

        $contentParents = [];
        foreach ($contentParentEntities as $contentParentEntity) {
            $contentParent                = [];
            $contentParent["id"]          = $contentParentEntity->getId();
            $contentParent["parent"]      = ($contentParentEntity->getParent() && $contentParentEntity->getParent()->getId()) ? "_parent#"
                                                                                                                                . $contentParentEntity->getParent()
                                                                                                                                                      ->getId() : "#";
            $contentParent["description"] = $contentParentEntity->getDescription();

            $contentParents[] = $contentParent;
        }

        return $contentParents;
    }

    /**
     * Returns all found content elements as an associative array
     *
     * @return array|NULL Array filled with available content elements
     */
    public function getContentElementArray() {
        $contentEntities = $this->getContentEntities();

        if (!$contentEntities) {
            return null;
        }

        $contentElements = [];
        foreach ($contentEntities as $contentEntity) {
            $contentElement           = [];
            $contentElement["id"]     = $contentEntity->getId();
            $contentElement["type"]   = $contentEntity->getType()->getName();
            $contentElement["parent"] = $contentEntity->getParent() ? $contentEntity->getParent()->getId() : false;
            $contentElement["name"]   = $contentEntity->getName();

            $contentElements[] = $contentElement;
        }

        return $contentElements;
    }

    /**
     * Generate a jsTree configuration file with content element data included
     *
     * @return array|NULL jsTree configuration file as PHP array
     */
    public function generateJsTreeConfigJSON() {
        $contentTypeGroups = $this->getContentTypeGroupArray();
        $contentTypes      = $this->getContentTypeArray();
        $contentParents    = $this->getContentParentArray();
        $contentElements   = $this->getContentElementArray();

        if (!$contentTypeGroups || !$contentTypes || !$contentElements) {
            return null;
        }

        $jsTreeContentElementData = [];

        foreach ($contentTypeGroups as $contentTypeGroup) {
            $jsTreeContentElementData[] = [
                "id"     => $contentTypeGroup["name"],
                "icon"   => "jstree-folder",
                "parent" => "#",
                "text"   => I18N::translate('cms_content_type_group_label_' . $contentTypeGroup['name'], $contentTypeGroup['name']),
            ];
        }

        foreach ($contentTypes as $contentType) {
            $jsTreeContentElementData[] = [
                "id"     => $contentType["name"],
                "icon"   => "jstree-folder",
                "parent" => $contentType["parent"],
                "text"   => I18N::translate('cms_content_type_label_' . $contentType['name'], $contentType['name']),
            ];
        }

        if ($contentParents) {
            foreach ($contentParents as $contentParent) {
                $jsTreeContentElementData[] = [
                    "id"     => "_parent#" . $contentParent["id"],
                    "icon"   => "jstree-folder",
                    "parent" => $contentParent["parent"],
                    "text"   => $contentParent["description"],
                ];
            }
        }
        foreach ($contentElements as $contentElement) {
            $jsTreeContentElementData[] = [
                "id"     => "_element#" . $contentElement["id"],
                "icon"   => "jstree-file",
                "parent" => $contentElement["parent"] ? "_parent#" . $contentElement["parent"] : $contentElement["type"],
                "text"   => $contentElement["name"],
            ];
        }

        return $jsTreeContentElementData;
    }

    /**
     * Returns all available content type group entities
     *
     * @return ContentTypeGroup[]|NULL
     */
    private function getContentTypeGroupEntities() {
        $contentTypeGroupEntityArray = $this->repository("contentTypeGroup")->findAll();

        if (isset($contentTypeGroupEntityArray)) {
            return $contentTypeGroupEntityArray;
        } else {
            return null;
        }
    }

    /**
     * Returns all available content type entities
     *
     * @return ContentType[]|NULL
     */
    private function getContentTypeEntities() {
        $contentTypeEntityArray = $this->repository("contentType")->findAll();

        if (isset($contentTypeEntityArray)) {
            return $contentTypeEntityArray;
        } else {
            return null;
        }
    }

    /**
     * Returns all available content parent entities
     *
     * @return ContentParent[]|NULL
     */
    private function getContentParentEntities() {
        $contentParentEntityArray = $this->repository("contentParent")->findAll();

        if (isset($contentParentEntityArray)) {
            return $contentParentEntityArray;
        } else {
            return null;
        }
    }

    /**
     * Returns all available content entities
     *
     * @return Content[]|NULL
     */
    private function getContentEntities() {
        $contentEntityArray = $this->repository("content")->findAll();

        if (isset($contentEntityArray)) {
            return $contentEntityArray;
        } else {
            return null;
        }
    }
}
