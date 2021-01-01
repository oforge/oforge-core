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

    public function getYoutubeVideos($insertionId) {
        $repository = $this->repository('youtube');
        $videoKey   = $repository->findBy(['insertionId' => $insertionId]);

        return $videoKey;
    }

    public function getInseration($insertionId) {
        $repository = $this->repository('insertion');
        $insertion  = $repository->findOneBy(['id' => $insertionId]);

        return $insertion;
    }

    public function processChange($id, $videoDataRaw) {
        try {
            /**
             * @var $insertion Insertion
             */
            $insertion = $this->getInseration($id);
            //insertion available ??
            if ($insertion != null) {
                $videos = $this->getYoutubeVideos($id);

                $delete = [];
                $insert = [];

                //clean duplicates
                $i         = 0;
                $j         = 0;
                $videoData = [];

                foreach ($videoDataRaw as $video) {
                    $found = false;
                    $j     = 0;
                    foreach ($videoDataRaw as $newVideo) {
                        if ($i != $j && $newVideo["videoKey"] == $video["videoKey"]) {
                            if ($i > $j) {
                                $found = true;
                                break;
                            }
                        }

                        $j++;
                    }
                    $i++;

                    if (!$found) {
                        $videoData[] = $video;
                    }
                }


                //found duplicates
                $i = 0;
                foreach ($videos as $video) {
                    $found = false;
                    $j     = 0;
                    foreach ($videos as $newVideo) {
                        if ($i != $j && $newVideo->getVideoKey() == $video->getVideoKey()) {
                            if ($i > $j) {
                                $found = true;
                                break;
                            }
                        }
                        $j++;
                    }
                    $i++;
                    if ($found) {
                        $delete[$video->getId()] = $video;
                    }
                }

                //found old keys and existing
                foreach ($videos as $video) {
                    $found = false;
                    foreach ($videoData as $newVideo) {
                        if ($newVideo["videoKey"] == $video->getVideoKey()) {
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        $delete[$video->getId()] = $video;
                    }
                }

                //found new keys
                foreach ($videoData as $newVideo) {
                    $found = false;

                    foreach ($videos as $video) {
                        if ($newVideo["videoKey"] == $video->getVideoKey()) {
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        $insert[] = $newVideo;
                    }
                }

                //clear old youtube videos from insertion

                //find insertion media object
                $deleteMedia        = [];
                $deleteImediaObject = [];
                foreach ($insertion->getMedia() as $imedia) {
                    if ($imedia->getContent() != null) {
                        $mediaContent = $imedia->getContent();
                        if ($mediaContent->getType() == "video/youtube") {
                            $deleteMedia[]        = $mediaContent;
                            $deleteImediaObject[] = $imedia;
                        }
                    }
                }

                //remove insertion media from array
                foreach ($deleteImediaObject as $imediaObject) {
                    $insertion->getMedia()->removeElement($imediaObject);
                }

                //update insertion object
                if (sizeof($deleteImediaObject)) {
                    $this->entityManager()->update($insertion, true);
                }

                //remove insertion media object
                foreach ($deleteImediaObject as $imediaObject) {
                    $this->entityManager()->remove($imediaObject);
                }

                //remove media object
                foreach ($deleteMedia as $mediaObject) {
                    $this->entityManager()->remove($mediaObject);
                }

                //remove youtube object
                foreach ($delete as $youtubeObject) {
                    $this->entityManager()->remove($youtubeObject);
                }



                //insert new youtube objects
                foreach ($insert as $newVideo) {
                    $videoMedia = YoutubeVideo::create([
                        "thumbnail"   => $newVideo["thumbnail"],
                        "videoKey"    => $newVideo["videoKey"],
                        "insertionId" => $insertion->getId(),
                    ]);

                    $this->entityManager()->create($videoMedia);
                }

                foreach ($videoData as $newVideo) {
                    $videoMedia = Media::create([
                        "name" => $newVideo["videoKey"],
                        "type" => "video/youtube",
                        "path" => $newVideo["videoKey"],
                    ]);
                    $this->entityManager()->create($videoMedia);

                    $imedia = InsertionMedia::create(["name" => $newVideo["videoKey"], "content" => $videoMedia, "main" => 0]);
                    $imedia->setInsertion($insertion);
                    $this->entityManager()->create($imedia);
                }

            }
        } catch (\Exception $e) {
            Oforge()->Logger()->get()->error("youtube", ["exception" => $e]);
        }
    }

    public function resolveVideoData($videoID) {
        $url = 'https://www.youtube.com/oembed?format=json&url=http://www.youtube.com/watch?v=' . $videoID;

        $data = json_decode(file_get_contents($url), true);
        if ($data != null) {
            return $data;
        }

        return null;
    }
}
