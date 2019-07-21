<?php

namespace Insertion\Services;

use Insertion\Models\InsertionZipCoordinates;
use Oforge\Engine\Modules\APIRaven\Services\APIRavenService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

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

    public function catch($zip, $country = 'germany') : ?InsertionZipCoordinates {
        /**
         * @var $apiService APIRavenService
         */
        $apiService = Oforge()->Services()->get('apiraven');

        $result = $apiService->get("https://nominatim.openstreetmap.org/search", ["postalcode" => $zip, "country" => $country, "format" => "json"]);
        $entry  = null;

        if (sizeof($result) > 0) {
            $entry = InsertionZipCoordinates::create(["zip" => $zip, "country" => $country, "lat" => $result[0]["lat"], "lng" => $result[0]["lon"]]);
            $this->entityManager()->create($entry);
        }

        return $entry;
    }

}

