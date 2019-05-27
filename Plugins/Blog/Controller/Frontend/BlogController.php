<?php

namespace Blog\Controller\Frontend;

use Blog\Enums\BlogPermission;
use Blog\Services\AuthService;
use Blog\Services\CategoryService;
use Blog\Services\CommentService;
use Blog\Services\PostService;
use Blog\Services\RatingService;
use Doctrine\ORM\ORMException;
use Exception;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RedirectHelper;
use Oforge\Engine\Modules\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class BlogController
 *
 * @package Blog\Controller\Frontend\Blog
 * @EndpointClass(path="/blog", name="frontend_blog", assetScope="Frontend")
 */
class BlogController extends SecureFrontendController {
    /** @var AuthService $authService */
    private $authService;
    /** @var CategoryService $categoryService */
    private $categoryService;
    /** @var CommentService $commentService */
    private $commentService;
    /** @var PostService $postService */
    private $postService;
    /** @var RatingService $ratingService */
    private $ratingService;

    /**
     * BlogController constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        $this->authService     = Oforge()->Services()->get('blog.auth');
        $this->categoryService = Oforge()->Services()->get('blog.category');
        $this->commentService  = Oforge()->Services()->get('blog.comment');
        $this->postService     = Oforge()->Services()->get('blog.post');
        $this->ratingService   = Oforge()->Services()->get('blog.rating');
    }

    public function initPermissions() {
        $this->ensurePermissions('indexAction', User::class, BlogPermission::PUBLIC);
        $this->ensurePermissions('viewPostAction', User::class, BlogPermission::PUBLIC);
        $this->ensurePermissions('createCommentAction', User::class, BlogPermission::LOGGED);
        $this->ensurePermissions('deleteCommentAction', User::class, BlogPermission::LOGGED);
        $this->ensurePermissions('leaveRatingAction', User::class, BlogPermission::LOGGED);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @throws ConfigElementNotFoundException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="[/[{categoryID:\d+}[/[{seoUrlPath}[/]]]]]")
     */
    public function indexAction(Request $request, Response $response, array $args) {
        /**
         * @var ConfigService $configService
         */
        $configService = Oforge()->Services()->get('config');
        $viewData      = [
            'postsPerPage' => $configService->get('blog_load_more_epp_posts'),
            'categories'   => $this->prepareCategories(),
            'posts'        => [],
        ];
        $categoryID    = null;
        if (isset($args['categoryID'])) {
            $categoryID = $args['categoryID'];
            try {
                $category = $this->categoryService->getCategoryByID($categoryID);
                if (isset($category)) {
                    $viewData['category'] = $category->toArray(1, ['posts']);
                }
            } catch (ORMException $exception) {
                Oforge()->Logger()->logException($exception);
            }
        }
        try {
            $posts     = $this->postService->getPosts(0, $categoryID);
            $postsData = [];

            $excludeProperties = isset($categoryID) ? ['category', 'comments', 'author'] : ['category' => ['posts'], 'comments', 'author'];
            foreach ($posts as $post) {
                $postData = $post->toArray(2, $excludeProperties);
                $this->ratingService->evaluateRating($postData);
                $postData['created'] = $post->getCreated()->format('Y.m.d H:i:s');
                $postsData[]         = $postData;
            }
            $viewData['posts'] = $postsData;
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }
        Oforge()->View()->assign([
            'blog' => $viewData,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @throws ConfigElementNotFoundException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/post/{postID:\d+}[/[{seoUrlPath}[/]]]", name="view")
     */
    public function viewPostAction(Request $request, Response $response, array $args) {
        /**
         * @var ConfigService $configService
         */
        $configService = Oforge()->Services()->get('config');
        $viewData      = [
            'commentsPerPage' => $configService->get('blog_load_more_epp_comments'),
            // 'categories'   => $this->prepareCategories(),
        ];
        $postID        = $args['postID'];

        try {
            $post     = $this->postService->getPost($postID);
            $postData = $post->toArray(2, [/*'author' => ['*', '!id'], */'category' => ['posts'], 'comments']);
            $this->ratingService->evaluateRating($postData);
            $postData['created'] = $post->getCreated()->format('Y.m.d H:i:s');
            $postData['updated'] = $post->getCreated()->format('Y.m.d H:i:s');
            $postCategoryData    = $postData['category'];
            unset($postData['category']);
            $viewData['category'] = $postCategoryData;
            $viewData['post']     = $postData;

            $comments     = $this->commentService->getCommentsOfPost($post);
            $commentsData = [];
            foreach ($comments as $comment) {
                $commentsData[] = $comment->toArray(1, ['author' => ['*', '!id'], 'post']);//TODO author name
            }
            $viewData['comments']       = $commentsData;
            $viewData['comments_count'] = $post->getComments()->count();

        } catch (ORMException $exception) {
            Oforge()->Logger()->logException($exception);
        }

        // TODO
        Oforge()->View()->assign([
            'blog' => $viewData,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/login/{postID:\d+}", name="login", method=EndpointMethod::POST)
     */
    public function loginProcessAction(Request $request, Response $response, array $args) {
        // TODO
        Oforge()->View()->assign(['tmp' => $args]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/registration/{postID:\d+}", name="registration", method=EndpointMethod::POST)
     */
    public function registrationProcessAction(Request $request, Response $response, array $args) {
        // TODO
        Oforge()->View()->assign(['tmp' => $args]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/post/{postID:\d+}/{seoUrlPath}/comments/create", name="create_comment", method=EndpointMethod::ANY)
     */
    public function createCommentAction(Request $request, Response $response, array $args) {
        // TODO
        Oforge()->View()->assign(['tmp' => $args]);
        // Oforge()->View()->assign(['blog' => $this->getUserData()]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/post/{postID:\d+}/{seoUrlPath}/comments/delete/{commentID}", name="delete_comment", method=EndpointMethod::POST)
     *
     * @return Response
     */
    public function deleteCommentAction(Request $request, Response $response, array $args) {
        // TODO BlogController#deleteCommentAction

        Oforge()->View()->Flash()->addMessage('success', I18N::translate('backend_crud_msg_create_success', 'Entity successfully created.'));

        return RedirectHelper::redirect($response, 'frontend_blog_view', $args);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/post/{postID:\d+}/{seoUrlPath}/more-comments/{page:\d+}", name="more_comments", method=EndpointMethod::GET)
     */
    public function loadMoreCommentsAction(Request $request, Response $response, array $args) {
        // TODO BlogController#loadMoreCommentsAction
        Oforge()->View()->assign(['json' => ['tmp' => $args]]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/more-posts/{categoryID:\d+}/{page:\d+}", name="more_posts", method=EndpointMethod::GET)
     */
    public function loadMorePostsAction(Request $request, Response $response, array $args) {
        // TODO BlogController#loadMoreCommentsAction
        Oforge()->View()->assign(['json' => ['tmp' => $args]]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/post/{postID:\d+}/{seoUrlPath}/leave-rating/{rating:up|down}[/]", name="leave_rating", method=EndpointMethod::ANY)
     */
    public function leaveRatingAction(Request $request, Response $response, array $args) {
        // TODO
        Oforge()->View()->assign(['tmp' => $args]);
    }

    /** @return array */
    protected function prepareCategories() : array {
        $data = [];
        try {
            $categories = $this->categoryService->getCategories();
            foreach ($categories as $category) {
                $data[] = $category->toArray(1, ['posts']);
            }
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }

        return $data;
    }

}
