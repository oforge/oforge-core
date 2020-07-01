<?php

namespace SocialLogin\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use SocialLogin\Models\LoginProvider;

/**
 * Class LoginProviderService
 *
 * @package Seo\Services
 */
class LoginProviderService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(['default' => LoginProvider::class]);
    }

    public function getByName(string $name) : ?LoginProvider {
        /** @var LoginProvider|null $result */
        $result = $this->repository()->findOneBy(['name' => $name]);
        return $result;
    }

}
