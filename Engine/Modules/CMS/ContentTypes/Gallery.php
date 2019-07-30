<?php

namespace Oforge\Engine\Modules\CMS\ContentTypes;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;

/**
 * Class Gallery
 *
 * @package Oforge\Engine\Modules\CMS\ContentTypes
 */
class Gallery extends AbstractContentType {

    /**
     * Return whether or not content type is a container type like a row
     *
     * @return bool true|false
     */
    public function isContainer() : bool {
        return false;
    }

    /**
     * Return edit data for page builder of content type
     *
     * @return array
     */
    public function getEditData() {
        $data           = [];
        $data['id']     = $this->getContentId();
        $data['type']   = $this->getId();
        $data['name']   = $this->getContentName();
        $data['css']    = $this->getContentCssClass();
        $data['url']    = $this->getContentData();
        $data['images'] = $this->getContentData();

        return $data;
    }

    /**
     * Set edit data for page builder of content type
     *
     * @param $data
     *
     * @return Gallery $this
     */
    public function setEditData($data) {
        $this->setContentData(ArrayHelper::get($data, 'images', []));

        return $this;
    }

    /**
     * Return data for page rendering of content type
     *
     * @return array
     */
    public function getRenderData() {
        $data                = [];
        $data['form']        = 'ContentTypes/' . $this->getPath() . '/PageBuilderForm.twig';
        $data['type']        = 'ContentTypes/' . $this->getPath() . '/PageBuilderPreview.twig';
        $data['typeId']      = $this->getId();
        $data['isContainer'] = $this->isContainer();
        $data['css']         = $this->getContentCssClass();
        $data['images']      = $this->getContentData();

        return $data;
    }

    /**
     * Create a child of given content type
     *
     * @param Content $contentEntity
     * @param int $order
     *
     * @return Gallery $this
     */
    public function createChild($contentEntity, $order) {
        return $this;
    }

    /**
     * Delete a child
     *
     * @param Content $contentEntity
     * @param int $order
     *
     * @return Gallery $this
     */
    public function deleteChild($contentEntity, $order) {
        return $this;
    }

    /**
     * Return child data of content type
     *
     * @return array|false should return false if no child content data is available
     */
    public function getChildData() {
        return false;
    }
}
