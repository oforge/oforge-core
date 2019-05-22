<?php

namespace Blog\Exceptions;

use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class CommentNotFoundException
 *
 * @package Blog\Exceptions
 */
class CommentNotFoundException extends BlogException {

    /**
     * CommentNotFoundException constructor.
     *
     * @param string $commentID
     */
    public function __construct(string $commentID) {
        parent::__construct(sprintf(#
            I18N::translate('plugin_blog_exception_comment_not_found', 'Comment with ID "%s" not found!'),#
            $commentID#
        ));
    }

}
