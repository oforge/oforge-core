<?php

namespace Blog\Exceptions;

use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class UserRatingForPostNotFoundException
 *
 * @package Blog\Exceptions
 */
class UserRatingForPostNotFoundException extends BlogException {

    /**
     * UserRatingForPostNotFoundException constructor.
     *
     * @param int $postID
     */
    public function __construct(int $postID) {
        parent::__construct(sprintf(#
            I18N::translate('plugin_blog_exception_user_post_rating_not_found', 'User rating for post with ID "%s" not found!'),#
            $postID#
        ));
    }

}
