<?php

namespace Blog\Exceptions;

use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class PostNotFoundException
 *
 * @package Blog\Exceptions
 */
class PostNotFoundException extends BlogException {

    /**
     * PostNotFoundException constructor.
     *
     * @param int $postID
     */
    public function __construct(int $postID) {
        parent::__construct(sprintf(#
            I18N::translate('plugin_blog_exception_post_not_found', 'Post with ID "%s" not found!'),#
            $postID#
        ));
    }

}
