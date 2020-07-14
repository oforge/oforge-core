<?php

namespace Insertion\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserService;
use Insertion\Models\AttributeKey;
use Insertion\Models\AttributeValue;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionAttributeValue;
use Insertion\Models\InsertionContact;
use Insertion\Models\InsertionContent;
use Insertion\Models\InsertionMedia;
use Insertion\Models\InsertionType;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\Media\Models\Media;
use Oforge\Engine\Modules\Media\Services\MediaService;

class InsertionFormsService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default'  => Insertion::class,
            'key'      => AttributeKey::class,
            'type'     => InsertionType::class,
            'language' => Language::class,
            'media'    => Media::class,
        ]);
    }

    public function processPostData($sessionKey, $hasMoreImages = false) : ?array {
        $prefix = null;
        if (!isset($_SESSION['insertion' . $sessionKey])) {
            $_SESSION['insertion' . $sessionKey]              = [];
            $_SESSION['insertion' . $sessionKey]["insertion"] = [];
        }

        if (!isset($_SESSION['insertion' . $sessionKey]["insertion"]) || empty($_SESSION['insertion' . $sessionKey]["insertion"])) {
            $_SESSION['insertion' . $sessionKey]["insertion"] = [];
        }

        $insertion = $_SESSION['insertion' . $sessionKey]["insertion"];

        if (isset($_POST["insertion"])) {
            foreach ($_POST["insertion"] as $key => $value) {
                if (empty($value)) {
                    if (isset($insertion[$key])) {
                        unset($insertion[$key]);
                    }
                } else {
                    $insertion[$key] = $value;
                }
            }
        }

        /** @var MediaService $mediaService */
        $mediaService = Oforge()->Services()->get('media');

        if (isset($_POST["images"])) {
            if (!isset($_SESSION['insertion' . $sessionKey]["images"])) {
                $_SESSION['insertion' . $sessionKey]["images"] = [];
            }

            foreach ($_POST["images"] as $value1) {
                $imageData = $mediaService->getById($value1);
                if (isset($imageData)) {
                    $imageData = $imageData->toArray();
                }

                //$value1 = (is_numeric($value1)) ? (int)$value1 : $value1;

                if (isset($_POST['images_interactions'])
                    && isset($_POST['images_interactions'][$value1])
                    && $_POST['images_interactions'][$value1] == 'main') {
                    foreach ($_SESSION['insertion' . $sessionKey]["images"] as $imageKey => $value2) {
                        $_SESSION['insertion' . $sessionKey]["images"][$imageKey]["main"] = false;
                    }
                    $imageData["main"] = true;
                }

                $_SESSION['insertion' . $sessionKey]["images"][] = $imageData;
            }
            unset($_POST["images"]);
        }


        $_SESSION['insertion' . $sessionKey]              = ArrayHelper::mergeRecursive($_SESSION['insertion' . $sessionKey], $_POST, true);
        $_SESSION['insertion' . $sessionKey]["insertion"] = $insertion;

        $mainIndex = $hasMoreImages ? -1 : 0;

        foreach ($_SESSION['insertion' . $sessionKey]["images"] as $index => $image) {
            if (isset($_SESSION['insertion' . $sessionKey]["images"][$index]["main"]) && $_SESSION['insertion' . $sessionKey]["images"][$index]["main"] == true) {
                $mainIndex = -1;
            }
        }

        //TODO remove?
        #print_r($mainIndex);

        if (isset($_POST['images_interactions'])) {
            $imgs = [];

               //delete images
            if ($_SESSION['insertion' . $sessionKey]["images"]) {
                foreach ($_SESSION['insertion' . $sessionKey]["images"] as $index => $image) {
                    if (isset($image["id"]) && isset($_POST["images_interactions"][$image["id"]])
                        && $_POST["images_interactions"][$image["id"]] == "delete") {
                        $mediaService->delete($image["id"]);
                    } else {
                        array_push($imgs, $image);
                    }
                }
            }

            $_SESSION['insertion' . $sessionKey]["images"] = $imgs;
            //find main image
            foreach ($_SESSION['insertion' . $sessionKey]["images"] as $index => $image) {
                if (isset($image["id"]) && isset($_POST["images_interactions"][$image["id"]])) {
                    if ($_POST["images_interactions"][$image["id"]] == "main") {
                        $mainIndex = $index;
                    }
                }

                $_SESSION['insertion' . $sessionKey]["images"][$index]["main"] = false;
            }
        }

        if ($mainIndex >= 0) {
            if (isset($_SESSION['insertion' . $sessionKey]["images"][$mainIndex])) {
                $_SESSION['insertion' . $sessionKey]["images"][$mainIndex]["main"] = true;
            }
        }

        $_SESSION['insertion' . $sessionKey]["images_interactions"] = [];
        if (isset($_POST['images_interactions'])) {
            $_SESSION['insertion' . $sessionKey]["images_interactions"] = $_POST['images_interactions'];
        }

        return $_SESSION['insertion' . $sessionKey];
    }

    public function clearProcessedData($sessionKey) {
        unset($_SESSION['insertion' . $sessionKey]);
    }

    public function getProcessedData($sessionKey) {
        return isset($_SESSION['insertion' . $sessionKey]) ? $_SESSION['insertion' . $sessionKey] : [];
    }

    public function setProcessedData($sessionKey, $data) {
        return $_SESSION['insertion' . $sessionKey] = $data;
    }

    public function parsePageData(array $pageData) : array {
        $language = $this->repository("language")->findOneBy(["iso" => "de"]);

        $data = [
            "contact"             => [
                "name"    => $pageData["contact_name"],
                "email"   => $pageData["contact_email"],
                "phone"   => $pageData["contact_phone"],
                "zip"     => $pageData["contact_zip"],
                "city"    => $pageData["contact_city"],
                "visible" => isset($pageData["contact_visible"]) && !empty($pageData["contact_visible"]) && $pageData["contact_visible"] != "off",
            ],
            "content"             => [
                "language"    => $language,
                "title"       => $pageData["insertion_title"],
                "description" => $pageData["insertion_description"],
            ],
            "media"               => [],
            "attributes"          => [],
            "price"               => isset($pageData["price"]) ? $pageData["price"] : 0,
            "min_price"           => isset($pageData["price_min"]) ? $pageData["price_min"] : null,
            "auction_url"         => isset($pageData["auction_url"]) ? $pageData["auction_url"] : null,
            "price_type"          => $pageData["price_type"],
            "tax"                 => isset($pageData["tax"]) ? $pageData["tax"] == "on" : 0,
            'images_interactions' => $pageData["images_interactions"],
        ];

        if (isset($pageData["insertion"]["vimeo_video_id"]) && $pageData["insertion"]["vimeo_video_id"] !== "" ) {
            /** @var MediaService $mediaService */
            $videoMedia = $this->repository("media")->findOneBy(["path" => $pageData["insertion"]["vimeo_video_id"]]);
            if(!$videoMedia) {
                $videoMedia = new Media();
                $videoMedia->setName($pageData["insertion_title"]);
                $videoMedia->setType('video/vimeo');
                $videoMedia->setPath($pageData["insertion"]["vimeo_video_id"]);
                $this->entityManager()->create($videoMedia, true);
            }
            array_push($data["media"], ["name" => $pageData["insertion_title"], "content" => $videoMedia, "main" => 0]);

        } elseif (isset($pageData["insertion"]["video_content_id"])) {
            /** @var InsertionUpdaterService $insertionUpdaterService */
            $insertionUpdaterService = Oforge()->Services()->get('insertion.updater');
            $insertionUpdaterService->deleteInsertionMediaByContentId($pageData["insertion"]["video_content_id"]);
        }

        if (isset($pageData["images"]) && sizeof($pageData["images"]) > 0) {
            foreach ($pageData["images"] as $image) {
                $media = $this->repository("media")->findOneBy(["id" => $image["id"]]);
                array_push($data["media"], ["name" => $image["name"], "content" => $media, "main" => $image["main"]]);
            }
        }



        if (isset($pageData["insertion"])) {
            foreach ($pageData["insertion"] as $key => $value) {
                $attributeKey = $this->repository("key")->findOneBy(["id" => $key]);
                if (isset($attributeKey)) {
                    if (is_array($value)) {
                        foreach ($value as $val) {
                            array_push($data["attributes"], ["attributeKey" => $attributeKey, "value" => $val]);
                        }
                    } else {
                        array_push($data["attributes"], ["attributeKey" => $attributeKey, "value" => $value]);
                    }
                }
            }
        }

        return $data;
    }

}

