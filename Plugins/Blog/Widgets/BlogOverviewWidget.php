<?php

namespace Blog\Widgets;

use Blog\Models\Category;
use Blog\Models\Comment;
use Blog\Models\Post;
use Blog\Models\Rating;
use Oforge\Engine\Modules\AdminBackend\Core\Widgets\DashboardWidgetInterface;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class BlogDashboardWidget
 *
 * @package Blog\Widgets
 */
class BlogOverviewWidget extends AbstractDatabaseAccess implements DashboardWidgetInterface {

    public function __construct() {
        parent::__construct([
            Category::class => Category::class,
            Post::class     => Post::class,
            Comment::class  => Comment::class,
            Rating::class   => Rating::class,
        ]);
    }

    /** @inheritDoc */
    function getData() : array {
        return [
            'categories' => $this->repository(Category::class)->count([]),
            'posts'      => $this->repository(Post::class)->count([]),
            'comments'   => $this->repository(Comment::class)->count([]),
            'ratings'    => $this->repository(Rating::class)->count([]),
        ];
    }

}
