<?php

namespace Oforge\Engine\Modules\Mailer\Services;

use Pelago\Emogrifier;

/**
 * Class InlineCssService
 *
 * @package Oforge\Engine\Modules\Mailer\Services
 */
class InlineCssService
{

    /**
     * HTML / CSS input needs to be UTF-8 encoded.
     *
     * @param string $html
     * @param string|null $css
     *
     * @return string HTML with inline-CSS
     */
    public function inline(string $html, ?string $css = null) : string
    {
        $emogrifier = new Emogrifier($html);
        if ($css !== null) {
            $emogrifier->setCss($css);
        }

        return $emogrifier->emogrify();
    }

}
