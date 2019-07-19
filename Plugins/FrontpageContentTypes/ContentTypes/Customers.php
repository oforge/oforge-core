<?php

namespace FrontpageContentTypes\ContentTypes;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;

class Customers extends AbstractContentType {
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
            'id'            => $this->getContentId(),
            'type'          => $this->getId(),
            'name'          => $this->getContentName(),
            'css'           => $this->getContentCssClass(),
            'customer_name' => ArrayHelper::get($contentData, 'customer_name'),
            'image'         => ArrayHelper::get($contentData, 'image'),
            'text'          => ArrayHelper::get($contentData, 'text'),
        ];

        return $data;
    }

    /**
     * Set edit data for page builder of content type
     *
     * @param $data
     *
     * @return Customer $this
     */
    public function setEditData($data) {
        $contentData = [
            'customer_name' => $data['customer_name'],
            'image'         => $data['image'],
            'text'          => $data['text'],
        ];

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
            'form'        => "ContentTypes/" . $this->getPath() . "/PageBuilderForm.twig",
            'type'        => "ContentTypes/" . $this->getPath() . "/PageBuilder.twig",
            'typeId'      => $this->getId(),
            'isContainer' => $this->isContainer(),

            'customer_name' => ArrayHelper::get($contentData, 'customer_name'),
            'image'         => ArrayHelper::get($contentData, 'image'),
            'text'          => ArrayHelper::get($contentData, 'text'),
        ];

        return $data;
    }

    /**
     * Create a child of given content type
     *
     * @param Content $contentEntity
     * @param int $order
     *
     * @return IconTileText $this
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
     * @return IconTileText $this
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
