<?php

namespace Seo\Middleware;

use Seo\Models\SeoUrl;
use Seo\Services\SeoService;
use Slim\Http\Request;
use Slim\Http\Response;

class SeoMiddleware {
    public function __invoke(Request $request,Response $response, $next) {
        $uri  = $request->getUri();
        $params = $request->getQueryParams();
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
