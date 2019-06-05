<?php


namespace FrontpageContentTypes\ContentTypes;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\CMS\ContentTypes\Image;
use Oforge\Engine\Modules\CMS\ContentTypes\RichText;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\Media\Services\MediaService;


class IconTileBasic extends AbstractContentType
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
     * Return edit data for page builder of content type
     *
     * @return array
     */
    public function getEditData()
    {
        $contentData = $this->getContentData();

        $data = [
            'id' => $this->getContentId(),
            'type' => $this->getId(),
            'name' => $this->getContentName(),
            'css' => $this->getContentCssClass(),
            'url' => $contentData['url'],
            'caption' => $contentData['caption'],
            'link' => $contentData['link'],
            'backgroundcolor' => $contentData['backgroundcolor'],
            'fontcolor' => $contentData['fontcolor']
        ];

        return $data;
    }

    /**
     * Set edit data for page builder of content type
     * @param $data
     * @return IconTileBasic $this
     */
    public function setEditData($data)
    {
        $contentData = [
            'url' => $this->getContentData()['url'],
            'caption' => $data['caption'],
            'link' => $data['link'],
            'backgroundcolor' => $data['backgroundcolor'],
            'fontcolor' => $data['fontcolor']
        ];

        if (isset($_FILES["icon"])) {

            /** @var MediaService $configService */
            $mediaService = Oforge()->Services()->get('media');
            $media = $mediaService->add($_FILES["icon"]);
            if (isset($media)) {
                $contentData['url'] = $media->getPath();
            }
        }



        $this->setContentData($contentData);
        $this->setContentName($data['name']);
        $this->setContentCssClass($data['css']);

        return $this;
    }

    /**
     * Return data for page rendering of content type
     *
     * @return array
     */
    public function getRenderData()
    {
        $contentData = $this->getContentData();

        $data = [
            'form' => "ContentTypes/" . $this->getPath() . "/PageBuilderForm.twig",
            'type' => "ContentTypes/" . $this->getPath() . "/PageBuilder.twig",
            'typeId' => $this->getId(),
            'isContainer' => $this->isContainer(),
            'css' => $this->getContentCssClass(),
            'url' => $contentData['url'],
            'caption' => $contentData['caption'],
            'link' => $contentData['link'],
            'backgroundcolor' => $contentData['backgroundcolor'],
            'fontcolor' => $contentData['fontcolor']
        ];

        return $data;
    }

    /**
     * Create a child of given content type
     * @param Content $contentEntity
     * @param int $order
     *
     * @return IconTileBasic $this
     */
    public function createChild($contentEntity, $order)
    {
        return $this;
    }

    /**
     * Delete a child
     * @param Content $contentEntity
     * @param int $order
     *
     * @return IconTileBasic $this
     */
    public function deleteChild($contentEntity, $order)
    {
        return $this;
    }

    /**
     * Return child data of content type
     *
     * @return array|false should return false if no child content data is available
     */
    public function getChildData()
    {
        return false;
    }
}