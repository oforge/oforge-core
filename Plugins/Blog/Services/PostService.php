<?php

namespace Blog\Services;

use Blog\Models\Post;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class PostService
 *
 * @package Blog\Services
 */
class PostService extends AbstractDatabaseAccess {

    /** @inheritDoc */
    public function __construct() {
        parent::__construct(Post::class);
    }

}
