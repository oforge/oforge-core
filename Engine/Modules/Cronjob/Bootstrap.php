<?php

namespace Oforge\Engine\Modules\Cronjob;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Models\Module\Module;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\Cronjob\Cronjobs\LogCleanupCronjob;
use Oforge\Engine\Modules\Cronjob\Cronjobs\ProcessAsyncEventsCronjob;
use Oforge\Engine\Modules\Cronjob\Models\AbstractCronjob;
use Oforge\Engine\Modules\Cronjob\Services\CronjobService;

/**
 * Class Console-Bootstrap
 *
 * @package Oforge\Engine\Modules\Console
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Console-Bootstrap constructor.
     */
    public function __construct() {
        $this->cronjobs     = [
            LogCleanupCronjob::class,
            ProcessAsyncEventsCronjob::class,
        ];
        $this->dependencies = [
            \Oforge\Engine\Modules\Console\Bootstrap::class,
        ];
        $this->models       = [
            AbstractCronjob::class,
        ];
        $this->services     = [
            'cronjob' => CronjobService::class,
        ];
    }

    public function activate() {
        $cronjobClassNames = [];

        // TODO refactor after boostrap refactoring
        $entityManager    = Oforge()->DB()->getForgeEntityManager();
        $moduleRepository = $entityManager->getRepository(Module::class);
        $pluginRepository = $entityManager->getRepository(Plugin::class);
        /**
         * @var Module[] $modules
         */
        $modules = $moduleRepository->findBy(['active' => 1], ['order' => 'ASC']);
        $moduleRepository->clear();

        foreach ($modules as $module) {
            $bootstrapName = $module->getName();
            /**
             * @var AbstractBootstrap $instance
             */
            $instance          = new $bootstrapName();
            $cronjobClassNames = array_merge($cronjobClassNames, $instance->getCronjobs());
        }
        /**
         * @var Plugin[] $plugins
         */
        $plugins = $pluginRepository->findBy(['active' => 1], ['order' => 'ASC']);
        $pluginRepository->clear();
        foreach ($plugins as $plugin) {
            $bootstrapName = $plugin->getName() . '\Bootstrap';
            /**
             * @var AbstractBootstrap $instance
             */
            $instance          = new $bootstrapName();
            $cronjobClassNames = array_merge($cronjobClassNames, $instance->getCronjobs());
        }

        $cronjobInstances = [];
        foreach ($cronjobClassNames as $cronjobClassName) {
            if (is_subclass_of($cronjobClassName, AbstractCronjob::class)) {
                try {
                    $cronjobInstances[] = new $cronjobClassName();
                } catch (\Exception $exception) {
                    Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
                }
            }
        }
        /** @var CronjobService $cronjobService */
        $cronjobService = Oforge()->Services()->get('cronjob');
        $cronjobService->addCronjobInstances($cronjobInstances);

        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        // TODO uncomment after configservice refactoring
        // $configService->add([
        //     'name'     => CronjobSettings::LOGFILE_DAYS,
        //     'label'    => CronjobSettings::LOGFILE_DAYS,
        //     'type'     => 'number',
        //     'value'    => 14,
        //     'required' => true,
        //     'default'  => 14,
        //     'group'    => 'system'
        // ]);
    }

}
