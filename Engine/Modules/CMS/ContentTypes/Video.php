<?php

namespace Oforge\Engine\Modules\CMS\ContentTypes;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Media\Services\MediaService;

/**
 * Class Video
 *
 * @package Oforge\Engine\Modules\CMS\ContentTypes
 */
class Video extends AbstractContentType {

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
        $data['data'] = $this->getContentData();

        return $data;
    }

    /** @inheritDoc */
    public function setEditData($data) {
        $data = ArrayHelper::get($data, 'data', []);

        if (isset($_FILES['upload'])) {
            /** @var MediaService $mediaService */
            $mediaService = Oforge()->Services()->get('media');
            $media        = $mediaService->add($_FILES['upload']);
            if (isset($media)) {
                $data['mediaID'] = $media->getId();
                $data['path']    = $media->getPath();
            }
        }

        $this->setContentData($data);

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
        $data['data']        = $this->getContentData();

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
