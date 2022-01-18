<?php

namespace Oforge\Engine\Modules\CRUD\Twig;

use Twig_Extension;
use Twig_ExtensionInterface;
use Twig_Function;

/**
 * Class CrudExtension
 *
 * @package Oforge\Engine\Modules\CRUD\Twig
 */
class CrudExtension extends Twig_Extension implements Twig_ExtensionInterface {

    /** @inheritDoc */
    public function getFunctions() {
        return [
            new Twig_Function('CRUD_path', [$this, 'getCrudPath'], [
                'is_safe'       => ['html'],
                'needs_context' => true,
            ]),
        ];
    }

    /**
     * @param array $context
     * @param string $suffix
     *
     * @return array
     */
    public function getCrudPath(array $context, string $suffix) : array {
        $result = [];
        if (isset($context['crud']['templatePath'])) {
            $result[] = $context['crud']['templatePath'] . '/' . $suffix;
        }
        $result[] = 'Backend/CRUD/' . $suffix;
        if (isset($context['debug']['console']) && $context['debug']['console']) {
            o_dump($result);
        }

        return $result;
    }

}
