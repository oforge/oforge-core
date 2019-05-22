<?php

namespace Blog\Exceptions;

use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class UserNotLoggedInException
 *
 * @package Blog\Exceptions
 */
class UserNotLoggedInException extends BlogException {

    /**
     * UserNotLoggedInException constructor.
     */
    public function __construct() {
        parent::__construct(I18N::translate('plugin_blog_exception_user_not_logged_in', 'User not logged in!'));
    }

}
