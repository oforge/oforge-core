<?php

namespace Oforge\Engine\Modules\Media\Services;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\FileSystemHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
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
     * @param null $owner
     *
     * @return Media|null
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function add($file, $prefix = null, $owner = null) : ?Media {
        if (isset($file['error']) && $file['error'] == 0 && isset($file['size']) && $file['size'] > 0) {
            $filename = $this->normalizeFilename(basename($file['name']));
            if ($prefix !== null) {
                $filename = strtolower($prefix) . '_' . $filename;
            }

            $relativeFilePath = $this->createRelativeFilepath($filename);
            $absoluteFilePath = ROOT_PATH . $relativeFilePath;

            FileSystemHelper::mkdir(dirname($absoluteFilePath));

            if (move_uploaded_file($file['tmp_name'], ROOT_PATH . $relativeFilePath)) {
                if (extension_loaded('gd')) {
                    $size = getimagesize($absoluteFilePath);
                }

                $media = Media::create([
                    'type'  => strtolower($file['type']),
                    'name'  => urlencode($filename),
                    'path'  => str_replace('\\', '/', $relativeFilePath),
                    'owner' => $owner,
                ]);
                $this->entityManager()->create($media);
                $this->addMediaPostProcess($media);

                return $media;
            }
        }

        return null;
    }

    public function delete($id) {
        /*
        TODO: we should be able to delete medias. Possible solution: create a reference table <media_id, full_class_name_and_function of usage>, count usage amount. if this table is empty then usage amount is 0 and file is deletable.
        */
    }

    /**
     * @param $id
     *
     * @return Media|null
     */
    public function getById($id) : ?Media {
        return $this->getOneBy([
            'id' => $id,
        ]);
    }

    /**
     * Find media by full path including the filename
     *
     * @param string $path
     *
     * @return Media|null
     */
    public function getByPath(string $path) : ?Media {
        return $this->getOneBy([
            'path' => $path,
        ]);
    }

    /**
     * Search for medias
     *
     * @param string $query
     * @param int $page
     * @param int $pageSize
     *
     * @return array
     * @throws ORMException
     */
    public function search(string $query, int $page = 1, int $pageSize = 15) : array {
        $queryBuilder = $this->repository()->createQueryBuilder('m')->where('m.name LIKE :name')->orderBy('m.id', 'desc')
                             ->setParameter('name', '%' . $query . '%');

        $query     = $queryBuilder->getQuery()->setFirstResult(($page - 1) * $pageSize)->setMaxResults($pageSize);
        $paginator = new Paginator($query, $fetchJoinCollection = true);

        $result['query']['count']     = $paginator->count();
        $result['query']['pageSize']  = $pageSize;
        $result['query']['page']      = $page;
        $result['query']['pageCount'] = ceil((1.0) * $paginator->count() / $pageSize);
        $result['query']['items']     = [];

        foreach ($paginator as $item) {
            $result['query']['items'][] = $item->toArray();
        }

        return $result;
    }

    /**
     * Read the images folder and delete all thumbnail files.
     *
     * @throws ORMException
     */
    public function deleteThumbnails() {
        $path = glob(ROOT_PATH . Statics::IMAGES_DIR . '/*/*/*.*[jpg|jpeg|png|gif|svg|webp]*');
        /** @var Media[] $medias */
        $medias = $this->repository()->findAll();
        foreach ($medias as $media) {
            $searchIndex = array_search(ROOT_PATH . $media->getPath(), $path);
            if ($searchIndex !== false) {
                unset($path[$searchIndex]);
            }
        }
        foreach ($path as $file) {
            @unlink($file);
        }
    }

    public function download($photoURL, $filename, $type) {
        $filename         = $this->normalizeFilename($filename);
        $relativeFilePath = $this->createRelativeFilepath($filename);
        $absoluteFilePath = ROOT_PATH . $relativeFilePath;

        FileSystemHelper::mkdir(dirname($absoluteFilePath));
        file_put_contents($absoluteFilePath, file_get_contents($photoURL));
        if (extension_loaded('gd')) {
            $size = getimagesize($absoluteFilePath);
        }

        $media = Media::create([
            'type' => strtolower($type),
            'name' => urlencode($filename),
            'path' => str_replace('\\', '/', $relativeFilePath),
        ]);

        $this->entityManager()->create($media);
        $this->addMediaPostProcess($media);

        return $media;
    }

    /**
     * @param array $criteria
     *
     * @return Media|null
     */
    protected function getOneBy(array $criteria) : ?Media {
        /** @var Media|null $result */
        $result = $this->repository()->findOneBy($criteria);

        return $result;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    protected function normalizeFilename(string $filename) : string {
        $filenameData = pathinfo(strtolower($filename));
        $extension    = $filenameData['extension'];
        $filename     = $filenameData['filename'];
        $filename     = strtr($filename, [
            'ä' => 'ae',
            'ö' => 'oe',
            'ü' => 'ue',
            'ß' => 'ss',
        ]);
        $filename     = preg_replace("/[^a-z0-9]/", '_', $filename);
        $filename     = preg_replace("/_+/", '_', $filename);
        $filename     .= '.' . $extension;

        return $filename;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    protected function createRelativeFilepath(string $filename) : string {
        return Statics::IMAGES_DIR . Statics::GLOBAL_SEPARATOR . substr(md5(rand()), 0, 2) . Statics::GLOBAL_SEPARATOR . substr(md5(rand()), 0, 2)
               . Statics::GLOBAL_SEPARATOR . $filename;
    }

    /**
     * @param Media $media
     *
     * @throws ServiceNotFoundException
     */
    protected function addMediaPostProcess(Media $media) {
        if (StringHelper::startsWith($media->getType(), 'image')) {
            /** @var ImageService $imageService */
            $imageService = Oforge()->Services()->get('media.image');
            $imageService->getUrlByMedia($media, 400);
            $imageService->getUrlByMedia($media, 300);
        }
    }

}
