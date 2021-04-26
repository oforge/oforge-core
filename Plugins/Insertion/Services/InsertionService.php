<?php

namespace Insertion\Services;

use DateTime;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use FrontendUserManagement\Services\UserService;
use Insertion\Models\AttributeKey;
use Insertion\Models\AttributeValue;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionAttributeValue;
use Insertion\Models\InsertionContent;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Models\Language;

class InsertionService extends AbstractDatabaseAccess
{
    public function __construct()
    {
        parent::__construct([
            'default' => Insertion::class,
            'insertionAttributeValue' => InsertionAttributeValue::class,
            'language' => Language::class,
            'content' => InsertionContent::class
        ]);
    }

    /**
     * @param $insertionType
     * @param $name
     * @param $title
     * @param $description
     * @param string $iso
     *
     * @return Insertion|null
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public function createNewInsertion($insertionType, $name, $title, $description, $iso = 'de')
    {
        $insertion = new Insertion();

        /** @var UserService $userService */
        $userService = Oforge()->Services()->get('frontend.user.management.user');
        if (!isset($userService)) {
            return null;
        }
        $user = $userService->getUserById(1);
        $insertion->setInsertionType($insertionType);
        $insertion->setUser($user);

        $content = new InsertionContent();
        $insertion->setContent([$content]);

        //   $content->setName($name);
        $content->setDescription($description);
        $content->setTitle($title);
        $content->setInsertion($insertion);

        /** @var Language $language */
        $language = $this->repository("language")->findOneBy(["iso" => $iso]);
        $content->setLanguage($language);

        $this->entityManager()->create($content, false);
        $this->entityManager()->create($insertion);

        return $insertion;
    }

    /**
     * @param string $type
     * @param array $criteria
     * @param string|null $orderBy
     * @param null $offset
     * @param null $limit
     *
     * @return array
     * @throws ORMException
     */
    public function list(string $type, array $criteria = [], string $orderBy = null, $offset = null, $limit = null): array
    {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $result = $queryBuilder->select(['i'])->from(Insertion::class, 'i')->where($queryBuilder->expr()->eq('i.type', $type));
        foreach ($criteria as $criteriaKey => $criteriaValue) {
            switch ($criteriaValue['type']) {
                case 'multi':
                    $orX = $queryBuilder->expr()->orX();
                    foreach ($criteria['values'] as $value) {
                        $orX->add($queryBuilder->expr()->eq('i.' . $criteriaKey, $value));
                    }
                    $result = $result->andWhere($orX);
                    break;
                case 'range':
                    $result = $result->andWhere($queryBuilder->expr()->between('i.' . $criteriaKey, $criteriaValue['min'], $criteriaValue['max']));
                    break;
                case 'single':
                    $result = $result->andWhere($queryBuilder->expr()->eq('i.' . $criteriaKey, $criteriaValue));
                    break;
            }
        }

        return $result->orderBy($orderBy)->setFirstResult($offset)->setMaxResults($limit)->getQuery()->execute();
    }

    /**
     * @param $id
     *
     * @return object|null
     * @throws ORMException
     */
    public function getInsertionById($id)
    {
        return $this->repository()->find($id);
    }

    public function updateInsertion($id)
    {
    }

    /**
     * @param $id
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteInsertion($id)
    {
        $insertion = $this->repository()->find($id);
        $this->entityManager()->remove($insertion);
        $this->repository()->clear();
    }

    /**
     * @param Insertion $insertion
     * @param AttributeKey $attributeKey
     * @param AttributeValue $value
     *
     * @return InsertionAttributeValue
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addAttributeValueToInsertion($insertion, $attributeKey, $value)
    {
        $insertionAttributeValue = new InsertionAttributeValue();
        $insertionAttributeValue->setAttributeKey($attributeKey)->setInsertion($insertion)->setValue($value->getValue());

        $this->entityManager()->create($insertionAttributeValue);

        return $insertionAttributeValue;
    }

    /**
     * @param DateTime $from
     * @param DateTime $to
     *
     * @return Insertion[]
     */
    public function getInsertionByDays(DateTime $from, DateTime $to)
    {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $queryBuilder
            ->select(['i'])->from(Insertion::class, 'i')
            ->where('i.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to);
        /** @var Insertion[] $result */
        $result = $queryBuilder->getQuery()->getResult();
        return $result;
    }

    /**
     * @param $id
     * @throws ORMException
     */
    public function countUpInsertionViews($id)
    {
        /** @var Insertion $insertion */
        $insertion = $this->repository()->find($id);
        $insertion = $insertion->countUpViews();
        $this->entityManager()->flush($insertion);
    }

    /**
     * @param $id
     * @throws ORMException
     */
    public function countUpInsertionsContactAttempt($id)
    {
        /** @var Insertion $insertion */
        $insertion = $this->repository()->find($id);
        $insertion = $insertion->countContactAttempt();
        $this->entityManager()->flush($insertion);
    }

    /**
     * @param $userId
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function getLatestInsertionOfUser($userId)
    {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $queryBuilder
            ->select(['i'])->from(Insertion::class, 'i')
            ->where("i.user = :user")
            ->setParameter('user', $userId)
            ->orderBy('i.createdAt', 'DESC')
            ->setMaxResults(1);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string|int $id
     * @param string $language
     * @return object|null
     * @throws ORMException
     */
    public function getInsertionContentByLanguage($id, $language)
    {
        /** @var Language $language */
        $language = $this->repository('language')->findOneBy(['iso' => $language]);
        if (!isset($language)) {
            return null;
        }
        /** @var InsertionContent $insertion */
        return $insertionContent = $this->repository('content')->findOneBy(['insertion' => $id, 'language' => $language->getId()]);
    }


    /**
     * @param array $args
     * @return Insertion
     * @throws ORMException
     */
    public function addTranslatedInsertionContent($args = [])
    {
        /** @var Insertion $insertion */
        $insertion = $this->repository()->find($args['id']);

        /** @var InsertionContent[] $existingContent */
        $content = new InsertionContent();
        $insertion->setContent([$content]);

        $content->setDescription($args['description']);
        $content->setTitle($args['title']);
        $content->setInsertion($insertion);

        /** @var Language $language */
        $language = $this->repository("language")->findOneBy(["iso" => $args['language']]);
        $content->setLanguage($language);

        $this->entityManager()->create($content, false);
        $this->entityManager()->update($insertion);

        return $insertion;
    }

    /**
     * @param $userId
     * @return int
     * @throws Exception|\Doctrine\DBAL\Exception
     */
    public function countUserInsertionsOfCurrentYear($userId): int
    {
        $query = "SELECT COUNT(i.id) as count FROM 
                        oforge_insertion AS i 
                        WHERE insertion_user = '$userId' 
                        AND YEAR(NOW()) = YEAR(i.real_created_at);";
        $sqlResult = $this->entityManager()->getEntityManager()->getConnection()->fetchAssociative($query);

        return (int)$sqlResult['count'];
    }
}
