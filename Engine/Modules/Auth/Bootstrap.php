<?php

namespace Oforge\Engine\Modules\Auth;

use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Models\User\BackendUserDetail;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Auth\Services\BackendLoginService;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Auth\Services\PermissionService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\Auth
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->models = [
            BackendUser::class,
            BackendUserDetail::class,
        ];

        $this->services = [
            'auth'          => AuthService::class,
            'backend.login' => BackendLoginService::class,
            'password'      => PasswordService::class,
            'permissions'   => PermissionService::class,
        ];
    }

    public function install() {
        parent::install();
        I18N::translate('config_group_auth_core', [
            'en' => 'Auth (core)',
            'de' => 'Auth (core)',
        ]);
        I18N::translate('config_auth_core_password_min_length', [
            'en' => 'Password minimum length',
            'de' => 'Passwortmindestlänge',
        ]);
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $configService->add([
            'name'     => 'auth_core_password_min_length',
            'type'     => ConfigType::INTEGER,
            'group'    => 'auth_core',
            'default'  => 6,
            'label'    => 'config_auth_core_password_min_length',
            'required' => true,
        ]);
    }

}
