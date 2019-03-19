<?php

namespace Oforge\Engine\Modules\CMS\Abstracts;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;

abstract class AbstractContentType extends AbstractDatabaseAccess
{
    protected $entityManager    = NULL;
    
    private $contentTypeEntity  = NULL;
    
    private $contentEntity      = NULL;
    
    private $id                 = NULL;
    private $groupId            = NULL;
    private $name               = NULL;
    private $path               = NULL;
    private $icon               = NULL;
    private $description        = NULL;
    private $classPath          = NULL;
    
    private $contentId          = NULL;
    private $contentParentId    = NULL;
    private $contentName        = NULL;
    private $contentCssClass    = NULL;
    private $contentData        = NULL;
    
    public function __construct()
    {
        parent::__construct(['contentType' => ContentType::class, 'content' => Content::class]);
        
        $this->entityManager = Oforge()->DB()->getManager();
        
        $this->contentTypeEntity = $this->repository('contentType')->findOneBy(["classPath" => get_class($this)]);
        
        $this->id          = $this->contentTypeEntity->getId();
        $this->groupId     = $this->contentTypeEntity->getGroup()->getId();
        $this->name        = $this->contentTypeEntity->getName();
        $this->path        = $this->contentTypeEntity->getPath();
        $this->icon        = $this->contentTypeEntity->getIcon();
        $this->description = $this->contentTypeEntity->getDescription();
        $this->class_path  = $this->contentTypeEntity->getClassPath();
    }
    
    /**
     * Return whether or not content type is a container type like a row
     *
     * @return bool true|false
     */
    abstract public function isContainer(): bool;
    
    /**
     * Return edit data for page builder of content type
     *
     * @return mixed
     */
    abstract public function getEditData();
    
    /**
     * Set edit data for page builder of content type
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
     * Return child data of content type
     *
     * @return array|false should return false if no child content data is available
     * 
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
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Return id of content type group
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }
    
    /**
     * Return name of content type
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Return path of content type
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Return icon of content type
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }
    
    /**
     * Return description of content type
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Return class path of content type
     *
     * @return string
     */
    public function getClassPath()
    {
        return $this->classPath;
    }
    
    /**
     * Return id of content
     *
     * @return int
     */
    public function getContentId()
    {
        return $this->contentId;
    }
    
    /**
     * Return parent id of content
     *
     * @return int
     */
    public function getContentParentId()
    {
        return $this->contentParentId;
    }
    
    /**
     * Set parent id of content
     * @param int $contentParentId
     *
     * @return ContentType $this
     */
    public function setContentParentId(int $contentParentId)
    {
        $this->contentParentId = $contentParentId;
        
        return $this;
    }
    
    /**
     * Return name of content
     *
     * @return string
     */
    public function getContentName()
    {
        return $this->contentName;
    }
    
    /**
     * Set name of content
     * @param string $contentName
     *
     * @return ContentType $this
     */
    public function setContentName(string $contentName)
    {
        $this->contentName = $contentName;
        
        return $this;
    }
    
    /**
     * Return css class of content
     *
     * @return string
     */
    public function getContentCssClass()
    {
        return $this->contentCssClass;
    }
    
    /**
     * Set css class of content
     * @param string $contentCssClass
     *
     * @return ContentType $this
     */
    public function setContentCssClass(string $contentCssClass)
    {
        $this->contentCssClass = $contentCssClass;
        
        return $this;
    }
    
    /**
     * Return data of content
     *
     * @return mixed
     */
    public function getContentData()
    {
        return $this->contentData;
    }
    
    /**
     * Set data of content
     * @param string $contentCssClass
     *
     * @return ContentType $this
     */
    public function setContentData(string $contentData)
    {
        $this->contentData = $contentData;
        
        return $this;
    }
    
    /**
     * Persist data stored in $content of content type to database
     *
     * @return ContentType $this
     */
    public function save() {
        if ($this->id)
        {
            $this->contentTypeEntity = $this->repository('contentType')->findOneBy(["id" => $this->id]);
            
            if ($this->contentTypeEntity)
            {
                if (!$this->contentId || !$this->contentEntity)
                {
                    $this->contentEntity = new Content;
                }
                else
                {
                    $this->contentEntity->setId($this->contentId);
                }
                
                $this->contentEntity->setType($this->contentTypeEntity);
                $this->contentEntity->setParent($this->contentParentId);
                $this->contentEntity->setName($this->contentName);
                $this->contentEntity->setCssClass($this->contentCssClass);
                $this->contentEntity->setData($this->contentData);
                
                $this->entityManager->persist($this->contentEntity);
                $this->entityManager->flush();
                
                if (!$this->contentId)
                {
                    $this->contentId = $this->contentEntity->getId();
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Load content entity from database to $content
     * @param int $id content id
     *
     * @return ContentType $this
     */
    public function load(int $id) {
        if (!$id)
        {
            return $this;
        }
        
        $this->contentEntity = $this->repository('content')->findOneBy(["id" => $id]);
        
        if ($this->contentEntity && $this->contentEntity->getId() > 0 && $this->contentEntity->getType() && $this->contentEntity->getType()->getId() === $this->id)
        {
            $this->contentId        = $this->contentEntity->getId();
            $this->contentParentId  = $this->contentEntity->getParent();
            $this->contentName      = $this->contentEntity->getName();
            $this->contentCssClass  = $this->contentEntity->getCssClass();
            $this->contentData      = $this->contentEntity->getData();
        }
        else
        {
            $this->contentEntity    = NULL;
            
            $this->contentId        = NULL;
            $this->contentParentId  = NULL;
            $this->contentName      = NULL;
            $this->contentCssClass  = NULL;
            $this->contentData      = NULL;
        }
        
        return $this;
    }
}