<?php

namespace Oforge\Engine\Modules\Core\Services;

class RedirectService {

    public function getRedirectUrlName() {
        $urlName = null;

        if (isset ($_SESSION['redirectUrlName'])) {
            $urlName = $_SESSION['redirectUrlName'];
            unset($_SESSION['redirectUrlName']);
        }
        return $urlName;
    }

    public function hasRedirectUrlName() {
        return (isset($_SESSION['redirectUrlName']));
    }

    public function setRedirectUrlName($name) {
        $_SESSION['redirectUrlName'] = $name;
    }
}