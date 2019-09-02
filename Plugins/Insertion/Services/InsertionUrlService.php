<?php

namespace Insertion\Services;

use Insertion\Models\Insertion;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Services\UrlService;

/**
 * Class InsertionUrlService
 *
 * @package Insertion\Services
 */
class InsertionUrlService {
    /** @var UrlService $instance */
    private $instance;

    /**
     * InsertionUrlService constructor.
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

        if ($name == 'insertions_detail' && isset($namedParams["id"])) {
            /** @var InsertionService $service */
            $service = Oforge()->Services()->get('insertion');
            /** @var Insertion $insertion */
            $insertion = $service->getInsertionById($namedParams["id"]);
            if ($insertion != null) {
                $title     = str_replace(" ", "-", strtolower($insertion->getContent()[0]->getTitle()));
                $title     = str_replace("/", "", $title);
                $typeTitle = str_replace(" ", "-", strtolower(I18N::translate($insertion->getInsertionType()->getName())));

                return "/" . urlencode($typeTitle) . "/" . urlencode($title) . "/" . $insertion->getId();
            }
        } elseif ($name == 'insertions_profile' && isset($namedParams["id"])) {
            /** @var InsertionProfileService $service */
            $service = Oforge()->Services()->get('insertion.profile');
            $profile = $service->getById($namedParams["id"]);
            $title     = str_replace(" ", "-", strtolower($profile->getImprintName()));
            $title     = str_replace("/", "", $title);

            return "/" . urlencode(I18N::translate('insertion_url_profile')) . "/" . urlencode($title) . "/" . $namedParams["id"];
        }

        return $this->instance->getUrl($name, $namedParams, $queryParams);
    }

}
