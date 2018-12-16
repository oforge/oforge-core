<?php

namespace Oforge\Engine\Modules\SystemSettings\Serivces;

use Oforge\Engine\Modules\Core\Models\Config\Element;
use Oforge\Engine\Modules\Core\Models\Config\Value;

/**
* Class SystemSettingsService
* @package Oforge\Engine\Modules\SystemSettings\Serivces;
*/
class SystemSettingsService
{
    public function __construct()
    {
        $this->em = Oforge()->DB()->getManager();
    }

    private function getRepo($class)
    {
        return $this->em->getRepository($class);
    }

    public function getConfigValueList() {
        $repo = $this->getRepo(Value::class);
        $items = $repo->findAll();

        $result = [];
        foreach ($items as $item) {
            array_push($result, $item->toArray());
        }

        return $result;
    }

    public  function getConfigElementList() {
        $repo = $this->getRepo(Element::class);
        $items = $repo->findAll();

        $result = [];
        foreach ($items as $item) {
            array_push($result, $item->toArray());
        }

        return $result;
    }
}