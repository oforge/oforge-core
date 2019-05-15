<?php

namespace Blog\Services;

use Blog\Models\Category;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class CategoryService
 *
 * @package Blog\Services
 */
class CategoryService extends AbstractDatabaseAccess {

    /** @inheritDoc */
    public function __construct() {
        parent::__construct(Category::class);
    }

}
