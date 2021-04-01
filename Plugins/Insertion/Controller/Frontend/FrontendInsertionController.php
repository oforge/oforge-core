<?php

namespace Insertion\Controller\Frontend;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Services\FrontendUserService;
use Helpdesk\Services\HelpdeskTicketService;
use Insertion\Models\AttributeKey;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Services\AttributeService;
use Insertion\Services\InsertionCreatorService;
use Insertion\Services\InsertionFeedbackService;
use Insertion\Services\InsertionFormsService;
use Insertion\Services\InsertionListService;
use Insertion\Services\InsertionProfileService;
use Insertion\Services\InsertionSeoService;
use Insertion\Services\InsertionService;
use Insertion\Services\InsertionTypeService;
use Insertion\Services\InsertionUpdaterService;
use Insertion\Services\InsertionValidationService;
use Messenger\Models\Conversation;
use Messenger\Services\FrontendMessengerService;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Manager\Events\Event;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Services\LanguageService;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Services\UrlService;
use ReflectionException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class MessengerController
 *
 * @package Messenger\Controller\Frontend
 * @EndpointClass(path="/insertions", name="insertions", assetScope="Frontend")
 */
class FrontendInsertionController extends SecureFrontendController {

    public function initPermissions() {
        $this->ensurePermissions([
            'accountListAction',
            'reportAction',
            'feedbackAction',
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/create")
     */
    public function createAction(Request $request, Response $response) {
        /** @var InsertionTypeService $service */
        $service = Oforge()->Services()->get('insertion.type');

        $types = $service->getInsertionTypeTree();

        Oforge()->View()->assign(['types' => $types]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/search")
     */
    public function listAllAction(Request $request, Response $response) {
        /** @var InsertionTypeService $service */
        $service = Oforge()->Services()->get('insertion.type');

        $types = $service->getInsertionTypeTree();

        Oforge()->View()->assign(['types' => $types]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/create/{type}/{page}")
     */
    public function createStepsAction(Request $request, Response $response, $args) {
        $page   = intval($args['page']);
        $typeId = intval($args['type']);

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $result = [
            'page'      => $page,
            'pagecount' => 5,
        ];

        /** @var FrontendUserService $userService */
        $userService = Oforge()->Services()->get('frontend.user');

        /** @var InsertionFormsService $formsService */
        $formsService = Oforge()->Services()->get('insertion.forms');

        if ($page > 1 && !$userService->isLoggedIn()) {
            $uri = $router->pathFor('insertions_createSteps', ['type' => $typeId, 'page' => '1']);

            $data           = $formsService->getProcessedData($typeId);
            $result['data'] = $data;

            Oforge()->View()->assign($result);

            return $response->withRedirect($uri, 301);
        }

        $user = $userService->getUser();

        if ($page == 3) {
            /** @var InsertionService $insertionService */
            $insertionService = Oforge()->Services()->get('insertion');

            if (isset($user)) {
                /** @var Insertion $lastInsertion */
                $lastInsertion = $insertionService->getLatestInsertionOfUser($user->getId());

                if (isset($lastInsertion)) {
                    $result['lastContact'] = $lastInsertion->getContact()->toArray(1);
                }
            }
        }

        /** @var InsertionTypeService $service */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');

        $type                     = $insertionTypeService->getInsertionTypeById($typeId);
        $result['type']           = $type->toArray();
        $typeAttributes           = $insertionTypeService->getInsertionTypeAttributeTree($typeId);
        $result['attributes']     = $typeAttributes;
        $result['all_attributes'] = $insertionTypeService->getInsertionTypeAttributeMap();

        /** @var InsertionCreatorService $createService */
        $createService = Oforge()->Services()->get('insertion.creator');

        if ($request->isPost()) {
            Oforge()->Logger()->get('create')->info("log data ", $_POST);
            $formsService->processPostData($typeId);
        }

        $data           = $formsService->getProcessedData($typeId);
        $result['data'] = $data;

        Oforge()->View()->assign($result);

        /** @var InsertionValidationService $insertionValidationService */
        $insertionValidationService = Oforge()->Services()->get('insertion.validation');
        $redirectUrl                = '/insertions/create/' . $typeId . '/1';

        if ($page > 1 && !$insertionValidationService->titleExists()) {
            Oforge()->View()->Flash()->addMessage('error', I18N::translate('fill_title', [
                'de' => 'Bitte befülle den Titel',
                'en' => 'Please fill in the title',
            ]));
            $response = $response->withRedirect($redirectUrl, 303);

            return $response;
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/create/{type}")
     */
    public function createTypeAction(Request $request, Response $response, $args) {
        $typeId = $args['type'];

        /** @var InsertionTypeService $service */
        $service         = Oforge()->Services()->get('insertion.type');
        $types           = $service->getInsertionTypeTree($typeId);
        $result['types'] = $types;
        $result['type']  = $service->getInsertionTypeById($typeId)->toArray();
        Oforge()->View()->assign($result);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/process/{type}")
     */
    public function processStepsAction(Request $request, Response $response, $args) {
        $typeId = $args['type'];
        /** @var FrontendUserService $userService */
        $userService = Oforge()->Services()->get('frontend.user');
        $user        = $userService->getUser();
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');

        if (isset($user)) {
            /** @var InsertionCreatorService $createService */
            $createService = Oforge()->Services()->get('insertion.creator');

            /** @var InsertionFormsService $formsService */
            $formsService = Oforge()->Services()->get('insertion.forms');

            /** @var MailService $mailService */
            $mailService = Oforge()->Services()->get('mail');

            if ($request->isPost() || $request->isGet() && $_GET['success'] === 'true') {
                Oforge()->Logger()->get('create')->info('process data ', $_POST);

                $data = $formsService->processPostData($typeId);
                try {
                    $processData = $formsService->parsePageData($data);
                    Oforge()->Logger()->get('create')->info('processed data ', $data);

                    $insertionId = $createService->create($typeId, $user, $processData);
                    Oforge()->Events()->trigger(Event::create(Insertion::class . '::created', ["id" => $insertionId, "data" => $data]));

                    $insertionService = Oforge()->Services()->get('insertion');

                    /** @var Insertion $insertion */
                    $insertion = $insertionService->getInsertionById(intval($insertionId));
                    Oforge()->View()->assign(['last_insertion' => $insertion]);

                    if (isset($insertion)) {
                        try {
                            $mailService->sendInsertionCreateInfoMail($user, $insertion);
                        } catch (Exception $exception) {
                            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('confirmation_mail_not_sent', [
                                'en' => 'Confirmation could not be sent.',
                                'de' => 'Bestätigungs-Mail konnte nicht versandt werden.',
                            ]));
                        }
                    }

                    $uri = $router->pathFor('insertions_feedback');

                    $formsService->clearProcessedData($typeId);

                    Oforge()->View()->Flash()->addMessage('success', I18N::translate('insertion_created', [
                        'en' => 'Insertion creation was successful.',
                        'de' => 'Deine Inseratserstellung war erfolgreich.',
                    ]));

                    return $response->withRedirect($uri, 301);
                } catch (\Exception $exception) {
                    Oforge()->Logger()->get()->error('insertion_creation', $data);
                    Oforge()->Logger()->get()->error('insertion_creation_stack', $exception->getTrace());

                    Oforge()->View()->Flash()->addMessage('error', I18N::translate('server_error'));
                    $uri = $router->pathFor('insertions_createSteps', ['type' => $typeId, 'page' => '5']);

                    return $response->withRedirect($uri, 301);
                }
            }
        } else {
            Oforge()->View()->Flash()->addMessage('error', I18N::translate('missing_user'));
            $uri = $router->pathFor('insertions_createSteps', ['type' => $typeId, 'page' => '5']);

            return $response->withRedirect($uri, 301);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @EndpointAction(path="/feedback")
     */
    public function feedbackAction(Request $request, Response $response) {
        if ($request->isPost()) {
            /** @var InsertionFeedbackService $feedbackService */
            $feedbackService = Oforge()->Services()->get('insertion.feedback');
            $feedbackService->savePostData();

            /** @var Router $router */
            $router = Oforge()->App()->getContainer()->get('router');
            $uri    = $router->pathFor('insertions_success');

            return $response->withRedirect($uri, 301);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     */

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/success")
     */
    public function successAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws DBALException
     * @EndpointAction(path="/search/{type}")
     */
    public function listingAction(Request $request, Response $response, $args) {
        $typeIdOrName = $args['type'];

        $result = [];

        /** @var InsertionTypeService $service */
        $service = Oforge()->Services()->get('insertion.type');
        /** @var InsertionType $type */
        $type = $service->getInsertionTypeByName($typeIdOrName);
        if ($type == null) {
            $type = $service->getInsertionTypeById($typeIdOrName);
        }

        if (!isset($type) || $type == null) {
            return $response->withRedirect('/404', 301);
        }

        $typeAttributes           = $service->getInsertionTypeAttributeTree($type->getId());
        $result['attributes']     = $typeAttributes;
        $result['all_attributes'] = $service->getInsertionTypeAttributeMap();
        $result['keys']           = [];
        $result['typeId']         = $args['type'];
        $result['type']           = $type->toArray(0);
        /** @var InsertionTypeAttribute $attribute */
        foreach ($type->getAttributes() as $attribute) {
            $key                             = $attribute->getAttributeKey();
            $result['keys'][$key->getName()] = $key->toArray(0);
        }

        /** @var InsertionListService $listService */
        $listService = Oforge()->Services()->get('insertion.list');

        $radius = $listService->saveSearchRadius($request->getQueryParams());

        $result['search'] = $listService->search($type->getId(), array_merge($request->getQueryParams(), $radius));

        /** @var InsertionService $insertion */
        $insertionService = Oforge()->Services()->get('insertion');

        foreach ($result['search']['query']['items'] as &$insertionItem) {
            /** @var Insertion $insertion */
            $insertion = $insertionService->getInsertionById($insertionItem['id']);

            $insertionValues = [];
            foreach ($insertion->getValues() as $value) {
                if (isset($insertionValues[$value->getAttributeKey()->getId()])) {
                    if (is_array($insertionValues[$value->getAttributeKey()->getId()])) {
                        $insertionValues[$value->getAttributeKey()->getId()][] = $value->getValue();
                    } else {
                        $insertionValues[$value->getAttributeKey()->getId()] = [$insertionValues[$value->getAttributeKey()->getId()], $value->getValue()];
                    }
                } else {
                    $insertionValues[$value->getAttributeKey()->getId()] = $value->getValue();
                }
            }
            $insertionItem['insertion_values'] = $insertionValues;
        }

        Oforge()->View()->assign($result);
        if (Oforge()->View()->has('seo')) {
            $seo = Oforge()->View()->get('seo');
            if (isset($seo['url_id']) && isset($seo['url_name'])) {
                /** @var InsertionSeoService $insertionSeoService */
                $insertionSeoService = Oforge()->Services()->get('insertion.seo');
                $seoContents         = $insertionSeoService->getContentForUrl($seo['url_id']);
                if (!empty($seoContents)) {
                    Oforge()->View()->assign([
                        'seo' => [
                            'content' => $seoContents,
                        ],
                    ]);
                }
            }
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/detailsearch/{type}")
     */
    public function detailSearchAction(Request $request, Response $response, $args) {
        $typeIdOrName = $args['type'];
        /** @var InsertionTypeService $insertionTypeService */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');

        /** @var InsertionType $type */
        $type = $insertionTypeService->getInsertionTypeByName($typeIdOrName);
        if ($type == null) {
            $type = $insertionTypeService->getInsertionTypeById($typeIdOrName);
        }

        if (!isset($type) || $type == null) {
            return $response->withRedirect('/404', 301);
        }

        $typeAttributes           = $insertionTypeService->getInsertionTypeAttributeTree($type->getId());
        $result['attributes']     = $typeAttributes;
        $result['keys']           = [];
        $result['typeId']         = $args['type'];
        $result['type']           = $type->toArray(0);
        $result['all_attributes'] = $insertionTypeService->getInsertionTypeAttributeMap();

        /** @var InsertionTypeAttribute $attribute */
        foreach ($type->getAttributes() as $attribute) {
            $key                             = $attribute->getAttributeKey();
            $result['keys'][$key->getName()] = $key->toArray(0);
        }

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
     * @EndpointAction(path="/detail/{id}")
     */
    public function detailAction(Request $request, Response $response, $args) {
        $id = $args['id'];
        /** @var InsertionService $service */
        $service = Oforge()->Services()->get('insertion');
        /** @var Insertion $insertion */
        $insertion = $service->getInsertionById(intval($id));
        /** @var UrlService $urlService */
        $urlService = Oforge()->Services()->get('url');

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect('/404', 301);
        }

        $values = [];

        foreach ($insertion->toArray()['values'] as $value) {
            $id     = $value['attributeKey'];
            $values = $values + [$id => $value];
        }

        Oforge()->View()->assign(['values' => $values]);

        // if (!($insertion->isActive() && $insertion->isModeration())) {
        //     $auth = '';
        //     if (isset($_SESSION['auth'])) {
        //         $auth = $_SESSION['auth'];
        //     }
        //
        //     /** @var AuthService $authService */
        //     $authService = Oforge()->Services()->get('auth');
        //     $user        = $authService->decode($auth);
        //
        //     if ($user['type'] != BackendUser::class && $insertion->getUser()->getId() != $user['id']) {
        //         return $response->withRedirect('/404', 301);
        //     }
        // }

        /**
         *  Changed the criteria
         */
        if (!($insertion->isActive())) {
            $auth = '';
            if (isset($_SESSION['auth'])) {
                $auth = $_SESSION['auth'];
            }

            /** @var AuthService $authService */
            $authService = Oforge()->Services()->get('auth');
            $user        = $authService->decode($auth);

            if ($user['type'] != BackendUser::class && $insertion->getUser()->getId() != $user['id']) {
                $url = $urlService->getUrl('insertions_listing', ['type' => $insertion->getInsertionType()->getId()]);
                return $response->withRedirect($url, 301);
            }
        }

        Oforge()->View()->assign(['insertion' => $insertion->toArray(3, ['user' => ['*', '!id']])]);

        /** @var InsertionProfileService $service */
        $service = Oforge()->Services()->get('insertion.profile');

        $profile = $service->get($insertion->getUser()->getId());
        if (isset($profile)) {
            Oforge()->View()->assign(['profile' => $profile->toArray()]);
        }

        /** @var InsertionTypeService $insertionTypeService */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');

        $typeAttributes  = $insertionTypeService->getInsertionTypeAttributeTree($insertion->getInsertionType()->getId());
        $insertionValues = [];
        foreach ($insertion->getValues() as $value) {
            if (isset($insertionValues[$value->getAttributeKey()->getId()])) {
                if (is_array($insertionValues[$value->getAttributeKey()->getId()])) {
                    $insertionValues[$value->getAttributeKey()->getId()][] = $value->getValue();
                } else {
                    $insertionValues[$value->getAttributeKey()->getId()] = [$insertionValues[$value->getAttributeKey()->getId()], $value->getValue()];
                }
            } else {
                $insertionValues[$value->getAttributeKey()->getId()] = $value->getValue();
            }
        }

        $topValues = [];
        foreach ($typeAttributes as $attributeGroup) {
            foreach ($attributeGroup['items'] as $attribute) {
                if ($attribute['top'] == 'true') {
                    if (isset($insertionValues[$attribute['attributeKey']['id']])) {
                        $topValues[] = [
                            'name'         => $attribute['attributeKey']['name'],
                            'type'         => $attribute['attributeKey']['type'],
                            'filterType'   => $attribute['attributeKey']['filterType'],
                            'attributeKey' => $attribute['attributeKey']['id'],
                            'value'        => $insertionValues[$attribute['attributeKey']['id']],
                        ];
                    }
                }
            }
        }

        /** @var LanguageService $languageService */
        $languageService = Oforge()->Services()->get('i18n.language');

        /** @var Language[] $availableLanguages */
        $availableLanguages = $languageService->list(['active' => true]);
        foreach ($availableLanguages as &$language) {
            $language = $language->getIso();
        }
        Oforge()->View()->assign([
            'top_values'       => $topValues,
            'attributes'       => $typeAttributes,
            'all_attributes'   => $insertionTypeService->getInsertionTypeAttributeMap(),
            'insertion_values' => $insertionValues,
            'animations'       => Oforge()->View()->Flash()->getData('animations'),
            'languages'        => $availableLanguages,
        ]);
        Oforge()->View()->Flash()->clearData('animations');

    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/edit/{id}")
     */
    public function editAction(Request $request, Response $response, $args) {
        $id = $args['id'];
        /** @var InsertionService $service */
        $service = Oforge()->Services()->get('insertion');
        /** @var Insertion $insertion */
        $insertion = $service->getInsertionById(intval($id));

        /** @var InsertionTypeService $insertionTypeService */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');

        /** @var FrontendUserService $userService */
        $userService = Oforge()->Services()->get('frontend.user');
        $user        = $userService->getUser();

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect('/404', 301);
        }

        if ($user == null || $insertion->getUser()->getId() != $user->getId()) {
            return $response->withRedirect('/401', 301);
        }

        /** @var InsertionType $type */
        $type                     = $insertion->getInsertionType();
        $typeAttributes           = $insertionTypeService->getInsertionTypeAttributeTree($insertion->getInsertionType()->getId());
        $result['type']           = $type->toArray();
        $result['attributes']     = $typeAttributes;
        $result['keys']           = [];
        $result['all_attributes'] = $insertionTypeService->getInsertionTypeAttributeMap();
        /** @var InsertionTypeAttribute $attribute */
        foreach ($type->getAttributes() as $attribute) {
            /** @var AttributeKey $key */
            $key                             = $attribute->getAttributeKey();
            $result['keys'][$key->getName()] = $key->toArray(0);
        }

        /** @var InsertionUpdaterService $updateService */
        $updateService = Oforge()->Services()->get('insertion.updater');

        $result['data'] = $updateService->getFormData($insertion);
        /** @var InsertionFormsService $formsService */
        $formsService = Oforge()->Services()->get('insertion.forms');

        if ($request->isPost()) {
            $data = $formsService->processPostData('insertion' . $insertion->getId(), !empty($insertion->getMedia()));
            $data = $formsService->parsePageData($data);

            $updateService->update($insertion, $data);
            Oforge()->Events()->trigger(Event::create(Insertion::class . '::updated', ["id" => $insertion->getId(), "data" => $_POST]));

            $insertion      = $service->getInsertionById(intval($id));
            $result['data'] = $updateService->getFormData($insertion);
            $formsService->clearProcessedData('insertion' . $insertion->getId());
        }

        $result['insertion'] = $insertion->toArray(1);

        Oforge()->View()->assign($result);
        Oforge()->View()->Flash()->addMessage('success',
            I18N::translate('insertion_edit_success', ['en' => 'Insertion successfully updated', 'de' => 'Das Inserat wurde erfolgreich bearbeitet']));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/profile/{id}")
     */
    public function profileAction(Request $request, Response $response, $args) {
        /** @var InsertionProfileService $service */
        $service = Oforge()->Services()->get('insertion.profile');

        $result = $service->getById($args['id']);

        if ($result == null) {
            return $response->withRedirect('/404', 301);
        }

        /** @var InsertionListService $listService */
        $listService = Oforge()->Services()->get('insertion.list');
        $insertions  = $listService->getUserInsertions($result->getUser()->getId(), 1, 20);

        Oforge()->View()->assign(['profile' => $result->toArray(), 'insertions' => $insertions]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     * @throws ServiceNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException
     * @throws \Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @EndpointAction(path="/contact/{id}")
     */
    public function contactAction(Request $request, Response $response, $args) {
        $id = $args['id'];
        /** @var InsertionService $insertionService */
        $insertionService = Oforge()->Services()->get('insertion');
        /** @var Insertion $insertion */
        $insertion = $insertionService->getInsertionById(intval($id));

        /*
         * added stuff to avoid User to see the Contact-Infos when they are not loged in
         */
        /** @var FrontendUserService $userService */
        $userService = Oforge()->Services()->get('frontend.user');
        $user = $userService->getUser();
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $securedVisibility = $insertion->getContact()->isVisible();

        if (($user === null) && ($securedVisibility === true) ) {
            $uri = $router->pathFor('frontend_login');
            Oforge()->View()->Flash()->addMessage("error", I18N::translate("insertion_contact_attempt_login_required", [
                "de" => "Diese Seite ist nur für eingeloggte Nutzer verfügbar.",
                "en" => "This site is only available for logged in users."
            ]));
            $_SESSION['login_redirect_url'] = $request->getUri();
            return $response->withRedirect($uri, 302);
        }
        /*
         * End of new stuff
         */

        $insertionService->countUpInsertionsContactAttempt(intval($id));

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect('/404', 301);
        }

        if ($request->isPost()) {
            $body    = $request->getParsedBody();
            $message = $body['message'];

            /** @var FrontendUserService $userService */
            $userService = Oforge()->Services()->get('frontend.user');

            $user = $userService->getUser();

            /** @var Router $router */
            $router = Oforge()->App()->getContainer()->get('router');

            if ($user === null) {
                $uri = $router->pathFor('frontend_login');

                return $response->withRedirect($uri, 302);
            }

            /** @var FrontendMessengerService $messengerService */
            $messengerService = Oforge()->Services()->get('frontend.messenger');
            /** @var Conversation $conversation */
            $conversation = $messengerService->checkForConversation($user->getId(), $insertion->getUser()->getId(), 'insertion', $insertion->getId());

            if (is_null($conversation)) {
                $data = [
                    'requester'    => $user->getId(),
                    'requested'    => $insertion->getUser()->getId(),
                    'type'         => 'insertion',
                    'targetId'     => $insertion->getId(),
                    'title'        => $insertion->getContent()[0]->getTitle(),
                    'firstMessage' => $message,
                ];

                $conversation = $messengerService->createNewConversation($data);

                /** @var MailService $mailService */
                $mailService = Oforge()->Services()->get('mail');

                $mailService->sendNewMessageInfoMail($insertion->getUser()->getId(), $conversation->getId());
            }

            $uri = $router->pathFor('frontend_account_messages', ['id' => $conversation->getId()]);

            return $response->withRedirect($uri, 302);
        }

        $data              = $insertion->toArray(2);
        $data["topvalues"] = [];
        /** @var InsertionTypeAttribute $attribute */
        foreach ($insertion->getInsertionType()->getAttributes() as $attribute) {
            if ($attribute->isTop()) {
                foreach ($data['values'] as $value) {
                    if ($value['attributeKey'] == $attribute->getAttributeKey()->getId()) {
                        $data['topvalues'][] = [
                            'name'         => $attribute->getAttributeKey()->getName(),
                            'type'         => $attribute->getAttributeKey()->getType(),
                            'attributeKey' => $attribute->getAttributeKey()->getId(),
                            'value'        => $value['value'],
                        ];
                    }
                }
            }
        }

        Oforge()->View()->assign(['insertion' => $data]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @throws ReflectionException
     * @EndpointAction(path="/report/{id}")
     */
    public function reportAction(Request $request, Response $response, $args) {
        /** @var HelpdeskTicketService $crud */
        $helpdeskService = Oforge()->Services()->get('helpdesk.ticket');
        $reportTypes     = $helpdeskService->getIssueTypesByGroup('report');

        $id = $args['id'];
        /** @var InsertionService $service */
        $insertionService = Oforge()->Services()->get('insertion');
        /** @var Insertion $insertion */
        $insertion = $insertionService->getInsertionById(intval($id));

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect('/404', 301);
        }

        if ($request->isPost()) {
            $body      = $request->getParsedBody();
            $issueType = $body['issueType'];
            $message   = $body['message'];

            /** @var FrontendUserService $userService */
            $userService = Oforge()->Services()->get('frontend.user');
            $user        = $userService->getUser();

            /** @var Router $router */
            $router = Oforge()->App()->getContainer()->get('router');

            if (is_null($user)) {
                $uri = $router->pathFor('frontend_login');

                return $response->withRedirect($uri, 302);
            }

            /** @var HelpdeskTicketService $helpdeskService */
            $helpdeskService = Oforge()->Services()->get('helpdesk.ticket');

            $conversation = $helpdeskService->createNewTicket($user->getId(), $issueType, 'Report of Insertion: ' . $insertion->getId(), $message);

            $uri = $router->pathFor('frontend_account_messages', ['id' => $conversation->getId()]);

            return $response->withRedirect($uri, 302);
        }

        $data              = $insertion->toArray(2);
        $data['topvalues'] = [];
        /** @var InsertionTypeAttribute $attribute */
        foreach ($insertion->getInsertionType()->getAttributes() as $attribute) {
            if ($attribute->isTop()) {
                foreach ($data['values'] as $value) {
                    if ($value['attributeKey'] == $attribute->getAttributeKey()->getId()) {
                        $data['topvalues'][] = [
                            'name'         => $attribute->getAttributeKey()->getName(),
                            'type'         => $attribute->getAttributeKey()->getType(),
                            'attributeKey' => $attribute->getAttributeKey()->getId(),
                            'value'        => $value['value'],
                        ];
                    }
                }
            }
        }

        Oforge()->View()->assign([
            'insertion'   => $data,
            'reportTypes' => $reportTypes,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/all_attribute_values")
     */
    public function getAllAttributeValuesAction(Request $request, Response $response) {
        $queryParams = $request->getQueryParams();

        /** @var AttributeService $attributeService */
        $attributeService = Oforge()->Services()->get('insertion.attribute');
        $attributeValues  = $attributeService->getAllAttributeValues($queryParams['attributekeys']);
        Oforge()->View()->assign(['json' => $attributeValues]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/find_by_id")
     */
    public function findByIdAction(Request $request, Response $response) {
        $id = $request->getQueryParam('id');

        /** @var InsertionService $insertionService */
        $insertionService = Oforge()->Services()->get('insertion');

        /** @var Insertion $insertion */
        $insertion = $insertionService->getInsertionById($id);

        /** @var UrlService $urlService */
        $urlService = Oforge()->Services()->get('url');

        if (!isset($insertion)) {
            Oforge()->View()->Flash()->addMessage('error', I18N::translate('insertion_not_exist', [
                'en' => 'The provided ID does not belong to an insertion.',
                'de' => 'Zu der gegebenen ID existiert kein Inserat.',
            ]));

            return $response->withRedirect('/');
        }
        if ($insertion->isDeleted()) {
            Oforge()->View()->Flash()->addmessage('error', I18N::translate('insertion_not_available', [
                'en' => 'The insertion you are looking for is not available anymore.',
                'de' => 'Das von dir gesuchte Inserat ist leider nicht mehr verfügbar.',
            ]));

            return $response->withRedirect('/');
        }

        $url = $urlService->getUrl('insertions_detail', ['id' => $id]);

        return $response->withRedirect($url);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/add_view")
     */
    public function trackViewAction(Request $request, Response $response) {
        if ($request->isPost()) {
            $id = $request->getParsedBody()['id'];
            /** @var InsertionService $insertionService */
            $insertionService = Oforge()->Services()->get('insertion');
            $insertionService->countUpInsertionViews($id);
        }
    }
}
