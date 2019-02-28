<?php

namespace Oforge\Engine\Modules\CMS\ContentTypes;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;

class RichText extends AbstractContentType
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
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Set data of content type
     * @param string $richText
     *
     * @return ContentType $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
}
