<?php

namespace Oforge\Engine\Modules\Mailer\Services;

use Pelago\Emogrifier;

class InlineCssService {
    /**
     * @param $html
     * @param $css
     *
     * @return string
     */
    public function renderInlineCss($html, $css = null) {

        /** @var  $emogrifier */
        $cssInliner = new Emogrifier($html);

        if(isset($css)) {
            $cssInliner->setCss($css);
        }

        return $cssInliner->emogrify();
    }
}