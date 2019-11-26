<?php

namespace Insertion\Services;

use Doctrine\ORM\ORMException;
use Exception;
use Insertion\Models\InsertionZipCoordinates;
use Oforge\Engine\Modules\APIRaven\Services\APIRavenService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

class InsertionZipService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default' => InsertionZipCoordinates::class,
        ]);
    }

    public function get($zip, $country = 'germany') : ?InsertionZipCoordinates {
        $entry = $this->repository()->findOneBy(["zip" => $zip, "country" => $country]);

        if ($entry == null) {
            $entry = $this->catch($zip, $country);
        }

        return $entry;
    }

    /**
     * @param $zip
     * @param string $country
     *
     * @return InsertionZipCoordinates|null
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function catch($zip, $country = 'germany') : ?InsertionZipCoordinates {
        /**
         * @var APIRavenService $apiService
         */
        $apiService = Oforge()->Services()->get('apiraven');
        $result = [];

        try {
            $result = $apiService->get("https://nominatim.openstreetmap.org/search", ["postalcode" => $zip, "country" => $country, "format" => "json"]);
        } catch (Exception $e) {
            Oforge()->Logger()->logException($e);
            // TODO: Send email?
        }
        $entry  = null;

        if (!empty($result)) {
            $entry = InsertionZipCoordinates::create(["zip" => $zip, "country" => $country, "lat" => $result[0]["lat"], "lng" => $result[0]["lon"]]);
            $this->entityManager()->create($entry);
        }

        return $entry;
    }
}
