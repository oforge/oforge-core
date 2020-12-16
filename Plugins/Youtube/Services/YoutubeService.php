<?php

namespace Youtube\Services;

use Insertion\Models\Insertion;
use Insertion\Models\InsertionMedia;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Media\Models\Media;
use Oforge\Engine\Modules\Media\Services\MediaService;
use Youtube\Models\YoutubeVideo;

class YoutubeService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'youtube'         => YoutubeVideo::class,
            'insertion'       => Insertion::class,
            'media'           => Media::class,
            'inserationmedia' => InsertionMedia::class,
        ]);
    }

    public function getYoutubeVideo($insertionId) {
        $repository = $this->repository('youtube');
        $videoKey   = $repository->findOneBy(['insertionId' => $insertionId]);

        return $videoKey;
    }

    public function getInseration($insertionId) {
        $repository = $this->repository('insertion');
        $insertion  = $repository->findOneBy(['id' => $insertionId]);

        return $insertion;
    }

    public function processChange($id, $videoKey) {
        try {
            /**
             * @var $insertion Insertion
             */
            $insertion = $this->getInseration($id);
            //insertion available ??
            if ($insertion != null) {
                $video = $this->getYoutubeVideo($id);

                if ($video != null) { //video for inseration available -> update
                    if (false && $videoKey == $video->getVideoKey()) { // same key -> nothing to do
                        //nothing to do
                    } else {
                        //find insertion media object
                        $media        = null;
                        $imediaObject = null;
                        foreach ($insertion->getMedia() as $imedia) {
                            if ($imedia->getContent() != null) {
                                $mediaContent = $imedia->getContent();
                                if ($mediaContent->getType() == "video/youtube") {
                                    $media        = $mediaContent;
                                    $imediaObject = $imedia;
                                    break;
                                }
                            }
                        }

                        if (!empty($videoKey)) { //video key set -> update
                            // set new key
                            $video->setVideoKey($videoKey);
                            $this->entityManager()->update($video);

                            if ($media != null) { //media object found -> update
                                $media->setName($videoKey);
                                $media->setPath($videoKey);
                                $this->entityManager()->update($media, true);
                            } else { //media object not found -> create

                                /** @var MediaService $mediaService */
                                $videoMedia = Media::create([
                                    "name" => $videoKey,
                                    "type" => "video/youtube",
                                    "path" => $videoKey,
                                ]);
                                $this->entityManager()->create($videoMedia, true);

                                $imedia = InsertionMedia::create(["name" => $videoKey, "content" => $videoMedia, "main" => 0]);
                                $imedia->setInsertion($insertion);
                                $this->entityManager()->create($imedia, true);
                            }
                        } else { //video key empty -> delete
                            if ($imediaObject != null) { //media object found -> delete
                                $insertion->getMedia()->removeElement($imediaObject);
                                $this->entityManager()->update($insertion, true);
                                $this->entityManager()->remove($imediaObject);

                                $this->entityManager()->remove($video);


                                Oforge()->Logger()->get()->error("youtube found -> delete");
                            }
                        }

                    }
                } else { //video not available -> create
                    if (!empty($videoKey)) { // key is set -> create

                        // create video entry
                        $video = YoutubeVideo::create(["insertionId" => $id, "videoKey" => $videoKey]);
                        $this->entityManager()->create($video);

                        //create media entry
                        /** @var MediaService $mediaService */
                        $videoMedia = Media::create([
                            "name" => $videoKey,
                            "type" => "video/youtube",
                            "path" => $videoKey,
                        ]);

                        //create insertion media entry
                        $this->entityManager()->create($videoMedia);
                        $imedia = InsertionMedia::create(["name" => $videoKey, "content" => $videoMedia, "main" => 0]);
                        $imedia->setInsertion($insertion);

                        $this->entityManager()->create($imedia, true);

                        Oforge()->Logger()->get()->error("youtube not found -> create");
                    }
                }
            }
        } catch (\Exception $e) {
            Oforge()->Logger()->get()->error("youtube", [ "exception" => $e]);
        }
    }

    public function resolveVideoData($videoID) {
        $url = 'https://www.youtube.com/oembed?format=json&url=http://www.youtube.com/watch?v=' . $videoID;

        $data = json_decode(file_get_contents($url));
        if ($data != null) {
            return $data;
        }

        return null;
    }
}
