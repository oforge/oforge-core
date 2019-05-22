<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:53
 */

namespace Oforge\Engine\Modules\CMS\Twig;

use Oforge\Engine\Modules\CMS\Services\NamedContentService;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Twig_Extension;
use Twig_ExtensionInterface;
use Twig_Function;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\CMS
 */
class AccessExtension extends Twig_Extension implements Twig_ExtensionInterface {

    /**
     * @inheritDoc
     */
    public function getFunctions() {
        return [
            new Twig_Function('content', [$this, 'getContent'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    /** @inheritDoc */
    public function getContent(...$vars) {
        $name = ArrayHelper::get($vars, 0);
        if ($name != null) {
            /**
             * @var $contentService NamedContentService
             */
            $contentService = Oforge()->Services()->get("named.content");

            return $contentService->getContent($name);
        }
    }

}