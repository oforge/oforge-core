<?php

namespace Oforge\Engine\Modules\CMS\Abstracts;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;

abstract class AbstractContentType extends AbstractDatabaseAccess {
    protected $forgeEntityManager = null;

    private $contentTypeEntity = null;

    private $contentEntity = null;

    private $id = null;
    private $groupId = null;
    private $name = null;
    private $path = null;
    private $icon = null;
    private $classPath = null;

    private $contentId = null;
    private $contentParentId = null;
    private $contentName = null;
    private $contentCssClass = null;
    private $contentData = null;

    /**
     * AbstractContentType constructor.
     *
     * @throws ORMException
     */
    public function __construct() {
        parent::__construct(['contentType' => ContentType::class, 'content' => Content::class]);

        $this->forgeEntityManager = Oforge()->DB()->getForgeEntityManager();

        $this->contentTypeEntity = $this->repository('contentType')->findOneBy(["classPath" => get_class($this)]);

        $this->id          = $this->contentTypeEntity->getId();
        $this->groupId     = $this->contentTypeEntity->getGroup()->getId();
        $this->name        = $this->contentTypeEntity->getName();
        $this->path        = $this->contentTypeEntity->getPath();
        $this->icon        = $this->contentTypeEntity->getIcon();
        $this->class_path  = $this->contentTypeEntity->getClassPath();
    }

    /**
     * Return whether or not content type is a container type like a row
     *
     * @return bool true|false
     */
    abstract public function isContainer() : bool;

    /**
     * Return edit data for page builder of content type
     *
     * @return mixed
     */
    abstract public function getEditData();

    /**
     * Set edit data for page builder of content type
     *
     * @param mixed $data
     *
     * @return ContentType $this
     */
    abstract public function setEditData($data);

    /**
     * Return data for page rendering of content type
     *
     * @return mixed
     */
    abstract public function getRenderData();

    /**
     * Create a child of given content type
     *
     * @param Content $contentEntity
     * @param int $order
     *
     * @return ContentType $this
     */
    abstract public function createChild($contentEntity, $order);

    /**
     * Delete a child
     *
     * @param Content $contentEntity
     * @param int $order
     *
     * @return ContentType $this
     */
    abstract public function deleteChild($contentEntity, $order);

    /**
     * Return child data of content type
     *
     * @return array|false should return false if no child content data is available
     * If child data should be returned create an array of the following format:
     * $childContent["id"]      : integer        : fill with child content id
     * $childContent["content"] : content entity : fill with child content entity
     * $childContent["order"]   : integer        : fill with child content order index
     */
    abstract public function getChildData();

    /**
     * Return id of content type
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Return id of content type group
     *
     * @return int
     */
    public function getGroupId() {
        return $this->groupId;
    }

    /**
     * Return name of content type
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Return path of content type
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Return icon of content type
     *
     * @return string
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * Return class path of content type
     *
     * @return string
     */
    public function getClassPath() {
        return $this->classPath;
    }

    /**
     * Return id of content
     *
     * @return int
     */
    public function getContentId() {
        return $this->contentId;
    }

    /**
     * Return parent id of content
     *
     * @return int
     */
    public function getContentParentId() {
        return $this->contentParentId;
    }

    /**
     * Set parent id of content
     *
     * @param int $contentParentId
     *
     * @return AbstractContentType $this
     */
    public function setContentParentId(int $contentParentId) {
        $this->contentParentId = $contentParentId;

        return $this;
    }

    /**
     * Return name of content
     *
     * @return string
     */
    public function getContentName() {
        return $this->contentName;
    }

    /**
     * Set name of content
     *
     * @param string $contentName
     *
     * @return AbstractContentType $this
     */
    public function setContentName(string $contentName) {
        $this->contentName = $contentName;

        return $this;
    }

    /**
     * Return css class of content
     *
     * @return string
     */
    public function getContentCssClass() {
        return $this->contentCssClass;
    }

    /**
     * Set css class of content
     *
     * @param string $contentCssClass
     *
     * @return AbstractContentType $this
     */
    public function setContentCssClass(string $contentCssClass) {
        $this->contentCssClass = $contentCssClass;

        return $this;
    }

    /**
     * Return data of content
     *
     * @return mixed
     */
    public function getContentData() {
        return $this->contentData;
    }

    //TODO: Find out whether type string should be chosen

    /**
     * Set data of content
     *
     * @param $contentData
     *
     * @return AbstractContentType $this
     */
    public function setContentData($contentData) {
        $this->contentData = $contentData;

        return $this;
    }

    /**
     * Persist data stored in $content of content type to database
     *
     * @return AbstractContentType $this
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save() {
        if ($this->id) {
            $this->contentTypeEntity = $this->repository('contentType')->findOneBy(["id" => $this->id]);

            if ($this->contentTypeEntity) {
                if (!$this->contentId || !$this->contentEntity) {
                    $this->contentEntity = new Content();
                    $create              = true;
                } else {
                    $this->contentEntity->setId($this->contentId);
                    $create = false;
                }

                $this->contentEntity->setType($this->contentTypeEntity);
                $this->contentEntity->setParent($this->contentParentId);
                $this->contentEntity->setName($this->contentName);
                $this->contentEntity->setCssClass($this->contentCssClass);
                $this->contentEntity->setData($this->contentData);

                if ($create) {
                    $this->forgeEntityManager->create($this->contentEntity);
                } else {
                    $this->forgeEntityManager->update($this->contentEntity);
                }

                if (!$this->contentId) {
                    $this->contentId = $this->contentEntity->getId();
                }
            }
        }

        return $this;
    }

    /**
     * Load content entity from database to $content
     *
     * @param int $id content id
     *
     * @return AbstractContentType $this
     * @throws ORMException
     */
    public function load(int $id) {
        if (!$id) {
            return $this;
        }

        $this->contentEntity = $this->repository('content')->findOneBy(["id" => $id]);

        if ($this->contentEntity && $this->contentEntity->getId() > 0 && $this->contentEntity->getType()
            && $this->contentEntity->getType()->getId() === $this->id) {
            $this->contentId       = $this->contentEntity->getId();
            $this->contentParentId = $this->contentEntity->getParent();
            $this->contentName     = $this->contentEntity->getName();
            $this->contentCssClass = $this->contentEntity->getCssClass();
            $this->contentData     = $this->contentEntity->getData();
        } else {
            $this->contentEntity = null;

            $this->contentId       = null;
            $this->contentParentId = null;
            $this->contentName     = null;
            $this->contentCssClass = null;
            $this->contentData     = null;
        }

        return $this;
    }

    public function setOrder($order) {
    }
}
