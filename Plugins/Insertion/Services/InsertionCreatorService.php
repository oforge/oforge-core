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

class InsertionCreatorService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default'  => Insertion::class,
            'key'      => AttributeKey::class,
            'type'     => InsertionType::class,
            'language' => Language::class,
            'media'    => Media::class,
        ]);
    }

    public function processPostData($typeId) : ?array {
        if (!isset($_SESSION['insertion' . $typeId])) {
            $_SESSION['insertion' . $typeId] = [];
        }

        if (isset($_POST["current_page"])) {
            $_SESSION['insertion' . $typeId] = array_merge($_SESSION['insertion' . $typeId], $_POST);
            /** @var MediaService $mediaService */
            $mediaService = Oforge()->Services()->get('media');

            if (isset($_FILES["images"])) {
                if (!isset($_SESSION['insertion' . $typeId]["images"])) {
                    $_SESSION['insertion' . $typeId]["images"] = [];
                }

                foreach ($_FILES["images"]["error"] as $key => $error) {
                    if ($error == UPLOAD_ERR_OK) {
                        $file = [];

                        foreach ($_FILES["images"] as $k => $v) {
                            $file[$k] = $_FILES["images"][$k][$key];
                        }

                        $media     = $mediaService->add($file);
                        $imageData = $media->toArray();
                        if (isset($_POST['images_temp_interactions']) && isset($_POST['images_temp_interactions'][$key])
                            && $_POST['images_temp_interactions'][$key] == 'main') {
                            foreach ($_SESSION['insertion' . $typeId]["images"] as $imageKey => $value) {
                                $_SESSION['insertion' . $typeId]["images"][$imageKey]["main"] = false;
                            }
                            $imageData["main"] = true;
                        }

                        $_SESSION['insertion' . $typeId]["images"][] = $imageData;
                    }
                }
            }


            $mainIndex = 0;


            if (isset($_POST['images_interactions'])) {
                $imgs = [];

                //delete images
                foreach ($_SESSION['insertion' . $typeId]["images"] as $index => $image) {
                    if (isset($image["id"]) && isset($_POST["images_interactions"][$image["id"]]) && $_POST["images_interactions"][$image["id"]] == "delete") {
                        $mediaService->delete($image["id"]);
                    } else {
                        array_push($imgs, $image);
                    }
                }



                $_SESSION['insertion' . $typeId]["images"] = $imgs;
                //find main image
                foreach ($_SESSION['insertion' . $typeId]["images"] as $index => $image) {
                    if (isset($image["id"]) && isset($_POST["images_interactions"][$image["id"]])) {
                        if ($_POST["images_interactions"][$image["id"]] == "main") {
                            $mainIndex = $index;
                        }
                    }

                    $_SESSION['insertion' . $typeId]["images"][$index]["main"] = false;
                }
            }

            $_SESSION['insertion' . $typeId]["images"][$mainIndex]["main"] = true;
        }

        return $_SESSION['insertion' . $typeId];
    }

    public function clearProcessedData($typeId) {
        unset($_SESSION['insertion' . $typeId]);
    }

    public function getProcessedData($typeId) {
        return $_SESSION['insertion' . $typeId];
    }

    public function parsePageData(array $pageData) : array {
        $language = $this->repository("language")->findOneBy(["iso" => "de"]);

        $data = [
            "contact"    => [
                "name"    => $pageData["contact_name"],
                "email"   => $pageData["contact_email"],
                "phone"   => $pageData["contact_phone"],
                "zip"     => $pageData["contact_zip"],
                "city"    => $pageData["contact_city"],
                "visible" => isset($pageData["contact_visible"]) && !empty($pageData[3]["contact_visible"]),
            ],
            "content"    => [
                "language"    => $language,
                "name"        => $pageData["insertion_name"],
                "title"       => $pageData["insertion_title"],
                "description" => $pageData["insertion_description"],
            ],
            "media"      => [],
            "attributes" => [],
            "price"      => isset($pageData["price"]) ? $pageData["price"] : 0,
        ];

        if (isset($pageData["images"]) && sizeof($pageData["images"]) > 0) {
            foreach ($pageData["images"] as $image) {
                $media = $this->repository("media")->findOneBy(["id" => $image["id"]]);
                array_push($data["media"], ["name" => $image["name"],  "content" => $media, "main" => $image["main"]]);
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

    public function create(int $typeId, User $user, array $data) : ?int {
        $type = $this->repository("type")->findOneBy(["id" => $typeId]);

        if (isset($type)) {
            $insertion = Insertion::create(["insertionType" => $type, "user" => $user, "price" => $data["price"]]);

            $content = InsertionContent::create($data["content"]);
            $content->setInsertion($insertion);

            $contact = InsertionContact::create($data["contact"]);
            $contact->setInsertion($insertion);

            $media = [];
            foreach ($data["media"] as $mediaData) {
                $imedia = InsertionMedia::create($mediaData);
                $imedia->setInsertion($insertion);
                $this->entityManager()->create($imedia, false);
                array_push($media, $imedia);
            }

            $attributeValues = [];
            foreach ($data["attributes"] as $attributeData) {
                $attributeValue = InsertionAttributeValue::create($attributeData);
                $attributeValue->setInsertion($insertion);
                $this->entityManager()->create($attributeValue, false);
                array_push($attributeValues, $attributeValue);
            }

            $insertion->setContent([$content]);
            $insertion->setContact($contact);
            $insertion->setMedia($media);
            $insertion->setValues($attributeValues);

            $this->entityManager()->create($content, false);
            $this->entityManager()->create($contact, false);
            $this->entityManager()->create($insertion, false);
            $this->entityManager()->flush();

            return $insertion->getId();
        }
    }
}

