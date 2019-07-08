<?php

namespace Seo\Services;

use Oforge\Engine\Modules\TemplateEngine\Extensions\Services\UrlService;

/**
 * Class SeoUrlService
 *
 * @package Seo\Services
 */
class SeoUrlService {
    /** @var UrlService $instance */
    private $instance;

    /**
     * SeoUrlService constructor.
     *
     * @param $instance
     */
    public function __construct($instance) {
        $this->instance = $instance;
    }

    /** @see UrlService::getUrl() */
    public function getUrl(...$vars) {
        if (!isset($this->router)) {
            $this->router = Oforge()->App()->getContainer()->get('router');
        }

        if (!isset($this->seoService)) {
            $this->seoService = Oforge()->Services()->get('seo');
        }

        $result = $this->instance->getUrl(...$vars);

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
