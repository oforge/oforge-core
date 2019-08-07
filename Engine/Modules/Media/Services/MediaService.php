<?php

namespace Oforge\Engine\Modules\Media\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
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
     * add a file to the filesystem and to the database
     *
     * @param $file
     * @param $prefix
     *
     * @return Media|null
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function add($file, $prefix = null) : ?Media {
        if (isset($file['error']) && $file['error'] == 0 && isset($file['size']) && $file['size'] > 0) {
            $filename = md5(basename($file['name']) . '_' . microtime()) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            if ($prefix !== null) {
                $filename = strtolower($prefix . '_' . $filename);
            }
            $relativeFilePath = Statics::IMAGES_DIR . DIRECTORY_SEPARATOR . substr(md5(rand()), 0, 2) . DIRECTORY_SEPARATOR . substr(md5(rand()), 0, 2)
                                . DIRECTORY_SEPARATOR . $filename;

            FileSystemHelper::mkdir(dirname(ROOT_PATH . $relativeFilePath));

            if (move_uploaded_file($file['tmp_name'], ROOT_PATH . $relativeFilePath)) {
                /** @var ImageCompressService $imageCompressService */
                $imageCompressService = Oforge()->Services()->get('image.compress');
                $size                 = getimagesize(ROOT_PATH . $relativeFilePath);

                $media = Media::create([
                    'type' => $file['type'],
                    'name' => $filename,
                    'path' => str_replace('\\', '/', $relativeFilePath),
                ]);

                $media = $imageCompressService->compress($media);
                $this->entityManager()->create($media);

                return $media;
            }
        }

        return null;
    }

    public function delete($id) {
        /** @var Media $target */
        $target = $this->repository()->find($id);
        if (FileSystemHelper::delete($target->getPath())) {
            $this->entityManager()->remove($target);
            return true;
        }
        return false;
    }

    /**
     * @param $id
     *
     * @return Media|null
     * @throws ORMException
     */
    public function getById($id) : ?Media {
        /** @var Media|null $result */
        $result = $this->repository()->findOneBy([
            'id' => $id,
        ]);

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
        ]);

        return $result;
    }

    public function search(string $query, int $page = 1, int $pageSize = 15) : array {
        $queryBuilder = $this->repository()->createQueryBuilder('m')->where('m.name LIKE :name')->orderBy("m.id", "desc")
                             ->setParameter('name', '%' . $query . '%');

        $query     = $queryBuilder->getQuery()->setFirstResult(($page - 1) * $pageSize)->setMaxResults($pageSize);
        $paginator = new Paginator($query, $fetchJoinCollection = true);

        $result["query"]["count"]     = $paginator->count();
        $result["query"]["pageSize"]  = $pageSize;
        $result["query"]["page"]      = $page;
        $result["query"]["pageCount"] = ceil((1.0) * $paginator->count() / $pageSize);
        $result["query"]["items"]     = [];

        foreach ($paginator as $item) {
            $result["query"]["items"][] = $item->toArray();
        }

        return $result;

    }
}
