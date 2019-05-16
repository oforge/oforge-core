<?php

namespace Blog\Controller\Backend;

use Blog\Models\Post;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;

/**
 * Class PostController
 *
 * @package Blog\Controller\Backend\Blog
 * @EndpointClass(path="/backend/blog/posts", name="backed_blog_posts", assetScope="Backend")
 */
class PostController extends BaseCrudController {
    /** @var string $model */
    protected $model = Post::class;
    /** @var array $modelProperties */
    protected $modelProperties = [];

    public function __construct() {
        parent::__construct();
    }

}
