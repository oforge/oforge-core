<?php

namespace Insertion\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\UserService;
use Insertion\Models\Insertion;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

class InsertionService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct(['default' => Insertion::class]);
    }

    /**
     * @param $insertionType
     * @param $title
     * @param $description
     *
     * @return Insertion|null
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function createNewInsertion($insertionType, $title, $description) {
        $insertion = new Insertion();

        /** @var UserService $userService */
        $userService = Oforge()->Services()->get('frontend.user.management.user');
        if (!isset($userService)) {
            return null;
        }
        $user = $userService->getUserById(1);
        $insertion->setInsertionType($insertionType);
        $insertion->setTitle($title);
        $insertion->setUser($user);
        $insertion->setDescription($description);

        $this->entityManager()->persist($insertion);
        $this->entityManager()->flush($insertion);

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
    public function list(string $type, array $criteria = [], string $orderBy = null, $offset = null, $limit = null) : array {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $result       = $queryBuilder->select(['i'])->from(Insertion::class, 'i')->where($queryBuilder->expr()->eq('i.type', $type));
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
     * @throws ORMException
     */
    public function getInsertionById($id) {
        $this->repository()->find($id);
    }

    public function updateInsertion($id) {
    }

    /**
     * @param $id
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteInsertion($id) {
        $insertion = $this->repository()->find($id);
        $this->entityManager()->remove($insertion);
        $this->entityManager()->flush();
    }

    public function addAttributeValueToInsertion($insertionId, $attributeId) {

    }

}
