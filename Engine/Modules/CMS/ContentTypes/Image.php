<?php

namespace Oforge\Engine\Modules\CMS\ContentTypes;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;

class Image extends AbstractContentType
{
    /**
     * Return whether or not content type is a container type like a row
     *
     * @return bool true|false
     */
    public function isContainer(): bool
    {
        return false;
    }
    
    /**
     * Return data of content type
     *
     * @return mixed
     */
    public function getData()
    {
        return Null;
    }
    
    /**
     * Set data of content type
     * @param mixed $data
     *
     * @return ContentType $this
     */
    public function setData($data)
    {
        return $this;
    }
}
