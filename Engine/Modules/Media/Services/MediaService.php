<?php

namespace Oforge\Engine\Modules\Media\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Media\Models\Media;

class MediaService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => Media::class]);
    }

    public function add($file) : ?Media {
        if (isset($file["error"]) && $file["error"] == 0 && isset($file["size"]) && $file["size"] > 0) {

            $relative = Statics::IMAGES_DIR . DIRECTORY_SEPARATOR . substr(md5(rand()), 0, 2) . DIRECTORY_SEPARATOR . substr(md5(rand()), 0, 2).  DIRECTORY_SEPARATOR . basename($file['name']);

            @mkdir(dirname(ROOT_PATH . $relative), 0777, true);

            if (move_uploaded_file($file['tmp_name'], ROOT_PATH . $relative)) {

                $media = Media::create(["type" => $file["type"], "name" =>  basename($file['name']), "path" => $relative ]);
                $this->entityManager()->create($media);

                return $media;
            }
        }

        return null;
    }

    public function get(int $id) : ?Media {
        /**
         * @var $result Oforge\Engine\Modules\Media\Models\Media
         */
        $result = $this->repository()->findOneBy(["id" => $id]);;

        return $result;
    }

    public function getByPath(string $path) : ?Media{
        /**
         * @var $result Oforge\Engine\Modules\Media\Models\Media
         */
        $result = $this->repository()->findOneBy(["path" => $path]);;

        return $result;
    }
}
