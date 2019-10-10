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

    /**
     * @param int $insertionId
     * @param string $key
     * @throws ORMException
     */
    public function updateOrCreateVideoKey($insertionId, $key)
    {
        $repository = $this->repository('videoKey');
        $videoKey = $repository->findOneBy(['insertionId' => $insertionId]);
        if ($videoKey === null) {
            $videoKey = new VideoKey();
            $videoKey->setVideoKey($key);
            $videoKey->setInsertionId($insertionId);
            $this->entityManager()->create($videoKey);
        } else {
            $videoKey->setVideoKey($key);
            $this->entityManager()->update($videoKey);
        }
    }

    public function getVideoKey($insertionId) {
        $repository = $this->repository('videoKey');
        $videoKey = $repository->findOneBy(['insertionId' => $insertionId]);
        return $videoKey;
    }
}
