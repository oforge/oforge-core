<?php


namespace Translation\Controller\Frontend;


use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionContent;
use Insertion\Services\InsertionService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Translation\Services\TranslationService;

/**
 * Class TranslationController
 *
 * @package Translation\Controller\Frontend
 * @EndpointClass(path="/translate", name="frontend_translation", assetScope="Frontend")
 */
class TranslationController extends SecureFrontendController
{


    /**
     * @param Request $request
     * @param Response $response
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @EndpointAction()
     */

    public function indexAction(Request $request, Response $response)
    {
        /** @var TranslationService $translationService */
        $translationService = Oforge()->Services()->get('translation');
        $translations = $translationService->translate('Auf der Mauer sitzt eine kleine Wanze.');
        print_r($translations);
        die();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @EndpointAction(path="/insertion/{id}", name="insertion")
     */
    public function translateInsertionAction(Request $request, Response $response, $args)
    {
        /** @var InsertionService $insertionService */
        $insertionService = Oforge()->Services()->get('insertion');

        /** @var Insertion $insertion */
        $insertion = $insertionService->getInsertionById($args['id']);

        if (isset($insertion)) {
            /** @var InsertionContent[] $insertionContent */
            $insertionContent = $insertion->getContent();

            /** @var TranslationService $translationService */
            $translationService = Oforge()->Services()->get('translation');

            $translations = [];

            /** @var string[] $languages */
            $languages = $translationService->fetchAvailableLanguages();

            foreach ($languages as $language) {
                /** @var InsertionContent $localisedInsertionContent */
                $localisedInsertionContent = $insertionService->getInsertionContentByLanguage($args['id'], $language);

                if (isset($localisedInsertionContent)) {
                    $translations[$language] = ['title' => $localisedInsertionContent->getTitle(), 'description' => $localisedInsertionContent->getDescription()];
                } else {
                    $title = $translationService->translate($insertionContent[0]->getTitle(), [$language]);
                    $description = $translationService->translate($insertionContent[0]->getDescription(), [$language]);
                    $translations[] = ['title' => $title, 'description' => $description];
                    $insertionService->addTranslatedInsertionContent([
                        'id'            => $args['id'],
                        'description'   => $description[$language]['text'],
                        'language'      => $language,
                        'title'         => $title[$language]['text']
                        ]);
                }

            }
            Oforge()->View()->assign(['json' => $translations]);
        }
    }
}
