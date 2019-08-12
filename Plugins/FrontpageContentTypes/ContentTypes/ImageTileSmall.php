<?php

namespace FrontpageContentTypes\ContentTypes;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Media\Models\Media;
use Oforge\Engine\Modules\Media\Services\MediaService;

class ImageTileSmall extends AbstractContentType {
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
        $contentData = $this->getContentData();

        $data = [
            'id'              => $this->getContentId(),
            'type'            => $this->getId(),
            'name'            => $this->getContentName(),
            'css'             => $this->getContentCssClass(),
            'url'             => ArrayHelper::get($contentData, 'url'),
            'caption'         => ArrayHelper::get($contentData, 'caption'),
            'text'            => ArrayHelper::get($contentData, 'text'),
            'link'            => ArrayHelper::get($contentData, 'link'),
            'linktext'        => ArrayHelper::get($contentData, 'linktext'),
            'backgroundcolor' => ArrayHelper::get($contentData, 'backgroundcolor'),
            'fontcolor'       => ArrayHelper::get($contentData, 'fontcolor'),
            'targetBlank'     => ArrayHelper::get($contentData, 'targetBlank', false),
        ];

        return $data;
    }

    /**
     * Set edit data for page builder of content type
     *
     * @param $data
     *
     * @return ImageTileSmall $this
     * @throws ServiceNotFoundException
     */
    public function setEditData($data) {
        $contentData = [
            'url'             => $this->getContentData()['url'],
            'caption'         => ArrayHelper::get($data, 'caption', ''),
            'text'            => ArrayHelper::get($data, 'text', ''),
            'link'            => ArrayHelper::get($data, 'link', ''),
            'linktext'        => ArrayHelper::get($data, 'linktext', ''),
            'backgroundcolor' => ArrayHelper::get($data, 'backgroundcolor', ''),
            'fontcolor'       => ArrayHelper::get($data, 'fontcolor', ''),
            'targetBlank'     => ArrayHelper::get($data, 'targetBlank', false),
        ];

        if (isset($_FILES["image"])) {
            /** @var MediaService $configService */
            $mediaService = Oforge()->Services()->get('media');

            /** @var Media $media */
            $media = $mediaService->add($_FILES["image"]);
            if (isset($media)) {
                $contentData['url'] = $media->getPath();
            }
        }

        $this->setContentData($contentData);

        return $this;
    }

    /**
     * Return data for page rendering of content type
     *
     * @return array
     */
    public function getRenderData() {
        $contentData = $this->getContentData();

        $data = [
            'form'            => "ContentTypes/" . $this->getPath() . "/PageBuilderForm.twig",
            'type'            => "ContentTypes/" . $this->getPath() . "/PageBuilder.twig",
            'typeId'          => $this->getId(),
            'isContainer'     => $this->isContainer(),
            'css'             => ArrayHelper::get($contentData, 'css'),
            'url'             => ArrayHelper::get($contentData, 'url'),
            'caption'         => ArrayHelper::get($contentData, 'caption'),
            'text'            => ArrayHelper::get($contentData, 'text'),
            'link'            => ArrayHelper::get($contentData, 'link'),
            'linktext'        => ArrayHelper::get($contentData, 'linktext'),
            'backgroundcolor' => ArrayHelper::get($contentData, 'backgroundcolor'),
            'fontcolor'       => ArrayHelper::get($contentData, 'fontcolor'),
            'targetBlank'     => ArrayHelper::get($contentData, 'targetBlank', false),
        ];

        return $data;
    }

    /**
     * Create a child of given content type
     *
     * @param Content $contentEntity
     * @param int $order
     *
     * @return ImageTileSmall $this
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
     * @return ImageTileSmall $this
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
