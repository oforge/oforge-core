<?php

namespace Oforge\Engine\Modules\Mailer\Services;

use Pelago\Emogrifier;

class InlineCssService {
    /**
     * HTML / CSS input needs to be UTF-8 encoded.
     *
     * @param $html
     * @param $css
     *
     * @return string HTML with inline-CSS
     */
    public function renderInlineCss($html, $css = null) {

        /** @var  $cssInliner */
        $cssInliner = new Emogrifier($html);

        if(isset($css)) {
            $cssInliner->setCss($css);
        }

        return $cssInliner->emogrify();
    }
}