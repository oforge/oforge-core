<?php

namespace Oforge\Engine\Modules\CMS\ContentTypes;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;

/**
 * Class Html
 *
 * @package Oforge\Engine\Modules\CMS\ContentTypes
 */
class Html extends AbstractContentType {

    /** @inheritDoc */
    public function isContainer() : bool {
        return false;
    }

    /** @inheritDoc */
    public function getEditData() {
        $data         = [];
        $data['id']   = $this->getContentId();
        $data['type'] = $this->getId();
        $data['name'] = $this->getContentName();
        $data['css']  = $this->getContentCssClass();
        $data['text'] = $this->getContentData();

        return $data;
    }

    /** @inheritDoc */
    public function setEditData($data) {
        $this->setContentData(ArrayHelper::get($data, 'text', ''));

        return $this;
    }

    /** @inheritDoc */
    public function getRenderData() {
        $data                = [];
        $data['form']        = 'ContentTypes/' . $this->getPath() . '/PageBuilderForm.twig';
        $data['type']        = 'ContentTypes/' . $this->getPath() . '/PageBuilderPreview.twig';
        $data['typeId']      = $this->getId();
        $data['isContainer'] = $this->isContainer();
        $data['css']         = $this->getContentCssClass();
        $data['text']        = $this->getContentData();

        return $data;
    }

    /** @inheritDoc */
    public function createChild($contentEntity, $order) {
        return $this;
    }

    /** @inheritDoc */
    public function deleteChild($contentEntity, $order) {
        return $this;
    }

    /** @inheritDoc */
    public function getChildData() {
        return false;
    }

}
