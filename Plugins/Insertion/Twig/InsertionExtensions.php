<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 05.12.2018
 * Time: 15:49
 */

namespace Insertion\Twig;

use Insertion\Services\AttributeService;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Media\Services\ImageCompressService;
use Twig_Extension;
use Twig_ExtensionInterface;
use Twig_Function;

/**
 * Class AccessExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class InsertionExtensions extends Twig_Extension implements Twig_ExtensionInterface {

    /**
     * @inheritDoc
     */
    public function getFunctions() {
        return [
            new Twig_Function('getInserationValues', [$this, 'getInserationValues'])
        ];
    }

    /**
     * @param mixed ...$vars
     *
     * @return mixed|string
     * @throws ServiceNotFoundException
     */
    public function getInserationValues(...$vars) {
        $result = '';
        if (count($vars) == 1) {
            /** @var AttributeService $attributeService */
            $attributeService = Oforge()->Services()->get('insertion.attribute');
            $tmp = $attributeService->getAttribute($vars[0]);
            if(isset($tmp)) {
                $result = $tmp->toArray(3);
            }
        }
        return $result;
    }
}
