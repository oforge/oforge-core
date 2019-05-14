<?php

namespace Insertion;

use Insertion\Controller\Backend\BackendAttributeController;
use Insertion\Controller\Backend\BackendInsertionController;
use Insertion\Controller\Backend\BackendInsertionTypeController;
use Insertion\Controller\Frontend\FrontendInsertionController;
use Insertion\Models\AttributeKey;
use Insertion\Models\AttributeValue;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionAttributeValue;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Services\AttributeService;
use Insertion\Services\InsertionService;
use Insertion\Services\InsertionTypeService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            FrontendInsertionController::class,
            BackendAttributeController::class,
            BackendInsertionController::class,
            BackendInsertionTypeController::class,
        ];

        $this->services = [
            'insertion' => InsertionService::class,
            'insertion.type' => InsertionTypeService::class,
            'insertion.attribute' => AttributeService::class,
        ];

        $this->models = [
            AttributeKey::class,
            AttributeValue::class,
            Insertion::class,
            InsertionAttributeValue::class,
            InsertionType::class,
            InsertionTypeAttribute::class,
        ];

        $this->dependencies = [
            \FrontendUserManagement\Bootstrap::class
        ];
    }
}
