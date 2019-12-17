<?php


namespace Translation;


use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Models\Language;
use Translation\Controller\Frontend\TranslationController;
use Translation\Services\TranslationService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Plugins\Translation
 */
class Bootstrap extends AbstractBootstrap
{
    public function __construct()
    {
        $this->endpoints = [
            TranslationController::class
        ];

        $this->models = [
            Language::class,
        ];

        $this->services = [
            'translation' => TranslationService::class
        ];

        $this->dependencies = [
            \Insertion\Bootstrap::class
        ];
    }

    public function install()
    {
        parent::install(); // TODO: Change the autogenerated stub

        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        $configService->add([
            'name' => 'google_cloud_translation_api_key',
            'type' => ConfigType::STRING,
            'group' => 'google_cloud',
            'default' => '',
            'label' => 'config_google_cloud_translation_api_key',
            'required' => true,
            'order' => 0,
        ]);
    }
}
