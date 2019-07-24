<?php

namespace Blog\Controller\Frontend;

use Blog\Enums\BlogPermission;
use Blog\Exceptions\PostNotFoundException;
use Blog\Exceptions\UserNotLoggedInException;
use Blog\Exceptions\UserRatingForPostNotFoundException;
use Blog\Services\CategoryService;
use Blog\Services\CommentService;
use Blog\Services\PostService;
use Blog\Services\RatingService;
use Doctrine\ORM\ORMException;
use Exception;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\FrontendUserService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
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
    /** @var FrontendUserService $frontendUserService */
    private $frontendUserService;
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
        $this->categoryService     = Oforge()->Services()->get('blog.category');
        $this->commentService      = Oforge()->Services()->get('blog.comment');
        $this->postService         = Oforge()->Services()->get('blog.post');
        $this->ratingService       = Oforge()->Services()->get('blog.rating');
        $this->frontendUserService = Oforge()->Services()->get('frontend.user');
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
        /** @var ConfigService $configService */
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
                // $postData['created'] = DateTimeFormatter::datetime($post->getCreated());
                $postsData[] = $postData;
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

        $postID = $args['postID'];
        try {
            $post     = $this->postService->getPost($postID);
            $postData = $post->toArray(2, ['author' => ['*', '!name'], 'category' => ['posts'], 'comments']);
            $this->ratingService->evaluateRating($postData);
            // $postData['created'] = DateTimeFormatter::datetime($post->getCreated());
            // $postData['updated'] = DateTimeFormatter::datetime($post->getUpdated());
            $postCategoryData = $postData['category'];
            unset($postData['category']);
            $viewData['category'] = $postCategoryData;
            $viewData['post']     = $postData;

            $comments     = $this->commentService->getCommentsOfPost($post, 0);
            $commentsData = [];
            foreach ($comments as $comment) {
                $data = $comment->toArray(2, ['author' => ['*', '!detail'], 'post']);
                // $data['created'] = DateTimeFormatter::datetime($comment->getCreated());
                $commentsData[] = $data;
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
            $viewData['userLoggedIn'] = $this->frontendUserService->isLoggedIn();

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
            if (!empty($userID) && !empty($comment) && $userID == Oforge()->View()->get('current_user.id')) {
                $twigFlash = Oforge()->View()->Flash();
                try {
                    $this->commentService->createComment([
                        'author'  => $userID,
                        'content' => $comment,
                        'post'    => $args['postID'],
                    ]);
                    $twigFlash->addMessage('success', I18N::translate('plugin_blog_comment_creating_success', [
                        'en' => 'Your comment has been added successfully.',
                        'de' => 'Ihr Kommentar wurde erfolgreich hinzugefügt.',
                    ]));
                } catch (UserNotLoggedInException $exception) {
                    $twigFlash->addMessage('warning', I18N::translate('plugin_blog_comment_user_not_logged_in', [
                        'en' => 'You must be logged in to leave a comment.',
                        'de' => 'Sie müssen angemeldet sein, um einen Kommentar zu hinterlassen.',
                    ]));
                } catch (Exception $exception) {
                    Oforge()->Logger()->logException($exception);
                    $twigFlash->addMessage('error', I18N::translate('plugin_blog_comment_creating_failed', [
                        'en' => 'The comment could not be saved.',
                        'de' => 'Der Kommentar konnte nicht gespeichert werden.',
                    ]));
                }
            }
        }

        return RouteHelper::redirect($response, 'frontend_blog_view', $args);
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
        // TODO FEATURE/later BlogController#deleteCommentAction

        return RouteHelper::redirect($response, 'frontend_blog_view', $args);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @EndpointAction(path="/post/{postID:\d+}/{seoUrlPath}/comments/report/{commentID}", name="report_comment", method=EndpointMethod::POST)
     *
     * @return Response
     */
    public function reportCommentAction(Request $request, Response $response, array $args) {
        // TODO FEATURE/later BlogController#reportCommentAction

        return RouteHelper::redirect($response, 'frontend_blog_view', $args);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/post/{postID:\d+}/{seoUrlPath}/comments/more", name="more_comments", method=EndpointMethod::GET)
     */
    public function loadMoreCommentsAction(Request $request, Response $response, array $args) {
        $queryParams = $request->getQueryParams();
        $postID      = $args['postID'];
        $page        = -1 + ArrayHelper::get($queryParams, 'page', 1);
        try {
            $comments     = $this->commentService->getComments($postID, $page);
            $commentsData = [];
            foreach ($comments as $comment) {
                $data           = $comment->toArray(2, ['author' => ['*', '!detail'], 'post']);
                $commentsData[] = $data;
            }
            Oforge()->View()->assign([
                'blog.comments' => $commentsData,
            ]);
        } catch (PostNotFoundException $exception) {
            Oforge()->Logger()->logException($exception);

            return $response->write('');
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);

            return $response->write('');
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/more-posts", name="more_posts", method=EndpointMethod::GET)
     */
    public function loadMorePostsAction(Request $request, Response $response, array $args) {
        $queryParams = $request->getQueryParams();
        $categoryID  = ArrayHelper::get($queryParams, 'categoryID', null);
        $page        = -1 + ArrayHelper::get($queryParams, 'page', 1);

        try {
            $posts             = $this->postService->getPosts($page, $categoryID);
            $postsData         = [];
            $excludeProperties = isset($categoryID) ? ['category', 'comments', 'author'] : ['category' => ['posts'], 'comments', 'author'];
            foreach ($posts as $post) {
                $postData = $post->toArray(2, $excludeProperties);
                $this->ratingService->evaluateRating($postData);
                $postsData[] = $postData;
            }
            Oforge()->View()->assign([
                'blog.posts' => $postsData,
            ]);
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);

            return $response->write('');
        }
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
            $twigFlash->addMessage('success', I18N::translate('plugin_blog_rating_saving_success', [
                'en' => 'Your post rating has been saved.',
                'de' => 'Deine Beitragsbewertung wurde gespeichert.',
            ]));
        } catch (UserNotLoggedInException $exception) {
            $twigFlash->addMessage('warning', I18N::translate('plugin_blog_rating_user_not_logged_in', [
                'en' => 'You must be logged in to leave a rating.',
                'de' => 'Du musst angemeldet sein, um eine Bewertung abzugeben.',
            ]));
        } catch (ORMException $exception) {
            Oforge()->Logger()->logException($exception);
            $twigFlash->addMessage('error', I18N::translate('plugin_blog_rating_saving_failed', [
                'en' => 'The rating could not be saved.',
                'de' => 'Die Bewertung konnte nicht gespeichert werden.',
            ]));
        }

        return RouteHelper::redirect($response, 'frontend_blog_view', $args);
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
