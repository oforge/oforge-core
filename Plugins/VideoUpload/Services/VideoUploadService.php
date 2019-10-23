<?php

namespace VideoUpload\Services;

use VideoUpload\Models\VideoKey;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Doctrine\ORM\ORMException;

class VideoUploadService extends AbstractDatabaseAccess
{
    public function __construct()
    {
        parent::__construct([
            'videoKey' => videoKey::class,
        ]);
    }

    public function getVideoKey($insertionId) {
        $repository = $this->repository('videoKey');
        $videoKey = $repository->findOneBy(['insertionId' => $insertionId]);
        return $videoKey;
    }
}
