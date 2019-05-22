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
            $currentPage = $_POST["current_page"];

            $_SESSION['insertion' . $typeId][$currentPage] = $_POST;

            if (isset($_FILES["images"])) {
                /** @var MediaService $configService */
                $mediaService = Oforge()->Services()->get('media');

                if (!isset($_SESSION['insertion' . $typeId][$currentPage]["images"])) {
                    $_SESSION['insertion' . $typeId][$currentPage]["images"] = [];
                }

                $imgs = [];

                foreach ($_SESSION['insertion' . $typeId][$currentPage]["images"] as $image) {
                    if (isset($_POST["images_interactions"])) {
                        if (isset($image["id"]) && isset($_POST["images_interactions"][$image["id"]])
                            && $_POST["images_interactions"][$image["id"]] == "delete") {
                            $mediaService->delete($image["id"]);
                        } else {
                            array_push($imgs, $image);
                        }
                    }
                }

                $_SESSION['insertion' . $typeId][$currentPage]["images"] = $imgs;

                foreach ($_FILES["images"]["error"] as $key => $error) {
                    if ($error == UPLOAD_ERR_OK) {
                        $file = [];

                        foreach ($_FILES["images"] as $k => $v) {
                            $file[$k] = $_FILES["images"][$k][$key];
                        }

                        $media                                                     = $mediaService->add($file);
                        $_SESSION['insertion' . $typeId][$currentPage]["images"][] = $media->toArray();
                    }
                }
            }
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
                "name"    => $pageData[3]["contact_name"],
                "email"   => $pageData[3]["contact_email"],
                "phone"   => $pageData[3]["contact_phone"],
                "zip"     => $pageData[3]["contact_zip"],
                "city"    => $pageData[3]["contact_city"],
                "visible" => isset($pageData[3]["contact_visible"]) && !empty($pageData[3]["contact_visible"]),
            ],
            "content"    => [
                "language"    => $language,
                "name"        => $pageData[1]["insertion_name"],
                "title"       => $pageData[1]["insertion_title"],
                "description" => $pageData[3]["insertion_description"],
            ],
            "media"      => [],
            "attributes" => [],
        ];

        if (isset($pageData[1]["images"]) && sizeof($pageData[1]["images"]) > 0) {
            foreach ($pageData[1]["images"] as $image) {
                $media = $this->repository("media")->findOneBy(["id" => $image["id"]]);
                array_push($data["media"], ["name" => $image["name"], $media]);
            }
        }

        foreach ($pageData as $page) {
            if (isset($page["insertion"])) {
                foreach ($page["insertion"] as $key => $value) {
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
        }

        return $data;
    }

    public function create(int $typeId, User $user, array $data) : ?int {
        $type = $this->repository("type")->findOneBy(["id" => $typeId]);

        if (isset($type)) {
            $insertion = Insertion::create(["insertionType" => $type, "user" => $user]);

            $content = InsertionContent::create($data["content"]);
            $content->setInsertion($insertion);

            $contact = InsertionContact::create($data["contact"]);
            $contact->setInsertion($insertion);

            $media   = [];
            foreach ($data["media"] as $mediaData) {
                $imedia = InsertionMedia::create($mediaData);
                $imedia->setInsertion($insertion);
                $this->entityManager()->persist($imedia);
                array_push($media, $imedia);
            }


            $attributeValues   = [];
            foreach ($data["attributes"] as $attributeData) {
                $attributeValue = InsertionAttributeValue::create($attributeData);
                $attributeValue->setInsertion($insertion);
                $this->entityManager()->persist($attributeValue);
                array_push($attributeValues, $attributeValue);
            }

            $insertion->setContent([$content]);
            $insertion->setContact($contact);
            $insertion->setMedia($media);
            $insertion->setValues($attributeValues);

            $this->entityManager()->persist($content);
            $this->entityManager()->persist($contact);
            $this->entityManager()->persist($insertion);
            $this->entityManager()->flush();

            return $insertion->getId();
        }
    }
}

