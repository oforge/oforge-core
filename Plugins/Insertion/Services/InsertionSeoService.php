<?php

namespace Insertion\Services;

use Doctrine\ORM\ORMException;
use Insertion\Models\InsertionSeoContent;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class InsertionSeoService extends AbstractDatabaseAccess
{
    public function __construct()
    {
        parent::__construct([
            'default' => InsertionSeoContent::class,
        ]);
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws ORMException
     */
    public function getContentForUrl(int $id) {
        /** @var InsertionSeoContent $seoContent */
        $seoContent = $this->repository()->findOneBy(['seoTargetUrl' => $id]);
        if (isset($seoContent)) {
            $scontentElements = explode(',', $seoContent->getContentElements());
            $seoContentArray = $seoContent->toArray();
            unset($seoContentArray['seoTargetUrl']);
            $seoContentArray['contentElements'] = $scontentElements;
        }
        return $seoContentArray;
    }
}
