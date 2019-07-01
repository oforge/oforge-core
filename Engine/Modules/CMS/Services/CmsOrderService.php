<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\CMS\Models\Page\PageContent;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class CmsOrderService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct(['content' => Content::class, 'pagepath' => PagePath::class, 'pagecontent' => PageContent::class]);
    }

    public function order($data) {
        if (isset($data["element"]) && !empty($data["element"])) {
            /**
             * @var $content Content
             */
            $content = $this->repository("content")->find($data["element"]);
            if ($content) {
                $classname = $content->getType()->getClassPath();

                /**
                 * @var $instance AbstractContentType
                 */
                $instance = new $classname();
                $instance->load($data["element"]);
                $instance->setOrder($data["order"]);

                return true;
            }

        } elseif (isset($data["page"]) && !empty($data["page"]) && isset($data["language"]) && !empty($data["language"])) {
            /**
             * @var $pagePath PagePath
             */
            $pagePath = $this->repository("pagepath")->findOneBy(["page" => $data["page"], "language" => $data["language"]]);
            if ($pagePath != null) {
                $map = [];
                foreach ($data["order"] as $order) {
                    $map[$order["id"]] = $order;
                }

                /**
                 * @var $pageContent PageContent
                 */
                foreach ($pagePath->getPageContent() as $pageContent) {
                    if (isset($map[$pageContent->getContent()->getId()])) {
                        $pageContent->setOrder($map[$pageContent->getContent()->getId()]["order"]);
                        $this->entityManager()->update($pageContent);
                    }
                }

                return true;
            }
        }

        return false;
    }
}