<?php

namespace Blog\Services;

use Blog\Models\Rating;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class RatingService
 *
 * @package Blog\Services
 */
class RatingService extends AbstractDatabaseAccess {

    /** @inheritDoc */
    public function __construct() {
        parent::__construct(Rating::class);
    }

}
