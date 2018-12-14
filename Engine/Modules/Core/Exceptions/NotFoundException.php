<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 22.11.2018
 * Time: 11:53
 */

namespace Oforge\Engine\Modules\Core\Exceptions;

class NotFoundException extends \Exception {
    /**
     * CouldNotInstallPluginException constructor.
     *
     * @param string $text
     */
    public function __construct( $text ) {
        parent::__construct( $text );
    }
}
