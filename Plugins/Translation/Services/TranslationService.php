<?php


namespace Translation\Services;

use Doctrine\ORM\ORMException;
use Exception;
use Google\Api\Service;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionContent;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Google\Cloud\Translate\V2\TranslateClient;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\ConfigService;
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
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $apiKey = $configService->get('google_cloud_translation_api_key');
        $translate = new TranslateClient(['key' => $apiKey]);
        $result = [];
        foreach ($targetLanguages as $targetLanguage) {
            $rawResult = $translate->translate($stringToTranslate, ['target' => $targetLanguage]);
            $result[$targetLanguage] = $rawResult['text'];
        }
        return $result;
    }

    /**
     * @param Insertion $insertion
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function translateInsertion($insertion) {
        $insertionService = Oforge()->Services()->get('insertion');
        /** @var InsertionContent[] $insertionContent */
        $insertionContent = $insertion->getContent();

        $translations = [];

        /** @var string[] $languages */
        $languages = $this->fetchAvailableLanguages();

        foreach ($languages as $language) {
            /** @var InsertionContent $localisedInsertionContent */
            $localisedInsertionContent = $insertionService->getInsertionContentByLanguage($insertion->getId(), $language);

            if (isset($localisedInsertionContent)) {
                $translations[$language] = ['insertion-title' => $localisedInsertionContent->getTitle(), 'insertion-description' => $localisedInsertionContent->getDescription()];
            } else {
                $title = $this->translate($insertionContent[0]->getTitle(), [$language]);
                $description = $this->translate($insertionContent[0]->getDescription(), [$language]);
                $translations[$language] = ['insertion-title' => $title[$language], 'insertion-description' => $description[$language]];
                $insertionService->addTranslatedInsertionContent([
                    'id'            => $insertion->getId(),
                    'description'   => $description[$language],
                    'language'      => $language,
                    'title'         => $title[$language]
                ]);
            }
        }
        return $translations;
    }

    public function fetchAvailableLanguages()
    {
        /** @var LanguageService $languageService */
        $languageService = Oforge()->Services()->get('i18n.language');

        /** @var Language[] $availableLanguages */
        $availableLanguages = $languageService->list(['active' => true]);
        foreach ($availableLanguages as &$language) {
            $language = $language->getIso();
        }
        return $availableLanguages;
    }
}
