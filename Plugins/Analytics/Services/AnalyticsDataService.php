<?php
namespace Analytics\Services;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;

class AnalyticsDataService {
    /**
     * @return array
     * @throws ServiceNotFoundException
     * @throws ConfigElementNotFoundException
     */
  public function getData() {
      $apiKey = $this->getKey();

      $dummyData = [12, 19, 3, 5, 2, 3];
      return $dummyData;
  }

    /**
     * @throws ServiceNotFoundException
     * @throws ConfigElementNotFoundException
     */
  private function getKey() {
      /** @var  $configService */
      $configService = Oforge()->Services()->get("config");
      return $configService->get('analytics_api_key');
  }
}
