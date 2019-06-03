<?php

namespace Oforge\Engine\Modules\Media\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Helper\FileSystemHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Media\Models\Media;

/**
 * Class MediaService
 *
 * @package Oforge\Engine\Modules\Media\Services
 */
class MediaService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(Media::class);
    }

    /**
     * @param $file
     *
     * @return Media|null
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add($file) : ?Media {
        if (isset($file['error']) && $file['error'] == 0 && isset($file['size']) && $file['size'] > 0) {
            $filename         = basename($file['name']);
            $relativeFilePath = Statics::IMAGES_DIR . DIRECTORY_SEPARATOR . substr(md5(rand()), 0, 2) . DIRECTORY_SEPARATOR . substr(md5(rand()), 0, 2)
                                . DIRECTORY_SEPARATOR . $filename;

            FileSystemHelper::mkdir(dirname(ROOT_PATH . $relativeFilePath));

            if (move_uploaded_file($file['tmp_name'], ROOT_PATH . $relativeFilePath)) {
                $media = Media::create([
                    'type' => $file['type'],
                    'name' => $filename,
                    'path' => $relativeFilePath,
                ]);
                $this->entityManager()->persist($media);
                $this->entityManager()->flush();

                return $media;
            }
        }

        return null;
    }

    /**
     * @param int $id
     *
     * @return Media|null
     * @throws ORMException
     */
    public function getById(int $id) : ?Media {
        /** @var Media|null $result */
        $result = $this->repository()->findOneBy([
            'id' => $id,
        ]);;

        return $result;
    }

    /**
     * @param string $path
     *
     * @return Media|null
     * @throws ORMException
     */
    public function getByPath(string $path) : ?Media {
        /** @var Media|null $result */
        $result = $this->repository()->findOneBy([
            'path' => $path,
        ]);;

        return $result;
    }

}
