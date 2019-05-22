<?php

namespace Blog\Controller\Frontend;

use Blog\Services\AuthService;
use Blog\Services\CategoryService;
use Blog\Services\CommentService;
use Blog\Services\PostService;
use Blog\Services\RatingService;
use Doctrine\ORM\ORMException;
use Exception;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Models\Endpoint\EndpointMethod;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class BlogController
 *
 * @package Blog\Controller\Frontend\Blog
 * @EndpointClass(path="/blog", name="frontend_blog", assetScope="Frontend")
 */
class BlogController extends AbstractController {
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

    public function __construct() {
        $this->authService     = Oforge()->Services()->get('blog.auth');
        $this->categoryService = Oforge()->Services()->get('blog.category');
        $this->commentService  = Oforge()->Services()->get('blog.comment');
        $this->postService     = Oforge()->Services()->get('blog.post');
        $this->ratingService   = Oforge()->Services()->get('blog.rating');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="[/[{categoryID:\d+}[/[{seoUrlPath}[/]]]]]")
     */
    public function indexAction(Request $request, Response $response, array $args) {
        $viewData               = [];
        $viewData['categories'] = $this->prepareCategories();
        $categoryID             = null;
        if (isset($args['categoryID'])) {
            $categoryID = $args['categoryID'];
            try {
                $category = $this->categoryService->getCategoryByID($categoryID);
                if (isset($category)) {
                    $viewData['category'] = $category->toArray(1);
                }
            } catch (ORMException $exception) {
                //TODO
            }
        }
        try {
            $posts     = $this->postService->getPosts(0, $categoryID);
            $postsData = [];

            $blacklistProperties = isset($categoryID) ? ['category', 'comments', 'author'] : ['comments', 'author'];
            foreach ($posts as $post) {
                $postData = $post->toArray(2, $blacklistProperties);
                $this->ratingService->evaluateRating($postData);
                $postData['created'] = $post->getCreated()->format('Y.m.d H:i:s');
                $postsData[] = $postData;
            }
            $viewData['posts'] = $postsData;
        } catch (Exception $exception) {
            //TODO
        }
        // TODO
        // print_r($args);
        Oforge()->View()->assign(['meta.route.params' => $args]);
        Oforge()->View()->assign(['blog' => $viewData]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/post/{postID:\d+}[/[{seoUrlPath}[/]]]", name="view")
     */
    public function viewPostAction(Request $request, Response $response, array $args) {
        // TODO
        // print_r($args);
        Oforge()->View()->assign(['meta.route.params' => $args]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/login/{postID:\d+}", name="login", method=EndpointMethod::POST)
     */
    public function loginProcessAction(Request $request, Response $response, array $args) {
        // TODO
        Oforge()->View()->assign(['meta.route.params' => $args]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/registration/{postID:\d+}", name="registration", method=EndpointMethod::POST)
     */
    public function registrationProcessAction(Request $request, Response $response, array $args) {
        // TODO
        Oforge()->View()->assign(['meta.route.params' => $args]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/post/{postID:\d+}/{seoUrlPath}/comments/create", name="create_comment", method=EndpointMethod::ANY)
     */
    public function createCommentAction(Request $request, Response $response, array $args) {
        // TODO
        Oforge()->View()->assign(['meta.route.params' => $args]);
        // Oforge()->View()->assign(['blog' => $this->getUserData()]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/post/{postID:\d+}/{seoUrlPath}/comments/delete/{commentID}", name="delete_comment", method=EndpointMethod::DELETE)
     */
    public function deleteCommentAction(Request $request, Response $response, array $args) {
        // TODO
        Oforge()->View()->assign(['meta.route.params' => $args]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/post/{postID:\d+}/{seoUrlPath}/comments/more/{page:\d+}", name="more_comments", method=EndpointMethod::GET)
     */
    public function loadMoreCommentsAction(Request $request, Response $response, array $args) {
        // TODO
        Oforge()->View()->assign(['meta.route.params' => $args]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/post/{postID:\d+}/{seoUrlPath}/leave-rating", name="leave_rating", method=EndpointMethod::POST)
     */
    public function leaveRatingAction(Request $request, Response $response, array $args) {
        // TODO
        Oforge()->View()->assign(['meta.route.params' => $args]);
    }

    /** @return array */
    protected function prepareCategories() : array {
        $data = [];
        try {
            $categories = $this->categoryService->getCategories();
            foreach ($categories as $category) {
                $data[] = $category->toArray(1);
            }
        } catch (Exception $exception) {
            //TODO
        }

        return $data;
    }

    protected function prepareBreadrump() {
        // TODO
    }

}
