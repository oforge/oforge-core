<?php

namespace Oforge\Engine\Modules\Core\Services;

/**
 * Class PingService
 *
 * @package Oforge\Engine\Modules\Core\Services
 */
class PingService {

    /**
     * Returns or echo message.
     *
     * @param bool $echo
     *
     * @return string
     */
    public function me($echo = false) {
        $text = "Hail to the Oforge King! When you see this, everything looks good";
        if ($echo) {
            echo $text, "\n";
        }

        return $text;
    }

}
