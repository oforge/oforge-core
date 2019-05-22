<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CMS\Models\Content\Content;

class NamedContentService extends AbstractDatabaseAccess
{
    public function __construct()
    {
        parent::__construct(['default' => Content::class]);
    }


    public function getContent($name): ?array {
        /**
         * @var $content Content
         */
        $content = $this->repository()->findOneBy(["name" => $name]);
        $result = [];

        if(isset($content)) {
            /**
             * @var $contentTypeService ContentTypeService
             */
            $contentTypeService = Oforge()->Services()->get("content.type.service");
            $result = $contentTypeService->getContentDataArray($content->getId(), $content->getType()->getId());
        }

        return $result;
    }
}
