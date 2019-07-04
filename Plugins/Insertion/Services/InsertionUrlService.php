<?php

namespace Insertion\Services;

use Insertion\Models\Insertion;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\I18n\Helper\I18N;

class InsertionUrlService {

    private $instance;

    public function __construct($instance) {
        $this->instance = $instance;
    }

    public function getSlimUrl(...$vars) {
        if (!isset($this->router)) {
            $this->router = Oforge()->App()->getContainer()->get('router');
        }

        $name        = ArrayHelper::get($vars, 0);
        $namedParams = ArrayHelper::get($vars, 1, []);

        if ($name == 'insertions_detail' && isset($namedParams["id"])) {
            /**
             * @var $service InsertionService
             */
            $service = Oforge()->Services()->get('insertion');
            /**
             * @var $insertion Insertion
             */
            $insertion = $service->getInsertionById($namedParams["id"]);
            if ($insertion != null) {
                $title     = str_replace(" ", "-", strtolower($insertion->getContent()[0]->getTitle()));
                $typeTitle = str_replace(" ", "-", strtolower(I18N::translate($insertion->getInsertionType()->getName())));

                return "/" . urlencode($typeTitle) . "/" . urlencode($title) . "/" . $insertion->getId();
            }

        }

        return $this->instance->getSlimUrl(...$vars);

    }
}
