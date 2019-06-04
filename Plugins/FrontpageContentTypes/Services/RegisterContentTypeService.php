<?php


namespace FrontpageContentTypes\Services;


use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class RegisterContentTypeService extends AbstractDatabaseAccess
{
    public function __construct()
    {
        parent::__construct(['default' => ContentType::class, 'group' => ContentTypeGroup::class]);
    }

    /**
     * @param string $contentTypeGroup
     * @param string $contentTypeName
     * @param string $contentTypePath
     * @param string $contentTypeIcon
     * @param string $description
     * @param string $classPath
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function registerContentType(string $contentTypeGroup, string $contentTypeName, string $contentTypePath, string $contentTypeIcon, string $description, string $classPath)
    {
        /** @var ContentType $contentType */
        $contentType = $this->repository()->findOneBy(['name' => $contentTypeName]);
        $create      = false;
        if ($contentType === null) {
            $contentType = new ContentType();
            $create      = true;
        }

        /** @var ContentTypeGroup $group */

        $group = $this->repository('group')->findOneBy(['name' => $contentTypeGroup]);

        if ($group === null) {
            $group = $this->registerContentGroup($contentTypeGroup, $contentTypeGroup);
        }

        $contentType->setGroup($group)
            ->setName($contentTypeName)
            ->setPath($contentTypePath)
            ->setIcon($contentTypeIcon)
            ->setDescription($description)
            ->setClassPath($classPath);

        if ($create) {
            $this->entityManager()->create($contentType);
        } else {
            $this->entityManager()->update($contentType);
        }
    }

    /**
     * @param string $groupName
     * @param string $description
     * @throws \Doctrine\ORM\ORMException
     */
    public function registerContentGroup(string $groupName, string $description = '')
    {
        $group = $this->repository('group')->findOneBy(['name' => $groupName]);
        if ($group === null) {
            $group = new ContentTypeGroup();
            $group->setName($groupName)->setDescription($description);
            $this->entityManager()->create($group);
        }

        return $group;
    }

}
