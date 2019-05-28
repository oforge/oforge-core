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

class InsertionUpdaterService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default'  => Insertion::class,
            'key'      => AttributeKey::class,
            'type'     => InsertionType::class,
            'language' => Language::class,
            'media'    => Media::class,
        ]);
    }

    public function getFormData(Insertion $insertion) : ?array {
        $result = ["id" => $insertion->getId()];

        $contentData = $insertion->getContent() != null && sizeof($insertion->getContent()) > 0 ? $insertion->getContent()[0]->toArray(0) : [];
        $contactData = $insertion->getContact() != null ? $insertion->getContact()->toArray(0) : [];

        foreach ($contactData as $key => $value) {
            $result["contact_" . $key] = $value;
        }

        foreach ($contentData as $key => $value) {
            $result["insertion_" . $key] = $value;
        }

        $result["images"] = [];
        if ($insertion->getMedia() != null) {
            foreach ($insertion->getMedia() as $media) {
                if ($media->getContent() != null) {
                    $imageData          = $media->getContent()->toArray(0);
                    $imageData["id"]    = $media->getId();
                    $result["images"][] = $imageData;
                }
            }
        }

        $result["insertion"] = [];

        if ($insertion->getValues() != null) {
            foreach ($insertion->getValues() as $value) {
                if (isset($result["insertion"][$value->getAttributeKey()->getId()])) {
                    if (is_array($result["insertion"][$value->getAttributeKey()->getId()])) {
                        $result["insertion"][$value->getAttributeKey()->getId()][] = $value->getValue();
                    } else {
                        $oldValue                                                = $result["insertion"][$value->getAttributeKey()->getId()];
                        $result["insertion"][$value->getAttributeKey()->getId()] = [];
                        $result["insertion"][$value->getAttributeKey()->getId()] = [$oldValue, $value->getValue()];
                    }

                } else {
                    $result["insertion"][$value->getAttributeKey()->getId()] = $value->getValue();;
                }

            }
        }

        return $result;
    }

    public function parsePageData(array $pageData) : array {
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
                "name"        => $pageData["insertion_name"],
                "title"       => $pageData["insertion_title"],
                "description" => $pageData["insertion_description"],
            ],
            "media"      => [],
            "attributes" => [],
        ];

        if (isset($pageData["images"]) && sizeof($pageData["images"]) > 0) {
            foreach ($pageData["images"] as $image) {
                $media = $this->repository("media")->findOneBy(["id" => $image["id"]]);
                array_push($data["media"], ["name" => $image["name"], "content" => $media]);
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

    public function update(Insertion $insertion, array $data) {
        if ($insertion->getContent() == null || sizeof($insertion->getContent()) == 0) {
            $content = InsertionContent::create($data["content"]);
            $content->setInsertion($insertion);
            $insertion->setContent([$content]);
        }

        $content = $insertion->getContent()[0];
        $content->fromArray($data["content"]);

        $this->entityManager()->persist($content);

        if ($insertion->getContact() == null) {
            $contact = InsertionContact::create($data["contact"]);
            $contact->setInsertion($insertion);
            $insertion->setContact($contact);
        }

        $contact = $insertion->getContact();
        $contact->fromArray($data["contact"]);

        $this->entityManager()->persist($contact);
        //TODO set correct price
        $insertion->setPrice($data["price"] ? : 100.0);

        $notTouched = [];
        $modified   = [];
        $values     = $insertion->getValues();

        //update existing values
        foreach ($data["attributes"] as $key => $attributeData) {
            $touch = false;
            foreach ($values as $value) {
                $keyId = $value->getAttributeKey()->getId();
                if ($keyId == $attributeData["attributeKey"]->getId() && !isset($modified[$value->getId()])) {
                    $value->setValue($attributeData["value"]);
                    $modified[$value->getId()] = true;
                    $touch                     = true;
                    break;
                }
            }

            if (!$touch) {
                $notTouched[$key] = true;
            }
        }

        //collect items for deletion
        $delete = [];
        foreach ($values as $value) {
            if (!isset($modified[$value->getId()])) {
                $delete[] = $value;
            }
        }

        //delete not modified elements
        foreach ($delete as $item) {
            $insertion->getValues()->removeElement($item);
            $this->entityManager()->remove($item);
        }

        //create new values
        foreach ($notTouched as $key => $value) {
            $attributeData = $data["attributes"][$key];

            $attributeValue = InsertionAttributeValue::create($attributeData);
            $attributeValue->setInsertion($insertion);
            $this->entityManager()->persist($attributeValue);
            $insertion->getValues()->add($attributeValue);

        }

        $this->entityManager()->persist($insertion);
        //TODO update media data
        $this->entityManager()->flush();

    }

    public function deactivate(Insertion $insertion) {
        $insertion->setActive(false);
        $insertion->setDeleted(false);
        $this->entityManager()->persist($insertion);
        $this->entityManager()->flush();
    }

    public function activate(Insertion $insertion) {
        $insertion->setActive(true);
        $insertion->setDeleted(false);
        $this->entityManager()->persist($insertion);
        $this->entityManager()->flush();
    }

    public function delete(Insertion $insertion) {
        $insertion->setActive(false);
        $insertion->setDeleted(true);
        $this->entityManager()->persist($insertion);
        $this->entityManager()->flush();
    }
}

