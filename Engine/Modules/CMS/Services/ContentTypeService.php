<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 17.01.2019
 * Time: 10:52
 */

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class ContentTypeService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            "default" => ContentTypeGroup::class,
        ]);
    }

    /**
     * Returns all available content type group entities
     *
     * @return ContentTypeGroup[]|NULL
     */
    public function getContentTypeGroupEntities() {
        $contentTypeGroups = $this->repository()->findAll();

        if (isset($contentTypeGroups)) {
            return $contentTypeGroups;
        } else {
            return null;
        }
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
            $contentTypeGroup                = [];
            $contentTypeGroup["id"]          = $contentTypeGroupEntity->getId();
            $contentTypeGroup["description"] = $contentTypeGroupEntity->getDescription();

            $contentTypeEntities = $contentTypeGroupEntity->getContentTypes();

            $contentTypeArray = [];
            foreach ($contentTypeEntities as $contentTypeEntity) {
                $contentType                = [];
                $contentType["id"]          = $contentTypeEntity->getId();
                $contentType["group"]       = $contentTypeEntity->getGroup();
                $contentType["name"]        = $contentTypeEntity->getName();
                $contentType["icon"]        = $contentTypeEntity->getIcon();
                $contentType["description"] = $contentTypeEntity->getDescription();
                $contentType["class"]       = $contentTypeEntity->getClassPath();

                $contentTypeArray[] = $contentType;
            }

            $contentTypeGroup["types"] = $contentTypeArray;

            $contentTypeGroups[] = $contentTypeGroup;
        }

        return $contentTypeGroups;
    }
}