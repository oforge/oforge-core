<?php

namespace Seo\Services;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Seo\Models\SeoUrl;

class SeoService extends AbstractDatabaseAccess {
    /**
     * MailchimpNewsletterService constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        parent::__construct(['default' => SeoUrl::class]);
    }

    public function get($targetUrl) {
        $split = explode("?", $targetUrl);
        return $this->repository()->findOneBy(["target" => $split[0]]);
    }

    public function getBySource($sourceUrl) {
        $split = explode("?", $sourceUrl);
        return $this->repository()->findOneBy(["source" => $split[0]]);
    }

    /**
     * Get an array of SeoUrls with id as key
     *
     * @throws ORMException
     */
    public function getAllSeoUrls() {
        /** @var SeoUrl[] $seoUrls */
        $seoUrls = $this->repository()->findAll();
        $result = [];
        foreach($seoUrls as $seoUrl) {
            $result[$seoUrl->getId()] = $seoUrl->getTarget();
        }
        return $result;
    }
}
