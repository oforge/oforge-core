<?php

namespace Pedigree\Services;

use Oforge\Engine\Modules\I18n\Helper\I18N;
use Pedigree\Models\Ancestor;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;

class PedigreeService extends AbstractDatabaseAccess
{
    public function __construct()
    {
        parent::__construct([
            'ancestor' => Ancestor::class,
        ]);
    }

    /**
     * @return mixed
     * @throws ORMException
     * @throws NonUniqueResultException
     */
    public function getNameCount() {
        $repository = $this->repository('ancestor');
        $nameCount = $repository->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
        return $nameCount;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws ORMException
     */
    public function getAllAncestors($limit = null, $offset = null){
        return $this->repository('ancestor')->findBy([], ['name' => 'asc'], $limit, $offset);
    }

    /**
     * @param int $id
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteAncestor($id) {
        $ancestor = $this->repository('ancestor')->find($id);
        $this->entityManager()->remove($ancestor);
        $this->repository('ancestor')->clear();
    }

    public function addAncestor($name) {
        $ancestor = $this->repository('ancestor')->findBy(['name' => $name]);
        if($ancestor == null) {
            $ancestor = new Ancestor();
            $ancestor->setName($name);
            $this->entityManager()->create($ancestor);
        } else {
            Oforge()->View()->Flash()->addMessage('warning',
                I18N::translate('backend_insertion_add_name_already_exists','Name Already Exists', 'en'));
        }
    }
}
