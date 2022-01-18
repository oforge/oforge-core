<?php

namespace Oforge\Engine\Modules\Media\Services;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\FileSystemHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
use Oforge\Engine\Modules\Core\Manager\Events\Event;
use Oforge\Engine\Modules\Media\Enums\MediaType;
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
            $filename = preg_replace("/\s+/", "_", (basename($file['name'])));
            //      $filename         = md5(basename($file['name']) . '_' . microtime()) . '.' . pathinfo($file['name'],PATHINFO_EXTENSION);
            if ($prefix !== null) {
                $filename = strtolower($prefix . '_' . $filename);
            }

            $relativeFilePath = implode(
                Statics::GLOBAL_SEPARATOR,
                [
                    Statics::IMAGES_DIR,
                    substr(md5(rand()), 0, 2),
                    substr(md5(rand()), 0, 2),
                    $filename,
                ]
            );

            FileSystemHelper::mkdir(dirname(ROOT_PATH . $relativeFilePath));

            if (move_uploaded_file($file['tmp_name'], ROOT_PATH . $relativeFilePath)) {
                $size = getimagesize(ROOT_PATH . $relativeFilePath);
                $media = Media::create([
                    'type' => $file['type'],
                    'name' => urlencode($filename),
                    'path' => str_replace('\\', '/', $relativeFilePath),
                    'owner' => $owner
                ]);
                $this->entityManager()->create($media);

                $fileType = self::detectType($file['type'] ?? null, $filename);

                file_put_contents(__DIR__.'/log.txt', "media.$fileType::created\n", FILE_APPEND);
                Oforge()->Events()->trigger(Event::create("media.$fileType::created", [
                    'media' => $media,
                ]));

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
     * Find media by full path including the filename
     *
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

    /**
     * Read the images folder and delete all thumbnail files.
     * @throws ORMException
     */
    public function deleteThumbnails() {
        $path = glob(ROOT_PATH . Statics::IMAGES_DIR . '/*/*/*.*[jpg|jpeg|png|gif|svg]*');
        /** @var Media[] $medias */
        $medias = $this->repository()->findAll();
        foreach ($medias as $media) {
            $searchIndex = array_search(ROOT_PATH . $media->getPath(), $path);
            if ($searchIndex !== false) {
                unset($path[$searchIndex]);
            }
        }
        foreach ($path as $file) {
            unlink($file);
        }
    }

    public function rotate(Media $media, int $angle) {
        /** @var ImageRotateService $imageRotateService */
        $imageRotateService = Oforge()->Services()->get('image.rotate');

        $imageRotateService->rotate($media, $angle);
    }

    public function download($photoURL, $filename, $type) {
        $relativeFilePath = Statics::IMAGES_DIR . Statics::GLOBAL_SEPARATOR . substr(md5(rand()), 0, 2) . Statics::GLOBAL_SEPARATOR . substr(md5(rand()), 0, 2)
                            . Statics::GLOBAL_SEPARATOR . $filename;

        FileSystemHelper::mkdir(dirname(ROOT_PATH . $relativeFilePath));

        file_put_contents(ROOT_PATH.$relativeFilePath, file_get_contents($photoURL));

        $media = Media::create([
            'type' => $type,
            'name' => urlencode($filename),
            'path' => str_replace('\\', '/', $relativeFilePath),
        ]);

        $this->entityManager()->create($media);

        $fileType = self::detectType(null, $filename);

        file_put_contents(__DIR__.'/log.txt', "media.$fileType::created\n", FILE_APPEND);
        Oforge()->Events()->trigger(Event::create("media.$fileType::created", [
            'media' => $media,
        ]));

        return $media;
    }

    public static function detectType(?string $mimeType, string $filename) : string
    {
        if ($mimeType !== null) {
            if (StringHelper::startsWith($mimeType, 'image/')) {
                return MediaType::IMAGE;
            }
            if (StringHelper::startsWith($mimeType, 'video/')) {
                return MediaType::VIDEO;
            }
        }
        if ( !empty($filename)) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            if (in_array($extension, ['jpg', 'jpeg', 'gif', /*'svg', */ 'png', 'bmp'])) {
                return MediaType::IMAGE;
            }
            if (in_array($extension, ['mpeg', 'mpg', 'mp4', 'ogg', 'ogv', 'webm', 'avi'])) {
                return MediaType::VIDEO;
            }
        }

        return MediaType::BINARY;
    }

}
