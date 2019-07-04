<?php

namespace Seo\Services;

use Oforge\Engine\Modules\Core\Helper\ArrayHelper;

class SeoUrlService {

    private $instance;

    public function __construct($instance) {
        $this->instance = $instance;
    }

    public function getSlimUrl(...$vars) {
        if (!isset($this->router)) {
            $this->router = Oforge()->App()->getContainer()->get('router');
        }

        if (!isset($this->seoService)) {
            $this->seoService = Oforge()->Services()->get('seo');
        }

        $result = $this->instance->getSlimUrl(...$vars);

        try {
            $seoObject = $this->seoService->getBySource($result);
            if ($seoObject != null) {
                $result = $seoObject->getTarget();
            }
        } catch (\Exception $e) {
        }

        return $result;
    }
}
