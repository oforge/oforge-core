<?php

namespace Oforge\Engine\Modules\I18n\Middleware;

use Oforge\Engine\Modules\I18n\Services\LanguageService;

class I18nMiddleware {

    public function __invoke($request, $response, $next) {
        $uri  = $request->getUri();
        $path = $uri->getPath();

        //Split path into chunks
        $pathChunks = explode("/", $path);

        /**
         * @var $service LanguageService
         */
        $service = Oforge()->Services()->get("i18n.language");

        $languages = $service->getActiveLanguages();

        $map = [];
        $iso = null;
        foreach ($languages as $language) {
            $map[] = strtolower($language->getIso());
            if ($iso == null && $language->isDefault()) {
                $iso = $language->getIso();
            }
        }

        if (sizeof($pathChunks) > 1 && strlen($pathChunks[1]) == 2 && in_array(strtolower($pathChunks[1]), $map)) {
            $iso = strtolower($pathChunks[1]);

            //Produce new URI without language reference
            unset($pathChunks[1]);
            $newPath = implode('/', $pathChunks);
            $newUri  = $uri->withPath($newPath);
            $request = $request->withUri($newUri);
        }

        Oforge()->View()->assign(["meta" => ["language" => $iso]]);

        return $next($request, $response);
    }
}
