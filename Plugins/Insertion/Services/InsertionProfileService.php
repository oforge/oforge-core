<?php

namespace Insertion\Services;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use Insertion\Models\InsertionProfile;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Manager\Events\Event;
use Oforge\Engine\Modules\Media\Services\MediaService;
use ReflectionException;

class InsertionProfileService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([
            'default' => InsertionProfile::class,
        ]);
    }

    public function list(array $criteria = [], ?array $orderBy = null) {
        /**
         * @var InsertionProfile[] $entities
         */
        $entities = $this->repository()->findBy($criteria, $orderBy);

        return $entities;
    }

    /**
     * @param int $user
     *
     * @return InsertionProfile|null
     * @throws ORMException
     */
    public function get(int $user) : ?InsertionProfile {
        /**
         * @var $result InsertionProfile
         */
        $result = $this->repository()->findOneBy(["user" => $user]);

        return $result;
    }

    /**
     * @param int $id
     *
     * @return InsertionProfile|null
     * @throws ORMException
     */
    public function getById($id) : ?InsertionProfile {
        /**
         * @var $result InsertionProfile
         */
        $result = $this->repository()->find($id);

        return $result;
    }

    /**
     * @param User $user
     * @param array $params
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws ReflectionException
     */
    public function update(User $user, array $params) {
        /**
         * @var $result InsertionProfile
         */
        $result = $this->repository()->findOneBy(["user" => $user]);

        /** @var MediaService $mediaService */
        $mediaService = Oforge()->Services()->get('media');

        $create = $result == null;

        if ($create) {
            $result = new InsertionProfile();
        }

        if (isset($_FILES["background"])) {
            $media = $mediaService->add($_FILES["background"]);
            if ($media != null) {
                $oldId = null;

                if ($result->getBackground() != null) {
                    $oldId = $result->getBackground()->getId();
                }

                $result->setBackground($media);

                if ($oldId != null) {
                    $mediaService->delete($oldId);
                }
            }
        }

        $imprintWebsite = $params["imprint_website"];
        $disallowed     = ['http://', 'https://'];

        foreach ($disallowed as $d) {
            if (strpos($imprintWebsite, $d) === 0) {
                $imprintWebsite = str_replace($d, '', $imprintWebsite);
            }
        }

        $result->fromArray([
            "description"          => $params["description"] || "",
            "user"                 => $user,
            "imprintName"          => $params["imprint_name"],
            "imprintStreet"        => $params["imprint_street"],
            "imprintZipCity"       => $params["imprint_zipCity"],
            "imprintPhone"         => $params["imprint_phone"],
            "imprintEmail"         => $params["imprint_email"],
            "imprintFacebook"      => $params["imprint_facebook"],
            "imprintWebsite"       => $imprintWebsite,
            "imprintCompanyTaxId"  => $params["imprint_company_tax"],
            "imprintCompanyNumber" => $params["imprint_company_number"],
        ]);

        if ($create) {
            $this->entityManager()->create($result);
        } else {
            $this->entityManager()->update($result);
        }
    }

    public function getInsertionProvidersData() : array {
        /** @var InsertionListService $insertionListService */
        $insertionListService = Oforge()->Services()->get('insertion.list');

        $profiles = [];
        /** @var InsertionProfile[] $insertionProfiles */
        $insertionProfiles = $this->list(/*[], [
            'imprintName' => 'ASC',
        ]*/);
        foreach ($insertionProfiles as $insertionProfile) {
            $includeProfile = true;
            $includeProfile = Oforge()->Events()->trigger(#
                Event::create(self::class . '::getInsertionProvidersData.include', [
                    'insertionProfile' => $insertionProfile,
                ], $includeProfile, true)#
            );
            if ($includeProfile) {
                $insertionCount = $insertionListService->getUserInsertionCount($insertionProfile->getUser(), [#
                    // 'active' => true
                ]);
                // if ($insertionCount === 0) {
                //     continue;
                // }
                $insertionData                   = $insertionProfile->toArray(2);
                $insertionData['insertionCount'] = $insertionCount;
                $insertionData['insertionTypes'] = $insertionListService->getUserDistinctInsertionTypes($insertionProfile->getUser());

                $insertionData = $includeProfile = Oforge()->Events()->trigger(#
                    Event::create(self::class . '::getInsertionProvidersData.addData', [], $insertionData, true)#
                );
                $profiles[]    = $insertionData;
            }

        }

        return $profiles;
    }

}
