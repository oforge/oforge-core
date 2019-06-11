<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 05.12.2018
 * Time: 15:49
 */

namespace Seo\Twig;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\DateTimeFormatter;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Seo\Services\SeoService;
use Slim\Router;
use Twig_Extension;
use Twig_ExtensionInterface;
use Twig_Filter;
use Twig_Function;

/**
 * Class AccessExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class SeoExtension extends Twig_Extension implements Twig_ExtensionInterface {
    /** @var Router $router */
    private $router;

    /** @var SeoService $seoService*/
    private $seoService;
    /**
     * @inheritDoc
     */
    public function getFunctions() {
        return [
            new Twig_Function('url', [$this, 'getSlimUrl'], [
                'is_safe' => ['html'],
            ]),
        ];
    }


    /**
     * @param mixed ...$vars
     *
     * @return string
     */
    public function getSlimUrl(...$vars) {
        if (!isset($this->router)) {
            $this->router = Oforge()->App()->getContainer()->get('router');
        }

        if (!isset($this->seoService)) {
            $this->seoService = Oforge()->Services()->get('seo');
        }

        $name        = ArrayHelper::get($vars, 0);
        $namedParams = ArrayHelper::get($vars, 1, []);
        $queryParams = ArrayHelper::get($vars, 2, []);
        $result = "";
        try {
           $result = $this->router->pathFor($name, $namedParams, $queryParams);
           $seoObject = $this->seoService->getBySource($result);
           if($seoObject != null) {
               $result = $seoObject->getTarget();
           }
        } catch (\Exception $e) {
            $result = $name;
        }




        return $result;
    }


}