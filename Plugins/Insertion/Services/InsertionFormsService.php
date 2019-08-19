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

    public function processPostData($sessionKey) : ?array {
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
            $insertion = array_merge($_SESSION['insertion' . $sessionKey]["insertion"], $_POST["insertion"]);
        }

        $_SESSION['insertion' . $sessionKey]              = array_merge($_SESSION['insertion' . $sessionKey], $_POST);
        $_SESSION['insertion' . $sessionKey]["insertion"] = $insertion;

        /** @var MediaService $mediaService */
        $mediaService = Oforge()->Services()->get('media');

        if (isset($_FILES["images"])) {
            if (!isset($_SESSION['insertion' . $sessionKey]["images"])) {
                $_SESSION['insertion' . $sessionKey]["images"] = [];
            }

            foreach ($_FILES["images"]["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $file = [];

                    foreach ($_FILES["images"] as $k => $v) {
                        $file[$k] = $_FILES["images"][$k][$key];
                    }

                    if (isset($_SESSION['insertion' . $sessionKey]['insertion_title'])) {
                        $prefix = trim($_SESSION['insertion' . $sessionKey]['insertion_title']);
                        $prefix = preg_replace("/[^[:alnum:][:space:]]/iu", '', $prefix);
                        $prefix = preg_replace("/\s+/", '_', $prefix);
                    }

                    $media     = $mediaService->add($file, $prefix);
                    $imageData = $media->toArray();
                    if (isset($_POST['images_temp_interactions']) && isset($_POST['images_temp_interactions'][$key])
                        && $_POST['images_temp_interactions'][$key] == 'main') {
                        foreach ($_SESSION['insertion' . $sessionKey]["images"] as $imageKey => $value) {
                            $_SESSION['insertion' . $sessionKey]["images"][$imageKey]["main"] = false;
                        }
                        $imageData["main"] = true;
                    }

                    $_SESSION['insertion' . $sessionKey]["images"][] = $imageData;
                }
            }
        }

        $mainIndex = 0;

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

        if (isset($_SESSION['insertion' . $sessionKey]["images"][$mainIndex])) {
            $_SESSION['insertion' . $sessionKey]["images"][$mainIndex]["main"] = true;
        }

        $_SESSION['insertion' . $sessionKey]["images_interactions"] = $_POST['images_interactions'];

        return $_SESSION['insertion' . $sessionKey];
    }

    public function clearProcessedData($sessionKey) {
        unset($_SESSION['insertion' . $sessionKey]);
    }

    public function getProcessedData($sessionKey) {
        return $_SESSION['insertion' . $sessionKey];
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
            "tax"                 => isset($pageData["tax"]) ? $pageData["tax"] == "on" : 0,
            'images_interactions' => $pageData["images_interactions"],
        ];

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

