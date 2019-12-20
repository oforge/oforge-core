<?php

namespace Oforge\Engine\Modules\AdminBackend\Cronjob\Controller\Backend;

use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Cronjob\Models\AbstractCronjob;
use Oforge\Engine\Modules\Cronjob\Services\CronjobService;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CronjobController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Cronjob\Controller\Backend
 * @EndpointClass(path="/backend/cronjob", name="backend_cronjob", assetScope="Backend")
 */
class CronjobController extends BaseCrudController {
    /** @var string $model */
    protected $model = AbstractCronjob::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'  => 'title',
            'type'  => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'module_cronjob_property_title',
                'default' => [
                    'en' => 'Title',
                    'de' => 'Titel',
                ],
            ],
            'crud'  => [
                'index' => 'readonly',
            ],
        ],
        [
            'name'  => 'executionInterval',
            'type'  => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'module_cronjob_property_execution_interval',
                'default' => [
                    'en' => 'Execution interval',
                    'de' => 'Ausführungsintervall',
                ],
            ],
            'crud'  => [
                'index' => 'readonly',
            ],
        ],
        [
            'name'  => 'lastExecutionTime',
            'type'  => CrudDataTypes::DATETIME,
            'label' => [
                'key'     => 'module_cronjob_property_last_execution',
                'default' => [
                    'en' => 'Last execution',
                    'de' => 'Letzte Ausführung',
                ],
            ],
            'crud'  => [
                'index' => 'readonly',
            ],
        ],
        [
            'name'  => 'nextExecutionTime',
            'type'  => CrudDataTypes::DATETIME,
            'label' => [
                'key'     => 'module_cronjob_property_next_execution',
                'default' => [
                    'en' => 'Next execution',
                    'de' => 'Nächste Ausführung',
                ],
            ],
            'crud'  => [
                'index'  => 'editable',
                ''
            ],
        ],
        [
            'name'  => 'active',
            'type'  => CrudDataTypes::BOOL,
            'label' => [
                'key'     => 'module_cronjob_property_active',
                'default' => [
                    'en' => 'active',
                    'de' => 'aktiv',
                ],
            ],
            'crud'  => [
                'index' => 'editable',
            ],
        ],
        [
            'name'     => 'execute',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => [
                'key'     => 'module_cronjob_button_execute',
                'default' => [
                    'en' => 'Execution',
                    'de' => 'Ausführung',
                ],
            ],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Backend/Cronjob/Index/Action/Execute.twig',
            ],
        ],
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

    public function initPermissions() {
        parent::initPermissions();
        $this->ensurePermissions([
            'runAction',
        ], BackendUser::ROLE_ADMINISTRATOR);
    }

    /** @EndpointAction(create=false) */
    public function createAction(Request $request, Response $response) {
    }

    /** @EndpointAction(create=false) */
    public function updateAction(Request $request, Response $response, array $args) {
    }

    /** @EndpointAction(create=false) */
    public function deleteAction(Request $request, Response $response, array $args) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/run/{name}")
     */
    public function runAction(Request $request, Response $response, array $args) {
        /** @var CronjobService $cronjobService */
        $cronjobService = Oforge()->Services()->get('cronjob');
        if ($cronjobService->run($args['name'])) {
            Oforge()->View()->Flash()->addMessage('success', I18N::translate('cronjob_success', [
              'en' => 'Cronjob execution successful.',
              'de' => 'Cronjob erfolgreich ausgeführt.',
            ]));
        } else {
            Oforge()->View()->Flash()->addMessage('error', I18N::translate('cronjob_error', [
                'en' => 'Cronjob execution has caused an error, please check the logs for details.',
                'de' => 'Cronjob Ausführung führte zu einem Fehler, Details stehen in den Logs.',
            ]));
        }

        return RouteHelper::redirect($response, 'backend_cronjob', [], $request->getQueryParams());
    }

    protected function convertData(array $data, string $crudAction) : array {
        if (isset($data['nextExecutionTime'])) {
            try {
                $data['nextExecutionTime'] = new \DateTimeImmutable($data['nextExecutionTime']);
            } catch (\Exception $e) {
            }
        }
        return $data;
    }
}
