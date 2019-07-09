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

        $result["price"] = $insertion->getPrice();
        $result["tax"]   = $insertion->isTax();

        $result["images"] = [];
        if ($insertion->getMedia() != null) {
            foreach ($insertion->getMedia() as $media) {
                if ($media->getContent() != null) {
                    $imageData = $media->getContent()->toArray(0);
                    //  $imageData["id"]    = $media->getId();
                    $imageData["main"]  = $media->isMain();
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

    public function update(Insertion $insertion, array $data) {
        if ($insertion->getContent() == null || sizeof($insertion->getContent()) == 0) {
            $content = InsertionContent::create($data["content"]);
            $content->setInsertion($insertion);
            $insertion->setContent([$content]);
            $this->entityManager()->create($content, false);
        } else {
            $content = $insertion->getContent()[0];
            $content->fromArray($data["content"]);
            $this->entityManager()->update($content, false);
        }

        if ($insertion->getContact() == null) {
            $contact = InsertionContact::create($data["contact"]);
            $contact->setInsertion($insertion);
            $insertion->setContact($contact);
            $this->entityManager()->create($contact, false);
        } else {
            $contact = $insertion->getContact();
            $contact->fromArray($data["contact"]);
            $this->entityManager()->update($contact, false);
        }

        $insertion->setPrice($data["price"]);
        $insertion->setTax($data["tax"]);

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
            $this->entityManager()->remove($item, false);
        }

        //create new values
        foreach ($notTouched as $key => $value) {
            $attributeData = $data["attributes"][$key];

            $attributeValue = InsertionAttributeValue::create($attributeData);
            $attributeValue->setInsertion($insertion);
            $this->entityManager()->create($attributeValue, false);
            $insertion->getValues()->add($attributeValue);

        }

        $medias     = $insertion->getMedia();
        $notTouched = [];
        $modified   = [];

        //update existing media
        foreach ($data["media"] as $key => $attributeData) {
            $touch = false;
            foreach ($medias as $img) {
                if (isset($attributeData["content"]) && $img->getContent()->getId() == $attributeData["content"]->getId()) {
                    $img->setMain($attributeData["main"]);
                    $modified[$img->getId()] = true;
                    $touch                   = true;

                    break;
                }
            }

            if (!$touch) {
                $notTouched[$key] = true;
            }
        }

        if (isset($data["images_interactions"])) {

            //collect items for deletion
            $delete = [];
            foreach ($medias as $media) {
                if (isset($data["images_interactions"][$media->getContent()->getId()]) && $data["images_interactions"][$media->getContent()->getId()] == "delete") {
                    $delete[] = $media;
                }
            }

            //delete not modified elements
            foreach ($delete as $item) {
                $insertion->getMedia()->removeElement($item);
                $this->entityManager()->remove($item);
            }
        }

        //create new values
        foreach ($notTouched as $key => $value) {
            $mediaData = $data["media"][$key];

            $imedia = InsertionMedia::create($mediaData);
            $imedia->setInsertion($insertion);
            $this->entityManager()->create($imedia);
            $insertion->getMedia()->add($imedia);
        }

        $medias = $insertion->getMedia();
        foreach ($medias as $media) {
            $media->setMain(false);
        }

        if (isset($data["images_interactions"])) {
            foreach ($medias as $media) {
                if (isset($data["images_interactions"][$media->getContent()->getId()]) && $data["images_interactions"][$media->getContent()->getId()] == "main") {
                    $media->setMain(true);
                }
            }
        } elseif (sizeof($medias) > 0) {
            $medias[0]->setMain(true);
        }

        //save coordinates
        Oforge()->Services()->get("insertion.zip")->get($contact->getZip());

        $this->entityManager()->update($insertion, false);

        $this->entityManager()->flush();
    }

    public function deactivate(Insertion $insertion) {
        $insertion->setActive(false);
        $insertion->setDeleted(false);
        $this->entityManager()->update($insertion);
    }

    public function activate(Insertion $insertion) {
        $insertion->setActive(true);
        $insertion->setDeleted(false);
        $this->entityManager()->update($insertion);
    }

    public function delete(Insertion $insertion) {
        $insertion->setActive(false);
        $insertion->setDeleted(true);
        $this->entityManager()->update($insertion);
    }
}

