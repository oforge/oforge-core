<?php

namespace Oforge\Engine\Modules\I18n\Middleware;

use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\I18n\Services\LanguageService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class I18nMiddleware
 *
 * @package Oforge\Engine\Modules\I18n\Middleware
 */
class I18nMiddleware {

    /** @inheritDoc */
    public function __invoke(Request $request, Response $response, $next) {
        $uri  = $request->getUri();
        $path = $uri->getPath();
        /** @var LanguageService $languageService */
        $languageService = Oforge()->Services()->get('i18n.language');

        $languageIso    = $languageService->getCurrentLanguageIso();
        $languages      = $languageService->getActiveLanguages();
        $activeLanguage = [];
        foreach ($languages as $language) {
            $activeLanguage[$language->getIso()] = $language->getId();
        }
        $languageID = ArrayHelper::get($activeLanguage, $languageIso, 1);
        //Split path into chunks
        $pathChunks = explode('/', $path, 3);
        if (count($pathChunks) > 1 && isset($activeLanguage[strtolower($pathChunks[1])])) {
            $languageIso = strtolower($pathChunks[1]);
            $languageID  = $activeLanguage[strtolower($pathChunks[1])];
            //Produce new URI without language reference
            unset($pathChunks[1]);
            $newPath = implode('/', $pathChunks);
            $newUri  = $uri->withPath($newPath);
            $request = $request->withUri($newUri);
        }
        Oforge()->View()->assign([
            'meta.language' => [
                'id'  => $languageID,
                'iso' => $languageIso,
            ],
        ]);

        return $next($request, $response);
    }

}
