<?php

namespace Blog\Controller\Backend;

use Blog\Models\Comment;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;

/**
 * Class CommentController
 *
 * @package Blog\Controller\Backend\Blog
 * @EndpointClass(path="/backend/blog/comments", name="backed_blog_comments", assetScope="Backend")
 */
class CommentController extends BaseCrudController {
    /** @var string $model */
    protected $model = Comment::class;
    /** @var array $modelProperties */
    protected $modelProperties = [];

    public function __construct() {
        parent::__construct();
    }

}
