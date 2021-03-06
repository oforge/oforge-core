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
    public function getUrl(string $name, array $namedParams = [], array $queryParams = []) : string {
        if (!isset($this->router)) {
            $this->router = Oforge()->App()->getContainer()->get('router');
        }

        if (!isset($this->seoService)) {
            $this->seoService = Oforge()->Services()->get('seo');
        }

        $result = $this->instance->getUrl($name, $namedParams, $queryParams);

        try {
            $seoObject = $this->seoService->getBySource($result);
            if ($seoObject != null) {
                $query  = explode('?', $result);
                $result = $seoObject->getTarget();

                if (sizeof($query) > 1) {
                    $result .= '?' . $query[1];
                }
            }
        } catch (\Exception $e) {
        }

        return $result;
    }
}
