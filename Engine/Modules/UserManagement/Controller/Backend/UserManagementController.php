<?php

namespace Oforge\Engine\Modules\UserManagement\Controller\Backend;

use Exception;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterComparator;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroubByOrder;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class UserManagementController
 *
 * @package Oforge\Engine\Modules\UserManagement\Controller\Backend
 * @EndpointClass(path="/backend/users", name="backend_users", assetScope="Backend")
 */
class UserManagementController extends BaseCrudController {
    /** @var string $model */
    protected $model = BackendUser::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'   => 'email',
            'type'   => CrudDataTypes::EMAIL,
            'label'  => [
                'key'     => 'backend_backenduser_email',
                'default' => [
                    'en' => 'Mail',
                    'de' => 'Mail',
                ],
            ],
            'crud'   => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
            'editor' => [
                'required' => true,
            ],
        ],# email
        [
            'name'   => 'password',
            'type'   => CrudDataTypes::STRING,
            'label'  => [
                'key'     => 'backend_backenduser_password',
                'default' => [
                    'en' => 'Password',
                    'de' => 'Passwort',
                ],
            ],
            'crud'   => [
                'index'  => 'off',
                'view'   => 'off',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'off',
            ],
            'editor' => [
                'hint' => [
                    'key'     => 'crud_backenduser_hint_password',
                    'default' => [
                        'en' => 'If field is left blank, the password will not be changed.',
                        'de' => 'Wenn Feld leer gelassem wird, wird das Passwort nicht geändert.',
                    ],
                ],
            ],
        ],# password
        [
            'name'   => 'role',
            'type'   => CrudDataTypes::SELECT,
            'label'  => [
                'key'     => 'backend_backenduser_role',
                'default' => [
                    'en' => 'Role',
                    'de' => 'Rolle',
                ],
            ],
            'crud'   => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
            'list'   => 'getSelectRoles',
            'editor' => [
                'hint'    => [
                    'key'     => 'crud_backenduser_no_system_user_edit',
                    'default' => [
                        'en' => 'A change of system user data is not allowed via the backend.',
                        'de' => 'Eine Änderung von System Nutzerdaten ist nicht über das Backend erlaubt.',
                    ],
                ],
                'default' => BackendUser::ROLE_MODERATOR,
            ],
        ],# role
        [
            'name'  => 'name',
            'type'  => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'backend_backenduser_name',
                'default' => [
                    'en' => 'Name',
                    'de' => 'Name',
                ],
            ],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],# name
        [
            'name'  => 'active',
            'type'  => CrudDataTypes::BOOL,
            'label' => [
                'key'     => 'backend_backenduser_active',
                'default' => [
                    'en' => 'Active',
                    'de' => 'Aktiviert',
                ],
            ],
            'crud'  => [
                'index'  => 'editable',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],# active
    ];
    /** @var array $indexFilter */
    protected $indexFilter = [
        'email' => [
            'type'    => CrudFilterType::TEXT,
            'label'   => [
                'key'     => 'crud_backenduser_filter_email',
                'default' => [
                    'en' => 'Search in mail',
                    'de' => 'Suche in Mail',
                ],
            ],
            'compare' => CrudFilterComparator::LIKE,
        ],
        'name'  => [
            'type'    => CrudFilterType::TEXT,
            'label'   => [
                'key'     => 'crud_backenduser_filter_name',
                'default' => [
                    'en' => 'Search in name',
                    'de' => 'Suche in Name',
                ],
            ],
            'compare' => CrudFilterComparator::LIKE,
        ],
        'role'  => [
            'type'  => CrudFilterType::SELECT,
            'label' => [
                'key'     => 'crud_backenduser_filter_role',
                'default' => [
                    'en' => 'Select role',
                    'de' => 'Rolle auswählen',
                ],
            ],
            'list'  => 'getSelectRoles',
        ],
    ];
    /** @var array $indexOrderBy */
    protected $indexOrderBy = [
        'email' => CrudGroubByOrder::ASC,
    ];

    /** @return array */
    public function getSelectRoles() : array {
        I18N::translate('backend_user_role_' . BackendUser::ROLE_SYSTEM, [
            'en' => 'System',
            'de' => 'System',
        ]);

        return [
            BackendUser::ROLE_MODERATOR     => I18N::translate('backend_user_role_' . BackendUser::ROLE_MODERATOR, [
                'en' => 'Moderator',
                'de' => 'Moderator',
            ]),
            BackendUser::ROLE_ADMINISTRATOR => I18N::translate('backend_user_role_' . BackendUser::ROLE_ADMINISTRATOR, [
                'en' => 'Administrator',
                'de' => 'Administrator',
            ]),
        ];
    }

    /**
     * @inheritDoc
     * @EndpointAction(path="/view/{id:\d+}")
     */
    public function viewAction(Request $request, Response $response, array $args) {
        if ($this->checkUserType($args)) {
            return $this::redirect($response, 'index');
        }

        return parent::viewAction($request, $response, $args);
    }

    /**
     * @inheritDoc
     * @EndpointAction(path="/update/{id:\d+}")
     */
    public function updateAction(Request $request, Response $response, array $args) {
        if ($this->checkUserType($args)) {
            return $this::redirect($response, 'index');
        }

        return parent::updateAction($request, $response, $args);
    }

    /**
     * @inheritDoc
     * @EndpointAction(path="/delete/{id:\d+}")
     */
    public function deleteAction(Request $request, Response $response, array $args) {
        if ($this->checkUserType($args)) {
            return $this::redirect($response, 'index');
        }

        return parent::deleteAction($request, $response, $args);
    }

    /**
     * Check if user for id ist system user.
     *
     * @param array $args
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    protected function checkUserType(array $args) {
        /** @var BackendUser|null $entity */
        $entity = $this->crudService->getById($this->model, $args['id']);

        if (isset($entity) && $entity->getRole() === BackendUser::ROLE_SYSTEM) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('crud_backenduser_no_system_user_edit', [
                'en' => 'A change of system user data is not allowed via the backend.',
                'de' => 'Eine Änderung von System Nutzerdaten ist nicht über das Backend erlaubt.',
            ]));

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function convertData(array $data, string $crudAction) : array {
        if (empty($data['password'])) {
            if ($crudAction === 'create') {
                throw new Exception(I18N::translate('Missing password', ['en' => 'Missing password', 'de' => 'Passwort fehlt']));
            } else {
                unset($data['password']);
            }
        } else {
            /** @var PasswordService $passwordService */
            $passwordService  = Oforge()->Services()->get('password');
            $data['password'] = $passwordService->hash($data['password']);
        }

        return $data;
    }

    /**  @inheritDoc */
    protected function prepareItemDataArray(?AbstractModel $entity, string $crudAction) : array {
        $data = parent::prepareItemDataArray($entity, $crudAction);

        $data['password'] = '';

        return $data;
    }

    /** @inheritDoc */
    protected function evaluateIndexFilter(array $queryParams) : array {
        $filter = parent::evaluateIndexFilter($queryParams);
        if (!isset($queryParams['role']) || $queryParams['role'] === '') {
            $filter['role'] = [
                'comparator' => CrudFilterComparator::NOT_EQUALS,
                'value'      => BackendUser::ROLE_SYSTEM,
            ];
        }

        return $filter;
    }

}
