<?php

namespace Oforge\Engine\Modules\Import\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Helper\FileSystemHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Media\Models\Media;

/**
 * Class ImportMediaService
 *
 * @package Oforge\Engine\Modules\Import\Services
 */
class ImportMediaService {

    public function process() {
        $entityManager = Oforge()->DB()->getForgeEntityManager();
        $importDir     = ROOT_PATH . Statics::IMPORTS_DIR . '/media/';
        FileSystemHelper::mkdir($importDir);
        $filenames = scandir($importDir);
        $fileInfo  = finfo_open(FILEINFO_MIME_TYPE);
        foreach ($filenames as $filename) {
            $filepath = $importDir . $filename;
            if ($filename === '.' || $filename === '..' || is_dir($filepath)) {
                continue;
            }
            $fileMime = finfo_file($fileInfo, $filename);
            if ($fileMime === false) {
                continue;
            }
            $relativeFilePath = Statics::IMAGES_DIR . DIRECTORY_SEPARATOR . substr(md5(rand()), 0, 2) . DIRECTORY_SEPARATOR . substr(md5(rand()), 0, 2)
                                . DIRECTORY_SEPARATOR . $filename;
            FileSystemHelper::mkdir(dirname(ROOT_PATH . $relativeFilePath));

            if (rename($filepath, ROOT_PATH . $relativeFilePath)) {
                $media = Media::create([
                    'type' => $fileMime,
                    'name' => $filename,
                    'path' => str_replace('\\', '/', $relativeFilePath),
                ]);
                try {
                    $entityManager->create($media);
                } catch (ORMException $e) {
                }
            } else {
                Oforge()->Logger()->get()->error('Could not import media file: ' . $filepath);
            }
        }
    }

}
