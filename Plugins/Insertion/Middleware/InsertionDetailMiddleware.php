<?php

namespace Insertion\Middleware;

use Doctrine\ORM\ORMException;
use Insertion\Models\Insertion;
use Insertion\Services\InsertionProfileService;
use Insertion\Services\InsertionService;
use Interop\Container\Exception\ContainerException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class InsertionDetailMiddleware
 *
 * @package Insertion\Middleware
 */
class InsertionDetailMiddleware {

    /**
     * @param $request
     * @param $response
     * @param $next
     *
     * @return mixed
     * @throws ORMException
     * @throws ContainerException
     * @throws ServiceNotFoundException
     */
    public function __invoke($request, $response, $next) {
        $uri  = $request->getUri();
        $path = $uri->getPath();

        //Split path into chunks
        $pathChunks = explode('/', $path, 5);

        if (count($pathChunks) > 3 && is_numeric($pathChunks[3])) {
            $relativePath = isset($pathChunks[4]) ? ('/' . $pathChunks[4]) : '';

            /** @var InsertionService $insertionService */
            $insertionService = Oforge()->Services()->get('insertion');
            /** @var InsertionProfileService $insertionProfileService */
            $insertionProfileService = Oforge()->Services()->get('insertion.profile');
            /** @var Insertion|null $insertion */
            $insertion = $insertionService->getInsertionById($pathChunks[3]);
            $router    = Oforge()->App()->getContainer()->get('router');

            if ($insertion !== null) {
                $typeTitle = str_replace(' ', '-', strtolower(I18N::translate($insertion->getInsertionType()->getName())));
                if ($pathChunks[1] === $typeTitle) {
                    $title = str_replace(' ', '-', strtolower($insertion->getContent()[0]->getTitle()));
                    $title = str_replace('/', '', $title);
                    if ($pathChunks[2] !== urlencode($title)) {
                        $url = '/' . urlencode($typeTitle) . '/' . urlencode($title) . '/' . $insertion->getId();

                        return $response->withRedirect($url, 301);
                    }

                    $result = $router->pathFor('insertions_detail', ['id' => $pathChunks[3]]);

                    $newUri  = $uri->withPath($result);
                    $request = $request->withUri($newUri);
                }
            }

            $profile = $insertionProfileService->getById($pathChunks[3]);
            if ($profile !== null) {
                if ($pathChunks[1] === urlencode(I18N::translate('insertion_url_profile'))) {
                    $title = urlencode(str_replace(' ', '-', strtolower($profile->getImprintName())));
                    $title = str_replace('/', '', $title);

                    if ($pathChunks[2] === $title) {
                        $result  = $router->pathFor('insertions_profile', ['id' => $pathChunks[3]]) . $relativePath;
                        $newUri  = $uri->withPath($result);
                        $request = $request->withUri($newUri);
                    }
                }
            }
        }

        return $next($request, $response);
    }

}
