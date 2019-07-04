<?php

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Models\Snippet;
use ReflectionException;

/**
 * Class UrlService
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Services
 */
class UrlService {
    private $router = null;

    public function getSlimUrl(...$vars) {
        if (!isset($this->router)) {
            $this->router = Oforge()->App()->getContainer()->get('router');
        }

        $name        = ArrayHelper::get($vars, 0);
        $namedParams = ArrayHelper::get($vars, 1, []);
        $queryParams = ArrayHelper::get($vars, 2, []);
        $result      = "";
        try {
            $result = $this->router->pathFor($name, $namedParams, $queryParams);
        } catch (\Exception $e) {
            $result = $name;
        }

        return $result;
    }
}


