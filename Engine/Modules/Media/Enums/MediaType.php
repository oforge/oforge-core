<?php

namespace Oforge\Engine\Modules\Media\Enums;

class MediaType
{
    public const BINARY = 'binary';
    public const IMAGE = 'image';
    public const VIDEO = 'video';
    public const DEFAULT = self::BINARY;

    private function __construct()
    {
    }

}
