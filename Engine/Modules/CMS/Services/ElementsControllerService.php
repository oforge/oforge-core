<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;

class ElementsControllerService extends AbstractDatabaseAccess {
    private $entityManager = NULL;
    
    public function __construct() {
        parent::__construct(["contentTypeGroup" => ContentTypeGroup::class, "contentType" => ContentType::class, "content" => Content::class]);
        
        $this->entityManager = Oforge()->DB()->getManager();
    }

    public function getElementData()
    {
        $elementTreeService = OForge()->Services()->get("element.tree.service");
        $contentTypeService = OForge()->Services()->get("content.type.service");
        
        $data = [
            "js"                => ["cms_elements_controller_jstree_config" => $elementTreeService->generateJsTreeConfigJSON()],
            "contentTypeGroups" => $contentTypeService->getContentTypeGroupArray(),
            "post"              => $post
        ];
        
        return $data;
    }
}