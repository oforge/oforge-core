<?php

namespace Seo\Middleware;

use Seo\Models\SeoUrl;
use Seo\Services\SeoService;

class SeoMiddleware {
    public function __invoke($request, $response, $next) {
        $uri  = $request->getUri();
        $path = $uri->getPath();

        /**
         * @var $service SeoService
         */
        $service = Oforge()->Services()->get("seo");
        /**
         * @var $seoObject SeoUrl
         */
        $seoObject = $service->get($path);

        if ($seoObject != null) {
            $newUri = $uri->withPath($seoObject->getSource());

            $request = $request->withUri($newUri);
        }

        return $next($request, $response);
    }
}
