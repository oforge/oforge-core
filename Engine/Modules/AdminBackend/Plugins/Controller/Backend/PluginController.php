<?php

namespace Oforge\Engine\Modules\AdminBackend\Plugins\Controller\Backend;

use Exception;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\CouldNotActivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\CouldNotDeactivatePluginException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\CouldNotInstallPluginException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginAlreadyActivatedException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginAlreadyInstalledException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginNotActivatedException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Plugin\PluginNotInstalledException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\Core\Services\PluginStateService;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroupByOrder;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\TemplateEngine\Core\Exceptions\InvalidScssVariableException;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateManagementService;
use Oforge\Engine\Modules\TemplateEngine\Core\Twig\TwigFlash;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PluginController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Plugins\Controller\Backend
 * @EndpointClass(path="/backend/plugins", name="backend_plugins", assetScope="Backend")
 */
class PluginController extends BaseCrudController {
    /** @var string $model */
    protected $model = Plugin::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'     => 'name',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => [
                'key'     => 'backend_crud_plugin_property_name',
                'default' => [
                    'en' => 'Plugin name',
                    'de' => 'Pluginname',
                ],
            ],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Backend/Plugin/Components/Index/NameColumn.twig',
            ],
        ],
        [
            'name'     => 'dependencies',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => [
                'key'     => 'backend_crud_plugin_property_dependencies',
                'default' => [
                    'en' => 'Dependencies',
                    'de' => 'Abhängigkeiten',
                ],
            ],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Backend/Plugin/Components/Index/DependenciesColumn.twig',
            ],
        ],
        [
            'name'     => 'action',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => [
                'key'     => 'backend_crud_plugin_property_action',
                'default' => [
                    'en' => 'Action',
                    'de' => 'Aktion',
                ],
            ],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Backend/Plugin/Components/Index/ActionColumn.twig',
            ],
        ],
    ];
    /** @var array $indexOrderBy */
    protected $indexOrderBy = [
        'name' => CrudGroupByOrder::ASC,
    ];
    /** @var array $crudActions */
    protected $crudActions = [
        'index'  => true,
        'create' => false,
        'view'   => false,
        'update' => false,
        'delete' => false,
    ];
    /** @var int|array<string,int> $crudPermission */
    protected $crudPermissions = BackendUser::ROLE_ADMINISTRATOR;

    public function __construct() {
        parent::__construct();
    }

    public function initPermissions() {
        parent::initPermissions();
        $this->ensurePermissions([
            'activateAction',
            'deactivateAction',
            'deleteAction',
            'installAction',
            'installActivateAction',
            'uninstallAction',
            'reactivateAction',
            'rebuildAction',
            'reinstallAction',
            'reinstallActivateAction',
        ], BackendUser::ROLE_ADMINISTRATOR);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response|void
     * @EndpointAction(path="/add")
     */
    public function addAction(Request $request, Response $response, array $args) {
        Oforge()->View()->Flash()->addMessage('info', 'Not implemented yet!');

        //TODO Implementation of PluginController#addAction, later

        return RouteHelper::redirect($response, 'backend_plugins');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response|void
     * @EndpointAction(path="/activate/{name}")
     */
    public function activateAction(Request $request, Response $response, array $args) {
        $this->handleActivate($args);

        return RouteHelper::redirect($response, 'backend_plugins', [], $request->getQueryParams());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response|void
     * @EndpointAction(path="/deactivate/{name}")
     */
    public function deactivateAction(Request $request, Response $response, array $args) {
        $this->handleDeactivate($args);

        return RouteHelper::redirect($response, 'backend_plugins', [], $request->getQueryParams());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response|void
     * @EndpointAction(path="/delete/{name}")
     */
    public function deleteAction(Request $request, Response $response, array $args) {
        Oforge()->View()->Flash()->addMessage('info', 'Not implemented yet!');

        //TODO Implementation of PluginController#deleteAction

        return RouteHelper::redirect($response, 'backend_plugins');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response|void
     * @EndpointAction(path="/install/{name}")
     */
    public function installAction(Request $request, Response $response, array $args) {
        $this->handleInstall($args);

        return RouteHelper::redirect($response, 'backend_plugins', [], $request->getQueryParams());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response|void
     * @EndpointAction(path="/install_activate/{name}", name="install_activate")
     */
    public function installActivateAction(Request $request, Response $response, array $args) {
        if ($this->handleInstall($args)) {
            $this->handleActivate($args);
        }

        return RouteHelper::redirect($response, 'backend_plugins', [], $request->getQueryParams());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response|void
     * @EndpointAction(path="/reactivate/{name}")
     */
    public function reactivateAction(Request $request, Response $response, array $args) {
        if ($this->handleDeactivate($args)) {
            $this->handleActivate($args);
        }

        return RouteHelper::redirect($response, 'backend_plugins', [], $request->getQueryParams());
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response|void
     * @EndpointAction(path="/rebuild")
     */
    public function rebuildAction(Request $request, Response $response) {
        $twigFlash = Oforge()->View()->Flash();
        try {
            /** @var TemplateManagementService $templateManagementService */
            $templateManagementService = Oforge()->Services()->get('template.management');
            $templateManagementService->buildAll();
            $twigFlash->addMessage('success', I18N::translate('crud_plugin_msg_rebuild_template_success', 'The template successfully rebuilt.'));
        } catch (TemplateNotFoundException | InvalidScssVariableException $exception) {
            Oforge()->Logger()->logException($exception);
            $twigFlash->addExceptionMessage('error', I18N::translate('crud_plugin_msg_template_error', 'The template could not be rebuilt.'), $exception);
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
            $twigFlash->addExceptionMessage('error', I18N::translate('crud_plugin_msg_error', 'An error has occurred.'), $exception);
        }

        return RouteHelper::redirect($response, 'backend_plugins', [], $request->getQueryParams());
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response|void
     * @EndpointAction(path="/reinstall/{name}")
     */
    public function reinstallAction(Request $request, Response $response, array $args) {
        $postData = $request->getParsedBody();
        if ($request->isPost() && !empty($postData) && isset($postData['keep_data'])) {
            $keepData = (bool) $postData['keep_data'];
            if ($this->handleUninstall($args, $keepData)) {
                $this->handleInstall($args);
            }

            return RouteHelper::redirect($response, 'backend_plugins', [], $request->getQueryParams());
        }
        Oforge()->View()->assign([
            'crud' => [
                'context'      => 'reinstall',
                'contextLabel' => 'reinstall',
                'pluginName'   => $args['name'],
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response|void
     * @EndpointAction(path="/reinstall-activate/{name}")
     */
    public function reinstallActivateAction(Request $request, Response $response, array $args) {
        $postData = $request->getParsedBody();
        if ($request->isPost() && !empty($postData) && isset($postData['keep_data'])) {
            $keepData = (bool) $postData['keep_data'];
            if ($this->handleUninstall($args, $keepData)) {
                if ($this->handleInstall($args)) {
                    $this->handleActivate($args);
                }
            }

            return RouteHelper::redirect($response, 'backend_plugins', [], $request->getQueryParams());
        }
        Oforge()->View()->assign([
            'crud' => [
                'context'      => 'reinstall_activate',
                'contextLabel' => 'reinstall & activate',
                'pluginName'   => $args['name'],
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response|void
     * @EndpointAction(path="/uninstall/{name}")
     */
    public function uninstallAction(Request $request, Response $response, array $args) {
        $postData = $request->getParsedBody();
        if ($request->isPost() && !empty($postData) && isset($postData['keep_data'])) {
            $keepData = (bool) $postData['keep_data'];
            $this->handleUninstall($args, $keepData);

            return RouteHelper::redirect($response, 'backend_plugins', [], $request->getQueryParams());
        }
        Oforge()->View()->assign([
            'crud' => [
                'context'      => 'uninstall',
                'contextLabel' => 'uninstall',
                'pluginName'   => $args['name'],
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(create=false)
     */
    public function updateAction(Request $request, Response $response, array $args) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(create=false)
     */
    public function createAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(create=false)
     */
    public function viewAction(Request $request, Response $response, array $args) {
    }

    /**
     * @param array $args
     *
     * @return bool
     */
    protected function handleActivate(array $args) : bool {
        if (isset($args['name'])) {
            $pluginName = $args['name'];
            $twigFlash  = Oforge()->View()->Flash();
            try {
                /** @var PluginStateService $pluginStateService */
                $pluginStateService = Oforge()->Services()->get('plugin.state');
                $pluginStateService->activate($pluginName);
                $twigFlash->addMessage('success', sprintf(#
                    I18N::translate('crud_plugin_msg_activate_success', 'Plugin "%s" successfully activated.'),#
                    $pluginName#
                ));

                return true;
            } catch (PluginAlreadyActivatedException $exception) {
                $twigFlash->addMessage('info', sprintf(#
                    I18N::translate('crud_plugin_msg_already_activated', 'The plugin "%s" is already activated. You cannot activate it twice.'),#
                    $pluginName#
                ));
            } catch (PluginNotFoundException $exception) {
                $twigFlash->addMessage('error', sprintf(#
                    I18N::translate('crud_plugin_msg_plugin_not_exist', 'Plugin "%s" does not exist.'),#
                    $pluginName#
                ));
            } catch (PluginNotInstalledException $exception) {
                $twigFlash->addMessage('error', sprintf(#
                    I18N::translate('crud_plugin_msg_not_installed', 'The plugin "%s" is not installed. You have to install it first.'),#
                    $pluginName#
                ));
            } catch (CouldNotActivatePluginException $exception) {
                $message    = sprintf(#
                    I18N::translate(#
                        'crud_plugin_msg_activate_failed',#
                        'Plugin "%s" could not be activated due to missing (not installed or activated) dependencies.'#
                    ),#
                    $pluginName#
                );
                $subMessage = I18N::translate('crud_plugin_msg_dependencies', 'Dependencies');
                $this->appendTwigFlashDetailMessage($twigFlash, $message, $subMessage, $exception->getDependencies());
            } catch (TemplateNotFoundException | InvalidScssVariableException $exception) {
                Oforge()->Logger()->logException($exception);
                $twigFlash->addExceptionMessage('error', I18N::translate('crud_plugin_msg_template_error', 'The template could not be rebuilt.'), $exception);
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
                $twigFlash->addExceptionMessage('error', I18N::translate('crud_plugin_msg_error', 'An error has occurred.'), $exception);
            }
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return bool
     */
    protected function handleDeactivate(array $args) : bool {
        if (isset($args['name'])) {
            $pluginName = $args['name'];
            $twigFlash  = Oforge()->View()->Flash();
            try {
                /** @var PluginStateService $pluginStateService */
                $pluginStateService = Oforge()->Services()->get('plugin.state');
                $pluginStateService->deactivate($pluginName);
                $twigFlash->addMessage('success', sprintf(#
                    I18N::translate('crud_plugin_msg_deactivate_success', 'Plugin "%s" successfully deactivated.'),#
                    $pluginName#
                ));

                return true;
            } catch (PluginNotFoundException $exception) {
                $twigFlash->addMessage('error', sprintf(#
                    I18N::translate('crud_plugin_msg_plugin_not_exist', 'Plugin "%s" does not exist.'),#
                    $pluginName#
                ));
            } catch (PluginNotInstalledException $exception) {
                $twigFlash->addMessage('error', sprintf(#
                    I18N::translate('crud_plugin_msg_not_installed', 'The plugin "%s" is not installed. You have to install it first.'),#
                    $pluginName#
                ));
            } catch (PluginNotActivatedException $exception) {
                $twigFlash->addMessage('success', sprintf(#
                    I18N::translate('crud_plugin_msg_not_activated', 'Plugin "%s" is not activated.'),#
                    $pluginName#
                ));
            } catch (CouldNotDeactivatePluginException $exception) {
                $message    = sprintf(#
                    I18N::translate(#
                        'crud_plugin_msg_deactivate_failed',#
                        'Plugin "%s" could not be deactivated because there are active plugins that depend on it.'#
                    ),#
                    $pluginName#
                );
                $subMessage = I18N::translate('crud_plugin_msg_dependents', 'Dependents');
                $this->appendTwigFlashDetailMessage($twigFlash, $message, $subMessage, $exception->getDependents());
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
                $twigFlash->addExceptionMessage('error', I18N::translate('crud_plugin_msg_error', 'An error has occurred.'), $exception);
            }
        }

        return false;
    }

    /**
     * @param array $args
     *
     * @return bool
     */
    protected function handleInstall(array $args) : bool {
        if (isset($args['name'])) {
            $pluginName = $args['name'];
            $twigFlash  = Oforge()->View()->Flash();
            try {
                /** @var PluginStateService $pluginStateService */
                $pluginStateService = Oforge()->Services()->get('plugin.state');
                $pluginStateService->install($pluginName);
                $twigFlash->addMessage('success', sprintf(#
                    I18N::translate('crud_plugin_msg_install_success', 'Plugin "%s" successfully installed.'),#
                    $pluginName#
                ));

                return true;
            } catch (PluginNotFoundException $exception) {
                $twigFlash->addMessage('error', sprintf(#
                    I18N::translate('crud_plugin_msg_plugin_not_exist', 'Plugin "%s" does not exist.'),#
                    $pluginName#
                ));
            } catch (CouldNotInstallPluginException $exception) {
                // $preContent = implode("\n", array_map(function ($item) {
                //     return str_replace('\\', ' - ', StringHelper::leftTrim(StringHelper::rightTrim($item, '\\Bootstrap'), 'Oforge\\Engine\\'));
                // }, $exception->getDependencies()));
                $message    = sprintf(#
                    I18N::translate(#
                        'crud_plugin_msg_install_failed',#
                        'Plugin "%s" could not be installed due to missing (not installed or activated) dependencies.'#
                    ),#
                    $pluginName#
                );
                $subMessage = I18N::translate('crud_plugin_msg_dependencies', 'Dependencies');
                $this->appendTwigFlashDetailMessage($twigFlash, $message, $subMessage, $exception->getDependencies());
            } catch (PluginAlreadyInstalledException $exception) {
                $twigFlash->addMessage('info', sprintf(#
                    I18N::translate('crud_plugin_msg_already_installed', 'The plugin "%s" is already installed. You cannot install it twice.'),#
                    $pluginName#
                ));
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
                $twigFlash->addExceptionMessage('error', I18N::translate('crud_plugin_msg_error', 'An error has occurred.'), $exception);
            }
        }

        return false;
    }

    /**
     * @param array $args
     * @param bool $keepData
     *
     * @return bool
     */
    protected function handleUninstall(array $args, bool $keepData) : bool {
        if (isset($args['name'])) {
            $pluginName = $args['name'];
            $twigFlash  = Oforge()->View()->Flash();
            try {
                /** @var PluginStateService $pluginStateService */
                $pluginStateService = Oforge()->Services()->get('plugin.state');
                $pluginStateService->uninstall($pluginName, $keepData);
                $twigFlash->addMessage('success', sprintf(#
                    I18N::translate('crud_plugin_msg_uninstall_success', 'Plugin "%s" successfully uninstalled.'),#
                    $pluginName#
                ));

                return true;
            } catch (PluginNotFoundException $exception) {
                $twigFlash->addMessage('error', sprintf(#
                    I18N::translate('crud_plugin_msg_plugin_not_exist', 'Plugin "%s" does not exist.'),#
                    $pluginName#
                ));
            } catch (PluginNotInstalledException $exception) {
                $twigFlash->addMessage('error', sprintf(#
                    I18N::translate('crud_plugin_msg_uninstall_not_installed', 'The plugin "%s" is not installed.'),#
                    $pluginName#
                ));
            } catch (PluginNotActivatedException $exception) {
                $twigFlash->addMessage('success', sprintf(#
                    I18N::translate('crud_plugin_msg_not_activated', 'Plugin "%s" is not activated.'),#
                    $pluginName#
                ));
            } catch (CouldNotDeactivatePluginException $exception) {
                $message    = sprintf(#
                    I18N::translate(#
                        'crud_plugin_msg_deactivate_failed',#
                        'Plugin "%s" could not be deactivated because there are active plugins that depend on it.'#
                    ),#
                    $pluginName#
                );
                $subMessage = I18N::translate('crud_plugin_msg_dependents', 'Dependents');
                $this->appendTwigFlashDetailMessage($twigFlash, $message, $subMessage, $exception->getDependents());
            } catch (Exception $exception) {
                $twigFlash->addExceptionMessage('error', I18N::translate('crud_plugin_msg_error', 'An error has occurred.'), $exception);
            }
        }

        return false;
    }

    /**
     * @param TwigFlash $twigFlash
     * @param string $message
     * @param string $subMessage
     * @param array $preContentLines
     */
    protected function appendTwigFlashDetailMessage(TwigFlash $twigFlash, string $message, string $subMessage, array $preContentLines) {
        $preContent = implode("\n", $preContentLines);
        $twigFlash->addDetailMessage('error', $message, $subMessage, $preContent);
    }

    /** @inheritDoc */
    protected function prepareItemDataArray(?AbstractModel $entity, string $crudAction, array $queryParams = []) : array {
        $data = parent::prepareItemDataArray($entity, $crudAction, $queryParams);

        // Refactor after Boot-Refactoring
        $pluginBootstrapClass = $data['name'] . '\\Bootstrap';
        $bootstrapInstance    = Oforge()->getBootstrapManager()->getBootstrapInstance($pluginBootstrapClass);

        if (isset($bootstrapInstance)) {
            $dependencies = $bootstrapInstance->getDependencies();
            if (!empty($dependencies)) {
                $dependencies         = array_map(function ($dependency) {
                    return StringHelper::rightTrim($dependency, '\\Bootstrap');
                }, $dependencies);
                $data['dependencies'] = $dependencies;
            }
        }

        // TODO include data version, description.long & description.short
        return $data;
    }

}
