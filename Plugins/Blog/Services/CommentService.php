<?php

namespace Blog\Services;

use Blog\Models\Comment;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class CommentService
 *
 * @package Blog\Services
 */
class CommentService extends AbstractDatabaseAccess {

    /** @inheritDoc */
    public function __construct() {
        parent::__construct(Comment::class);
    }

}
