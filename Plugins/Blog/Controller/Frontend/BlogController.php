<?php

namespace Blog\Controller\Frontend;

use Blog\Enums\BlogPermission;
use Blog\Exceptions\UserNotLoggedInException;
use Blog\Exceptions\UserRatingForPostNotFoundException;
use Blog\Services\CategoryService;
use Blog\Services\CommentService;
use Blog\Services\PostService;
use Blog\Services\RatingService;
use Blog\Services\UserService;
use Doctrine\ORM\ORMException;
use Exception;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\LoggerAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
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
    /** @var UserService $userService */
    private $userService;
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
     * @throws LoggerAlreadyExistException
     */
    public function __construct() {
        $this->categoryService = Oforge()->Services()->get('blog.category');
        $this->commentService  = Oforge()->Services()->get('blog.comment');
        $this->postService     = Oforge()->Services()->get('blog.post');
        $this->ratingService   = Oforge()->Services()->get('blog.rating');
        $this->userService     = Oforge()->Services()->get('blog.user');
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
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $viewData      = [
            'commentsPerPage' => $configService->get('blog_load_more_epp_comments'),
            // 'categories'   => $this->prepareCategories(),
        ];
        $postID        = $args['postID'];

        try {
            $post     = $this->postService->getPost($postID);
            $postData = $post->toArray(2, ['author' => ['*', '!name'], 'category' => ['posts'], 'comments']);
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
                $data            = $comment->toArray(2, ['author' => ['*', '!detail'], 'post']);
                $data['created'] = $comment->getCreated()->format('Y.m.d H:i:s');
                $commentsData[]  = $data;
            }
            $viewData['comments']       = $commentsData;
            $viewData['comments_count'] = $post->getComments()->count();

            $userRating = null;
            try {
                $userRating = $this->ratingService->getUserRatingOfPost($post);
            } catch (UserNotLoggedInException $exception) {
                // do nothing
            } catch (UserRatingForPostNotFoundException $exception) {
                // do nothing
            } catch (ORMException $exception) {
                Oforge()->Logger()->logException($exception);
            }
            $viewData['userRating']   = $userRating;
            $viewData['userLoggedIn'] = $this->userService->isLoggedIn();

        } catch (ORMException $exception) {
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
     * @EndpointAction(path="/post/{postID:\d+}/{seoUrlPath}/comments/create", name="create_comment", method=EndpointMethod::POST)
     *
     * @return Response
     */
    public function createCommentAction(Request $request, Response $response, array $args) {
        $postData = $request->getParsedBody();
        if ($request->isPost() && !empty($postData)) {
            $userID  = ArrayHelper::get($postData, 'userID');
            $comment = ArrayHelper::get($postData, 'comment');
            if (!empty($userID) && !empty($comment) && $userID == Oforge()->View()->get('user.id')) {
                $twigFlash = Oforge()->View()->Flash();
                try {
                    $this->commentService->createComment([
                        'author'  => $userID,
                        'content' => $comment,
                        'post'    => $args['postID'],
                    ]);
                    $twigFlash->addMessage('success', I18N::translate('plugin_blog_comment_creating_success', 'Your comment has been added successfully.'));
                } catch (UserNotLoggedInException $exception) {
                    $twigFlash->addMessage('warning', I18N::translate('plugin_blog_comment_user_not_logged_in', 'You must be logged in to leave a comment.'));
                } catch (Exception $exception) {
                    Oforge()->Logger()->logException($exception);
                    $twigFlash->addMessage('error', I18N::translate('plugin_blog_comment_creating_failed', 'The comment could not be saved.'));
                }
            }
        }

        return RedirectHelper::redirect($response, 'frontend_blog_view', $args);
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
        // TODO later BlogController#deleteCommentAction

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
     *
     * @return Response
     */
    public function leaveRatingAction(Request $request, Response $response, array $args) {
        $postID      = $args['postID'];
        $ratingValue = $args['rating'] === 'up';
        $twigFlash   = Oforge()->View()->Flash();
        try {
            $this->ratingService->createOrUpdateRating($postID, $ratingValue);
            $twigFlash->addMessage('success', I18N::translate('plugin_blog_rating_saving_success', 'Your post rating has been saved.'));
        } catch (UserNotLoggedInException $exception) {
            $twigFlash->addMessage('warning', I18N::translate('plugin_blog_rating_user_not_logged_in', 'You must be logged in to leave a rating.'));
        } catch (ORMException $exception) {
            Oforge()->Logger()->logException($exception);
            $twigFlash->addMessage('error', I18N::translate('plugin_blog_rating_saving_failed', 'The rating could not be saved.'));
        }

        return RedirectHelper::redirect($response, 'frontend_blog_view', $args);
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
