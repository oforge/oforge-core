<?php

namespace Oforge\Engine\Modules\Core\Helper;

class MimeTypes {
    public const IMAGE_GIF  = 'image/gif';
    public const IMAGE_JPG  = 'image/jpeg';
    public const IMAGE_PNG  = 'image/png';
    public const IMAGE_SVG  = 'image/svg+xml';
    public const IMAGE_WEBP = 'image/webp';

    public static function getExtension(string $mimeType) {
        switch ($mimeType) {
            case MimeTypes::IMAGE_GIF:
                return 'gif';
            case MimeTypes::IMAGE_JPG:
                return 'jpg';
            case MimeTypes::IMAGE_PNG:
                return 'png';
            case MimeTypes::IMAGE_SVG:
                return 'svg';
            case MimeTypes::IMAGE_WEBP:
                return 'webp';
            default:
                return null;
        }
    }
}
