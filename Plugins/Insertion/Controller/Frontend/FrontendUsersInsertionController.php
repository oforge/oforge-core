<?php

namespace Insertion\Controller\Frontend;

use Doctrine\ORM\ORMException;
use FastRoute\Route;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Models\UserDetail;
use FrontendUserManagement\Services\FrontendUserService;
use FrontendUserManagement\Services\UserDetailsService;
use FrontendUserManagement\Services\UserService;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionProfile;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Models\InsertionUserSearchBookmark;
use Insertion\Services\InsertionBookmarkService;
use Insertion\Services\InsertionListService;
use Insertion\Services\InsertionProfileService;
use Insertion\Services\InsertionSearchBookmarkService;
use Insertion\Services\InsertionService;
use Insertion\Services\InsertionTypeService;
use Insertion\Services\InsertionUpdaterService;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Services\UrlService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\RouterInterface;
use Slim\Router;

/**
 * Class FrontendUsersInsertionController
 *
 * @package Insertion\Controller\Frontend
 * @EndpointClass(path="/account/insertions", name="frontend_account_insertions", assetScope="Frontend")
 */
class FrontendUsersInsertionController extends SecureFrontendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        /**
         * @var $insertionListService InsertionListService
         */
        $insertionListService = Oforge()->Services()->get("insertion.list");

        /**
         * @var $userService FrontendUserService
         */
        $userService = Oforge()->Services()->get("frontend.user");
        $user        = $userService->getUser();
        /* neue Methode mit ALl als param*/
        $result = ["insertions" => $insertionListService->getUserInsertionsAll($user)];

        Oforge()->View()->assign($result);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     */
    public function pageAction(Request $request, Response $response) {
        /**
         * @var $insertionListService InsertionListService
         */
        $insertionListService = Oforge()->Services()->get("insertion.list");

        $page = isset($_GET["page"]) ? $_GET["page"] : 1;
        /**
         * @var $userService FrontendUserService
         */
        $userService = Oforge()->Services()->get("frontend.user");
        $user        = $userService->getUser();

        $result = ["insertions" => $insertionListService->getUserInsertions($user, $page)];

        Oforge()->View()->assign($result);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/delete/{id}")
     */
    public function deleteAction(Request $request, Response $response, $args) {
        /** @var $service InsertionService */
        $service   = Oforge()->Services()->get("insertion");
        $id        = $args["id"];
        $insertion = $service->getInsertionById(intval($id));
        Oforge()->View()->assign(['insertion' => $insertion->toArray(3, ['user' => ['*', '!id']])]);

        if ($request->isPost()) {
            /** @var Router $router */
            $router = Oforge()->App()->getContainer()->get('router');
            $url    = $router->pathFor('frontend_account_insertions');;

            return $this->modifyInsertion($request, $response, $args, 'delete', $url);
        }

    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/video/{id}")
     */
    public function deleteVideoAction(Request $request, Response $response, $args) {
        if ($request->isDelete()) {
            $videoId = $args['id'];
            /** @var InsertionUpdaterService $updaterService */
            $updaterService = Oforge()->Services()->get('insertion.updater');
            $updaterService->deleteInsertionMediaByContentId($videoId);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function resetProfileImageAction(Request $request, Response $response) {
        /** @var UserDetailsService $userDetailService */
        $userDetailService = Oforge()->Services()->get('frontend.user.management.user.details');

        $userId = Oforge()->View()->get('current_user')['id'];

        if (isset($userId)) {
            $userDetail = $userDetailService->get($userId);
            if (isset($userDetail)) {
                $userDetail->resetImage();
                $userDetailService->entityManager()->update($userDetail);
            }
        }

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $url    = $router->pathFor('frontend_account_insertions_profile');

        return $response->withRedirect($url, 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function resetProfileBackgroundAction(Request $request, Response $response) {
        /** @var InsertionProfileService $insertionProfileService */
        $insertionProfileService = Oforge()->Services()->get('insertion.profile');

        $userId = Oforge()->View()->get('current_user')['id'];

        if (isset($userId)) {
            $insertionProfile = $insertionProfileService->get($userId);
            if (isset($insertionProfile)) {
                $insertionProfile->resetBackground();
                $insertionProfileService->entityManager()->update($insertionProfile);
            }
        }

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $url    = $router->pathFor('frontend_account_insertions_profile');

        return $response->withRedirect($url, 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/activate/{id}")
     */
    public function activateAction(Request $request, Response $response, $args) {
        return $this->modifyInsertion($request, $response, $args, 'activate');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/disable/{id}")
     */
    public function disableAction(Request $request, Response $response, $args) {
        /** @var User $user */
        /** @var UserService $frontendUserService */
        $user = Oforge()->View()->get('current_user');

        /** @var UserDetailsService $userDetailService */
        $userDetailService = Oforge()->Services()->get('frontend.user.management.user.details');
        $userDetails       = $userDetailService->get($user['id']);
        $mailService       = Oforge()->Services()->get('mail');
        $mailOptions       = [
            'to'       => [$user['email'] => $user['email']],
            'from'     => 'no_reply',
            'subject'  => I18N::translate('mailer_subject_deactivation_confirm', 'Oforge | Your Deactivation was successful'),
            'template' => 'DeactivationConfirm.twig',
        ];
        $templateData      = [
            'receiver_name' => $userDetails->getNickName(),
            'sender_mail'   => $mailService->getSenderAddress('no_reply'),
        ];

        $mailService->send($mailOptions, $templateData);

        return $this->modifyInsertion($request, $response, $args, 'disable');
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function bookmarksAction(Request $request, Response $response) {
        /**
         * @var $userService FrontendUserService
         */
        $userService = Oforge()->Services()->get("frontend.user");
        $user        = $userService->getUser();

        /**
         * @var $bookmarkService InsertionBookmarkService
         */
        $bookmarkService = Oforge()->Services()->get("insertion.bookmark");

        $bookmarks = $bookmarkService->list($user);

        Oforge()->View()->assign([
            "bookmarks"  => $bookmarks,
            "animations" => Oforge()->View()->Flash()->getData('animations'),
        ]);
        Oforge()->View()->Flash()->clearData('animations');
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function searchBookmarksAction(Request $request, Response $response) {
        /**
         * @var $userService FrontendUserService
         */
        $userService = Oforge()->Services()->get("frontend.user");
        $user        = $userService->getUser();

        /** @var $searchBookmarkService InsertionSearchBookmarkService */
        $searchBookmarkService = Oforge()->Services()->get("insertion.search.bookmark");

        $bookmarks = $searchBookmarkService->list($user);

        $result = [];
        if (isset($bookmarks) && sizeof($bookmarks) > 0) {
            foreach ($bookmarks as $bookmark) {
                $data = $bookmark->toArray(1);

                $data["url"] = $searchBookmarkService->getUrl($data["insertionType"]["id"], $data["params"]);
                $result[]    = $data;
            }
        }

        $bookmarks = $result;

        /**
         * @var $typeService InsertionTypeService
         */
        $typeService = Oforge()->Services()->get("insertion.type");

        $attributeMap = $typeService->getInsertionTypeAttributeMap();

        $types    = $typeService->getInsertionTypeList(100, 0);
        $valueMap = [];
        foreach ($types as $type) {
            /**
             * @var $attribute InsertionTypeAttribute
             */
            foreach ($type->getAttributes() as $attribute) {
                $attributeMap[$attribute->getAttributeKey()->getId()] = [
                    "name" => $attribute->getAttributeKey()->getName(),
                    "top"  => $attribute->isTop(),
                ];

                foreach ($attribute->getAttributeKey()->getValues() as $value) {
                    $valueMap[$value->getId()] = $value->getValue();
                }
            }
        }

        Oforge()->View()->assign(["bookmarks" => $bookmarks, "values" => $valueMap, 'all_attributes' => $attributeMap]);

    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/toggle-bookmark/{insertionId:\d+}")
     */
    public function toggleBookmarkAction(Request $request, Response $response, $args) {
        $id = (int) $args["insertionId"];
        /**
         * @var InsertionService $service
         */
        $service = Oforge()->Services()->get("insertion");
        /** @var Insertion $insertion */
        $insertion = $service->getInsertionById($id);

        if (!isset($insertion)) {
            return $response->withRedirect("/404", 301);
        }

        /**
         * @var FrontendUserService $userService
         */
        $userService = Oforge()->Services()->get("frontend.user");
        $user        = $userService->getUser();

        /**
         * @var $bookmarkService InsertionBookmarkService
         */
        $bookmarkService = Oforge()->Services()->get("insertion.bookmark");

        $bookmarkService->toggle($insertion, $user);

        Oforge()->View()->Flash()->setData('animations', ['heartbeat' => true]);

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $url    = $router->pathFor('insertions_detail', ["id" => $id]);

        $refererHeader = $request->getHeader('HTTP_REFERER');
        if (isset($refererHeader) && sizeof($refererHeader) > 0) {
            $url = $refererHeader[0];
        }

        return $response->withRedirect($url, 301);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function toggleSearchBookmarkAction(Request $request, Response $response) {
        $id = $request->getParsedBody()['type_id'];
        /**
         * @var $service InsertionTypeService
         */
        $service       = Oforge()->Services()->get("insertion.type");
        $insertionType = $service->getInsertionTypeById(intval($id));

        if (!isset($insertionType) || $insertionType == null) {
            return $response->withRedirect("/404", 301);
        }

        /**
         * @var $userService FrontendUserService
         */
        $userService = Oforge()->Services()->get("frontend.user");
        $user        = $userService->getUser();

        /**
         * @var $bookmarkService InsertionSearchBookmarkService
         */
        $bookmarkService = Oforge()->Services()->get("insertion.search.bookmark");

        if ($request->isPost()) {
            $filterData = $_POST["filter"];
            $params     = [];
            if (isset($filterData)) {
                $params = json_decode($filterData, true);
            }
            if ($params == null) {
                $params = [];
            }
            if ($bookmarkService->hasBookmark($id, $user->getId(), $params)) {
                $bookmarkService->toggle($insertionType, $user, $params);
            } else {
                /** @var UrlService $urlService */
                $urlService = Oforge()->Services()->get('url');
                $url        = $urlService->getUrl('frontend_account_insertions_saveSearchBookmark');

                if (isset($_SESSION['saveBookmark'])) {
                    unset($_SESSION['saveBookmark']);
                }

                $_SESSION['saveBookmark'] = [
                    'params' => $params,
                    'typeId' => $insertionType,
                ];

                return $response->withRedirect($url);
            }
        }

        $url = $bookmarkService->getUrl($id, $params);

        $refererHeader = $request->getHeader('HTTP_REFERER');
        if (isset($refererHeader) && sizeof($refererHeader) > 0) {
            $url = $refererHeader[0];
        }

        return $response->withRedirect($url, 301);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @EndpointAction(path="/saveSearchBookmark[/{id:\d+}]")
     */
    public function saveSearchBookmarkAction(Request $request, Response $response, array $args) {
        /** @var $userService FrontendUserService */
        $userService = Oforge()->Services()->get("frontend.user");
        $user        = $userService->getUser();

        // Update Notification interval block
        if (isset($args['id']) && is_numeric($args['id'])) {
            /** @var $bookmarkService InsertionSearchBookmarkService */
            /** @var InsertionUserSearchBookmark $bookmark */
            $bookmarkService = Oforge()->Services()->get("insertion.search.bookmark");
            $bookmark        = $bookmarkService->get($args['id']);

            if (is_null($bookmark) || $bookmark->getUser()->getId() !== $user->getId()) {
                return $response->withRedirect("/404", 301);
            }

            if ($request->isPost()) {
                $options = [
                    'name'     => $request->getParsedBodyParam('search_title'),
                    'interval' => $request->getParsedBodyParam('email_interval'),
                ];
                $bookmarkService->update($bookmark->getId(), $options);

                $referer = $_SESSION['saveBookmark']['referer'];
                unset($_SESSION['saveBookmark']);

                return $response->withRedirect($referer);
            }

            Oforge()->View()->assign(['bookmark' => $bookmark->toArray()]);
            $refererHeader = $request->getHeader('HTTP_REFERER');
            if (isset($refererHeader) && sizeof($refererHeader) > 0) {
                $_SESSION['saveBookmark']['referer'] = $refererHeader[0];
            }

            return $response;
        }

        // Create new Block
        if (!isset($_SESSION['saveBookmark'])
            || !isset($_SESSION['saveBookmark']['params'])
            || !isset($_SESSION['saveBookmark']['typeId'])
            || is_null($user)) {
            return $response->withRedirect("/404", 301);
        }

        $params        = $_SESSION['saveBookmark']['params'];
        $insertionType = $_SESSION['saveBookmark']['typeId'];

        if ($request->isPost()) {
            $options = [
                'name'     => $request->getParsedBodyParam('search_title'),
                'interval' => $request->getParsedBodyParam('email_interval'),
            ];

            if (is_null($options['name']) || is_null($options['interval'])) {
                /** @var UrlService $urlService */
                $urlService = Oforge()->Services()->get('url');
                $url        = $urlService->getUrl('frontend_account_insertions_saveSearchBookmark');

                return $response->withRedirect($url);
            }

            /** @var $bookmarkService InsertionSearchBookmarkService */
            $bookmarkService = Oforge()->Services()->get("insertion.search.bookmark");
            $bookmarkService->toggle($insertionType, $user, $params, $options);

            $referer = $_SESSION['saveBookmark']['referer'];
            unset($_SESSION['saveBookmark']);

            Oforge()->View()->Flash()->addMessage('success', I18n::translate('frontend_save_search_bookmarks_success', [
                'en' => 'Bookmark successfully saved',
                'de' => 'Suche erfolgreich gespeichert.',
            ]));

            return $response->withRedirect($referer);
        }

        $refererHeader = $request->getHeader('HTTP_REFERER');
        if (isset($refererHeader) && sizeof($refererHeader) > 0) {
            $_SESSION['saveBookmark']['referer'] = $refererHeader[0];
        }

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @EndpointAction(path="/remove-searchBookmark/{searchBookmarkId:\d+}", method=EndpointMethod::POST)
     */
    public function removeSearchBookmarkAction(Request $request, Response $response, $args) {
        $id = $args["searchBookmarkId"];
        /** @var R $router */
        $router = Oforge()->App()->getContainer()->get('router');

        $url = $router->pathFor('frontend_account_insertions_searchBookmarks');

        /** @var InsertionSearchBookmarkService $searchBookmarkService */
        $searchBookmarkService = Oforge()->Services()->get('insertion.search.bookmark');
        /** @var InsertionUserSearchBookmark $searchBookmark */
        $searchBookmark = $searchBookmarkService->get($id);

        $user = Oforge()->View()->get('current_user');

        if (!isset($user['id'])) {
            return $response->withRedirect($url, 301);
        }

        if (!isset($searchBookmark)) {
            Oforge()->View()->Flash()->addMessage('error', I18N::translate('search_bookmark_entity_not_exist', [
                'en' => 'Bookmark entry does not exist.',
                'de' => 'Sucheintrag existiert nicht.',
            ]));

            return $response->withRedirect($url, 301);
        }
        if ($user['id'] != $searchBookmark->getUser()->getId()) {
            Oforge()->View()->Flash()->addMessage('error', I18N::translate('search_bookmark_invalid_user', [
                'en' => 'Permission denied.',
                'de' => 'Keine Befugnis.',
            ]));

            return $response->withRedirect($url, 301);
        }

        $searchBookmarkService->remove($id);

        Oforge()->View()->Flash()->addMessage('success', I18n::translate('remove_search_bookmark_success', [
            'en' => 'Search entry has been deleted.',
            'de' => 'Sucheintrag wurde erfolgreich gelöscht.',
        ]));

        return $response->withRedirect($url, 301);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/removeBookmark/{id:\d+}")
     */
    public function removeBookmarkAction(Request $request, Response $response, $args) {
        /**
         * @var $bookmarkService InsertionBookmarkService
         */
        $bookmarkService = Oforge()->Services()->get("insertion.bookmark");
        $id              = $args["id"];

        if (!$bookmarkService->remove($id)) {
            return $response->withRedirect('/404');
        }

        Oforge()->View()->Flash()->addMessage('success', I18n::translate('remove_bookmark_success', [
            'en' => 'Insertion has been deleted.',
            'de' => 'Inserat wurde erfolgreich vom Merkzettel entfernt',
        ]));

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $url    = $router->pathFor('frontend_account_insertions_bookmarks');

        $refererHeader = $request->getHeader('HTTP_REFERER');
        if (isset($refererHeader) && sizeof($refererHeader) > 0) {
            $url = $refererHeader[0];
        }

        Oforge()->View()->Flash()->setData('animations', ['heartbeat' => true]);

        return $response->withRedirect($url, 301);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws \ReflectionException
     * @EndpointAction(path = "/profile")
     */
    public function profileAction(Request $request, Response $response) {
        /**
         * @var $userService FrontendUserService
         */
        $userService = Oforge()->Services()->get("frontend.user");

        $user = $userService->getUser();
        /**
         * @var $insertionProfileService InsertionProfileService
         */
        $insertionProfileService = Oforge()->Services()->get("insertion.profile");

        if ($request->isPost() && $user !== null) {
            $insertionProfileService->update($user, $_POST);

            if (isset($_FILES["profile"])) {
                /**
                 * @var UserDetailsService $userDetailsService
                 */
                $userDetailsService = Oforge()->Services()->get('frontend.user.management.user.details');

                $user = $userDetailsService->updateImage($user, $_FILES["profile"]);
            }

            /**
             * Update Session with new User Data
             */

            /** @var  AuthService $authService */
            $authService   = Oforge()->Services()->get('auth');
            $user2         = $user->toArray(1, ['password']);
            $user2["type"] = User::class;
            $jwt           = $authService->createJWT($user2);

            $_SESSION['auth'] = $jwt;
        }

        $result = $insertionProfileService->get($user->getId());

        $user = $user->toArray(1, ['password']);

        Oforge()->View()->assign(["profile" => $result != null ? $result->toArray() : null, "user" => $user]);
    }

    public function initPermissions() {
        $this->ensurePermissions([
            'accountListAction',
            'bookmarksAction',
            'searchBookmarksAction',
            'modifyInsertion',
            'disableAction',
            'pageAction',
            'deleteAction',
            'deleteVideoAction',
            'activateAction',
            'deleteAction',
            'indexAction',
            'toggleBookmarkAction',
            'toggleSearchBookmarkAction',
            'saveSearchBookmarkAction',
            'profileAction',
            'removeBookmarkAction',
            'removeSearchBookmarkAction',
            'resetProfileImageAction',
            'resetProfileBackgroundAction',
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @param string $action
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    private function modifyInsertion(Request $request, Response $response, $args, string $action, string $redirectUrl = null) {
        /** @var $service InsertionService */
        $service   = Oforge()->Services()->get("insertion");
        $id        = $args["id"];
        /**
         * @var $insertion Insertion
         */
        $insertion = $service->getInsertionById(intval($id));

        /**
         * @var $userService FrontendUserService
         */
        $userService = Oforge()->Services()->get("frontend.user");
        $user        = $userService->getUser();

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect("/404", 301);
        }

        if ($user == null || $insertion->getUser()->getId() != $user->getId()) {
            return $response->withRedirect("/401", 301);
        }

        /**
         * @var $updateService InsertionUpdaterService
         */
        $updateService = Oforge()->Services()->get("insertion.updater");

        switch ($action) {
            case "disable":
                $updateService->deactivate($insertion);
                break;
            case "delete":
                $insertion->setDeactivationCause($request->getParsedBody()["deletecause"]);
                $updateService->updateInseration($insertion);

                $updateService->delete($insertion);
                break;
            case "activate":
                $updateService->activate($insertion);
                break;
        }

        $refererHeader = $request->getHeader('HTTP_REFERER');

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $url    = $router->pathFor('frontend_account_insertions');;

        if (isset($redirectUrl)) {
            $url = $redirectUrl;
        } else if (isset($refererHeader) && sizeof($refererHeader) > 0) {
            $url = $refererHeader[0];
        }

        Oforge()->View()->Flash()->addMessage("success", I18N::translate("insertion_" . $action));

        return $response->withRedirect($url, 301);
    }
}
