<?php

namespace Seo\Middleware;

use Oforge\Engine\Modules\Core\Helper\RouteHelper;
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
         * @var SeoService $service
         */
        $service = Oforge()->Services()->get("seo");
        /**
         * @var SeoUrl $seoObject
         */
        $seoObject = $service->get($path);

        if ($seoObject != null) {
            $queryParams = RouteHelper::parseUrlWithQueryParams($seoObject->getSource());
            $newUri = $uri->withPath($queryParams['url']);
            $request = $request->withUri($newUri);
            if (!empty($params)) {
                //$request = $request->withQueryParams($params);
            } elseif(!empty($queryParams['query_params'])) {
                $request = $request->withQueryParams($queryParams['query_params']);
            }
        }

        return $next($request, $response);
    }
}
