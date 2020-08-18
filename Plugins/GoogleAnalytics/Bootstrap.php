<?php

namespace GoogleAnalytics;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;

/**
 * Class Bootstrap
 *
 * @package GoogleAnalytics
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
    }

    /** @inheritDoc */
    public function install() {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        $configService->add([
            'name'     => 'google_analytics_api_key',
            'type'     => ConfigType::STRING,
            'group'    => 'google_analytics',
            'default'  => 0,
            'label'    => 'config_google_analytics_api_key',
            'required' => true,
            'order'    => 0,
        ]);
        $configService->add([
            'name'     => 'google_analytics_tracking_id',
            'type'     => ConfigType::STRING,
            'group'    => 'google_analytics',
            'default'  => '',
            'label'    => 'config_google_analytics_tracking_id',
            'required' => true,
            'order'    => 1,
        ]);
    }

    /** @inheritDoc */
    public function uninstall(bool $keepData) {
        if (!$keepData) {
            /** @var ConfigService $configService */
            $configService = Oforge()->Services()->get('config');
            $configService->remove('google_analytics_api_key');
            $configService->remove('google_analytics_tracking_id');
        }
    }

}
