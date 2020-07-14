<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Core\Middleware;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateManagementService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AssetsMiddleware
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Core\Middleware
 */
class AssetsMiddleware {

    /**
     * Middleware call before the controller call
     *
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     */
    public function prepend($request, $response) {
        // /** @var TemplateManagementService $templateManagementService */
        // $templateManagementService = Oforge()->Services()->get('template.management');
        // try {
        //     $templateName = $templateManagementService->getActiveTemplate()->getName();
        // } catch (\Exception $exception) {
        //     Oforge()->Logger()->logException($exception);
        //     $templateName = Statics::DEFAULT_THEME;
        // }

        $assetBundles = Oforge()->View()->get('meta.route.assetBundles');

        if (!empty($assetBundles)) {
            $data = [
                'assets'     => [
                    'js'  => Oforge()->Services()->get('assets.js')->getUrl($assetBundles[0]),
                    'css' => Oforge()->Services()->get('assets.css')->getUrl($assetBundles[0]),
                ],
                // 'meta.theme' => $templateName,
            ];
            Oforge()->View()->assign($data);
        }
    }

}
