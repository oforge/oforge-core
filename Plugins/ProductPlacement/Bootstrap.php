<?php

namespace ProductPlacement;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Helper\Statics;

/**
 * Class Bootstrap
 *
 * @package ProductPlacement
 */
class Bootstrap extends AbstractBootstrap {
    protected $order = Statics::DEFAULT_ORDER + 100;
}
