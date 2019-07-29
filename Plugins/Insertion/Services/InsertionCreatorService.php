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
            $insertion->setTax($data["tax"]);

            $this->entityManager()->create($content, false);
            $this->entityManager()->create($contact, false);
            $this->entityManager()->create($insertion, false);
            $this->entityManager()->flush();

            //save coordinates
            Oforge()->Services()->get("insertion.zip")->get($contact->getZip());

            return $insertion->getId();
        }
    }
}

