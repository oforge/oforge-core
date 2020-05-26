<?php

namespace FrontendUserPageBuilder\Controller;

//use CMS\Controller\Traits\BaseCmsBuilderTrait;
//use CMS\Enums\IncludePagePaths;
//use CMS\Exceptions\Message\CmsPageNotFoundException;
//use CMS\Exceptions\Message\CmsPagePathUrlPathExistException;
//use CMS\Helper\ApiDataResponseBuilder;
use Exception;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\AssetBundlesMode;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class BackendController
 *
 * @package FrontendUserPageBuilder/Controller/Frontend/FrontendUserPageBuilder
 * @EndpointClass(path="/page-builder", name="frontend_user_page_builder", assetScope="Frontend", assetBundlesMode=AssetBundlesMode::NONE)
 */
class FrontendUserPageBuilderController extends SecureFrontendController
{
    #use BaseCmsBuilderTrait;

    /**
     * BackendController constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct()
    {
        [EndpointAction::class, EndpointMethod::class, AssetBundlesMode::class];// Required for imports in nested traits
//        $this->initBaseCmsBuilderTrait(); // Required
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/app[/{context:.*}]", method=EndpointMethod::GET, assetBundles="CMS-Builder", assetBundlesMode=AssetBundlesMode::MERGE)
     */
    public function builderAction(Request $request, Response $response, array $args)
    {
        $context = ArrayHelper::get($args, 'context', '');
        $context = $context === '' ? [] : explode('/', $context);
        $contextCount = count($context);
        $cmsBuilderData = [];
        if ($contextCount === 0) {
            // builder start page
            $cmsBuilderData['view'] = 'index';
        } else {
            if ($context[0] === 'pages') {
                if ($contextCount === 3) {
                    $cmsBuilderData['view'] = 'pagePath';

                    $pageID = $context[1];
                    $pagePathID = $context[2];
                    $pageService = $this->cmsServices->PageService();
                    $page = $pageService->getByID($pageID);
                    try {
                        $pageService->checkPageExists($page, $pageID);
                    } catch (CmsPageNotFoundException $exception) {
                        Oforge()->View()->Flash()->addMessage('error', $exception->getMessage());

                        return $this->builderRedirect($response, 'pages');
                    }
                    $cmsBuilderData['page'] = $pageService->toArray($page, IncludePagePaths::ALL);
                    $cmsBuilderData['pagePathID'] = $pagePathID;
                } else {
                    $cmsBuilderData['view'] = 'pages';
                    $cmsBuilderData['languages'] = $this->prepareLanguageData();
                    $cmsBuilderData['pages'] = $this->preparePageTreeData();
                }
            }
        }
        Oforge()->View()->assign(['cms' => $cmsBuilderData]);

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/app[/{context:.*}]", name="builder_post", method=EndpointMethod::POST, assetBundlesMode=AssetBundlesMode::NONE)
     */
    public function builderPostAction(Request $request, Response $response, array $args)
    {
        $postData = $request->getParsedBody();
        $action = ArrayHelper::get($postData, 'action');
        $data = ArrayHelper::get($postData, 'data', []);
        try {
            switch ($action) {
                case 'add_page':
                    $name = $data['name'];
                    $parentPageID = $data['parentPageID'];
                    $parentPageID = empty($parentPageID) ? null : $parentPageID;
                    $this->cmsServices->PageService()->create($name, $parentPageID);
                    Oforge()->View()->Flash()->addMessage('success', I18N::translate('plugin_cms_api_page_create_success', [
                        'en' => 'Page was created.',
                        'de' => 'Seite wurde erstellt.',
                    ]));
                    break;
                case 'check_page_path':
                    $languageISO = $data['languageISO'];
                    $urlPath = $data['urlPath'];
                    try {
                        $this->cmsServices->PagePathService()->checkUrlPathExist($languageISO, $urlPath, null);
                        $exist = false;
                    } catch (CmsPagePathUrlPathExistException $exception) {
                        $exist = true;
                    }

                    return ApiDataResponseBuilder::assign($response, ['exist' => $exist]);
                    break;
                case 'clone_page':
                    $this->cmsServices->CloneService()->clonePage($data['pageID'], ArrayHelper::get($data, 'name'));
                    Oforge()->View()->Flash()->addMessage('success', I18N::translate('plugin_cms_api_page_clone_success', [
                        'en' => 'Page was cloned.',
                        'de' => 'Seite wurde geklont.',
                    ]));
                    break;
                case 'move_page':
                    $pageID = $data['pageID'];
                    $parentPageID = $data['parentPageID'];
                    $parentPageID = empty($parentPageID) ? null : $parentPageID;
                    $this->cmsServices->PageService()->update($pageID, ['parentPageID' => $parentPageID]);
                    Oforge()->View()->Flash()->addMessage('success', I18N::translate('plugin_cms_api_page_update_success', [
                        'en' => 'Page was updated.',
                        'de' => 'Seite wurde aktualisiert.',
                    ]));
                    break;
                case 'remove_page':
                    $pageID = $data['pageID'];
                    $this->cmsServices->PageService()->remove($pageID);
                    Oforge()->View()->Flash()->addMessage('success', I18N::translate('plugin_cms_api_page_remove_success', [
                        'en' => 'Page was removed.',
                        'de' => 'Seite wurde entfernt.',
                    ]));
                    break;
                case 'rename_page':
                    $pageID = $data['pageID'];
                    $name = $data['name'];
                    $this->cmsServices->PageService()->update($pageID, ['name' => $name]);
                    Oforge()->View()->Flash()->addMessage('success', I18N::translate('plugin_cms_api_page_rename_success', [
                        'en' => 'Page was renamed.',
                        'de' => 'Seite wurde umbenannt.',
                    ]));
                    break;
                case 'add_page_path':
                    $pageID = $data['pageID'];
                    $languageISO = $data['languageISO'];
                    $urlPath = $data['urlPath'];
                    $this->cmsServices->PagePathService()->create($pageID, $languageISO, ['urlPath' => $urlPath]);
                    Oforge()->View()->Flash()->addMessage('success', I18N::translate('plugin_cms_api_page_path_create_success', [
                        'en' => 'Page path was created.',
                        'de' => 'Seitenpfad wurde erstellt.',
                    ]));
                    break;
                case 'clone_page_path':
                    $pagePathID = $data['pagePathID'];
                    $languageISO = $data['languageISO'];
                    $this->cmsServices->CloneService()->clonePagePath($pagePathID, false, $languageISO);
                    Oforge()->View()->Flash()->addMessage('success', I18N::translate('plugin_cms_api_page_path_clone_success', [
                        'en' => 'Page path was cloned.',
                        'de' => 'Seitenpfad wurde geklont.',
                    ]));
                    break;
                case 'remove_page_path':
                    $pagePathID = $data['pagePathID'];
                    $this->cmsServices->PagePathService()->remove($pagePathID);
                    Oforge()->View()->Flash()->addMessage('success', I18N::translate('plugin_cms_api_page_path_remove_success', [
                        'en' => 'Page path was removed.',
                        'de' => 'Seitenpfad wurde entfernt.',
                    ]));
                    break;
                case null:
                default:
                    break;
            }
        } catch (Exception $exception) {
            Oforge()->View()->Flash()->addExceptionMessage('error', $exception->getMessage(), $exception);
        }
        // return ApiDataResponseBuilder::assign($response, [
        //     'args' => $args,
        //     'body' => $postData,
        // ]);

        return $this->builderRedirect($response, 'pages');
    }

    public function initPermissions()
    {
        $this->secureControllerActions[] = 'builderPostAction';
        $this->ensurePermissions($this->secureControllerActions, BackendUser::ROLE_ADMINISTRATOR);
        // $this->ensurePermissions($this->secureControllerActions, BackendUser::ROLE_PUBLIC);//TODO Uncomment for VUE npm:serve
    }

    /** @inheritDoc */
    protected function getBuilderContextArea(): string
    {
        return 'frontend';
    }

}
