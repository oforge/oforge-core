<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\Modules\TemplateEngine\Core\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\TemplateEngine\Core\Abstracts\AbstractTemplate;
use Oforge\Engine\Modules\TemplateEngine\Core\Exceptions\InvalidScssVariableException;
use Oforge\Engine\Modules\TemplateEngine\Core\Models\Template\Template;

class TemplateManagementService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => Template::class]);
    }

    /**
     * @param $name
     *
     * @throws TemplateNotFoundException
     * @throws ORMException
     */
    public function activate($name) {
        /** @var $templateToActivate Template */
        $templateToActivate = $this->repository()->findOneBy(["name" => $name]);
        $activeTemplate     = $this->getActiveTemplate();

        if (!isset($templateToActivate)) {
            throw new TemplateNotFoundException($name);
        }

        if (isset($activeTemplate)) {
            /** @var $activeTemplate Template */
            $activeTemplate->setActive(false);
        }

        $templateToActivate->setActive(true);

        $this->entityManager()->persist($templateToActivate);
        $this->entityManager()->persist($activeTemplate);
        $this->entityManager()->flush();
    }

    /**
     * @param $name
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws InvalidScssVariableException
     */
    public function register($name) {
        $template = $this->repository()->findOneBy(["name" => $name]);

        if (!$template) {
            $className = Statics::TEMPLATE_DIR . "\\" . $name . "\\Template";
            $parent    = null;

            $instance = null;

            if (is_subclass_of($className, AbstractTemplate::class)) {
                /**
                 * @var $instance AbstractTemplate
                 */
                $instance = new $className();
                $parent   = $instance->parent;
            }

            if ($parent !== null) {
                /**
                 * @var $parentTemplate Template
                 */
                $parentTemplate = $this->repository()->findOneBy(["name" => $parent]);
                $parent         = $parentTemplate->getId();
            }

            $template = Template::create(["name" => $name, "active" => 0, "installed" => 0, "parentId" => $parent]);

            $this->entityManager()->persist($template);
            $this->entityManager()->flush();

            if ($instance) {
                $instance->registerTemplateVariables();
            }
        }
    }

    /**
     * @return array|object[]
     */
    public function list() {
        $templateList = $this->repository()->findAll();

        return $templateList;
    }

    /**
     * Get the active theme, delete old cached assets, build new assets
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws InvalidScssVariableException
     * @throws TemplateNotFoundException
     * @throws TemplateNotFoundException
     */
    public function build() {
        /** @var Template $template */
        $template = $this->getActiveTemplate();
        if ($template) {
            /** @var TemplateAssetService $templateAssetService */
            $templateAssetService = Oforge()->Services()->get('assets.template');
            $templateAssetService->clear();

            $className = Statics::TEMPLATE_DIR . "\\" . $template->getName() . "\\Template";

            if (is_subclass_of($className, AbstractTemplate::class)) {
                /** @var $instance AbstractTemplate */
                $instance = new $className();
                $instance->registerTemplateVariables();
            }

            $templateAssetService->build($template->getName(), $templateAssetService::DEFAULT_SCOPE);
        }
    }

    /**
     * Build Backend and Frontend
     *
     * @throws InvalidScssVariableException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws TemplateNotFoundException
     */
    public function buildAll() {
        /** @var Template $template */
        $template = $this->getActiveTemplate();
        if ($template) {
            /** @var TemplateAssetService $templateAssetService */
            $templateAssetService = Oforge()->Services()->get('assets.template');
            $templateAssetService->clear();

            $className = Statics::TEMPLATE_DIR . "\\" . $template->getName() . "\\Template";

            if (is_subclass_of($className, AbstractTemplate::class)) {
                /** @var $instance AbstractTemplate */
                $instance = new $className();
                $instance->registerTemplateVariables();
            }

            $templateAssetService->build($template->getName(), 'Frontend');
            $templateAssetService->build($template->getName(), 'Backend');
        }
    }

    /**
     * @return Template
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TemplateNotFoundException
     */
    public function getActiveTemplate() {
        /**
         * @var $template Template
         */
        $template = $this->repository()->findOneBy(["active" => 1]);
        if ($template === null) {
            $template = $this->repository()->findOneBy(["name" => Statics::DEFAULT_THEME]);

            if ($template === null) {
                throw new TemplateNotFoundException(Statics::DEFAULT_THEME);
            }

            $template->setActive(1);
            $this->entityManager()->flush();
        }

        return $template;
    }
}
