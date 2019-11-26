<?php

namespace Seo\Middleware;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Seo\Models\SeoUrl;
use Seo\Services\SeoService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

class SeoMiddleware {
    /**
     * When a SEO url is requested:
     * - check the query params
     * - find the matching endpoint
     * - respond with result from that endpoint
     *
     * @param Request $request
     * @param Response $response
     * @param $next
     *
     * @return mixed
     * @throws ServiceNotFoundException
     */
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
            Oforge()->View()->assign(['seo' => [
                'url_id' => $seoObject->getId(),
                'url_name' => str_replace('/', '', $path),
            ]]);
            $request = $request->withUri($newUri);
            if (!empty($params)) {
                /**
                 * We cannot add QueryParams if the uri has a query.
                 * So we create a new uri here.
                 */
                $uriWithoutParams = new Uri($newUri->getScheme(), $newUri->getHost());
                $uriWithoutParams = $uriWithoutParams
                    ->withPath($newUri->getPath())
                    ->withFragment($newUri->getFragment())
                    ->withPort($newUri->getPort())
                    ->withUserInfo($newUri->getUserInfo());
                $merge = array_merge($queryParams['query_params'], $params);

                $request = $request->withUri($uriWithoutParams);
                $request = $request->withQueryParams($merge);

            } elseif(!empty($queryParams['query_params'])) {
                $request = $request->withQueryParams($queryParams['query_params']);
            }
        }
        return $next($request, $response);
    }
}
