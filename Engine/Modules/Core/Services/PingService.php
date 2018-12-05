<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 05.12.2018
 * Time: 13:42
 */
namespace Oforge\Engine\Modules\Core\Services;

class PingService {
    public function me()
    {
        print_r("Hail to the Oforge King! When you see this, everything looks good.\n");
    }
}