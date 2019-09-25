<?php


namespace Translation\Services;


use Oforge\Engine\Modules\APIRaven\Services\APIRavenService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class TranslationService extends AbstractDatabaseAccess
{
    /** @var APIRavenService */
    protected $apiraven;

    protected $apiKey = 'AIzaSyArJKPvNAuVOA3e1zlI6ILYop2zhcGxtrk';

    protected $googleApplicationCredentials = 'Oforge\Plugins/Translation/Keys/All Your Horses-a15cb4159d39.json';

    protected $googleTranslationApi = "https://translation.googleapis.com/language/translate/";

    public function __construct()
    {
        $this->apiraven = Oforge()->Services()->get('apiraven');
    }

    public function autoTranslate(string $stringToTranslate, array $languages)
    {
        $sourceLanguage = "";

        $this->translateFrom($sourceLanguage, $stringToTranslate, $this->fetchAvailableLanguages());
    }

    public function translateFrom(string $sourceLanguage, string $stringToTranslate, array $languages)
    {
        $this->apiraven->setApi($this->googleTranslationApi, ' ', $this->apiKey);
        $result = [];
        foreach ($languages as $language) {
            $data = [
                'q' => $stringToTranslate,
                'source' => $sourceLanguage,
                'target' => $language,
                'format' => 'text/html'
            ];
            $result[$language] = $this->apiraven->post('v2', $data);
        }
        return $result;
    }

    /**
     * @return array $availableLanguages
     */
    public function fetchAvailableLanguages()
    {
        $availableLanguages = [];

        return $availableLanguages;
    }

    public function storeTranslations()
    {

    }
}
