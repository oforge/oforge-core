<?php

namespace Oforge\Engine\Modules\I18n;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Models\Snippets;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;

class Bootstrap extends AbstractBootstrap
{
    public function __construct()
    {
        $this->services = [
            "i18n" => InternationalizationService::class
        ];

        $this->models = [
            Language::class,
            Snippets::class
        ];

    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */


    public function install()
    {

    }
}