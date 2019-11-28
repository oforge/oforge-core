<?php


namespace Translation\Services;

use Doctrine\ORM\ORMException;
use Exception;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Google\Cloud\Translate\V2\TranslateClient;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Services\LanguageService;

class TranslationService extends AbstractDatabaseAccess
{

    public function __construct()
    {
    }

    /**
     * @param string $stringToTranslate
     * @param array $targetLanguages
     * @return array
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function translate(string $stringToTranslate, $targetLanguages = [])
    {
        $targetLanguages = empty($targetLanguages) ? $this->fetchAvailableLanguages() : $targetLanguages;

        $translate = new TranslateClient(['key' => 'API_KEY']);
        $result = [];
        foreach ($targetLanguages as $targetLanguage) {
            $result[$targetLanguage] = $translate->translate($stringToTranslate, ['target' => $targetLanguage]);
        }
        return $result;
    }

    public function fetchAvailableLanguages(){
        /** @var LanguageService $languageService */
        $languageService = Oforge()->Services()->get('i18n.language');

        /** @var Language[] $availableLanguages */
        $availableLanguages = $languageService->list();
        foreach ($availableLanguages as &$language){
            $language = $language->getIso();
        }
        return $availableLanguages;
    }
}
