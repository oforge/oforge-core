<?php

namespace Insertion\Services;

use FrontendUserManagement\Models\User;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionUserBookmark;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use phpDocumentor\Reflection\Types\Integer;

class InsertionBookmarkService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'insertion' => Insertion::class,
            'user'      => InsertionUserBookmark::class,
        ]);
    }

    /**
     * @param Insertion $insertion
     * @param User $user
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function add(Insertion $insertion, User $user) : bool {
        if (isset($insertion)) {
            $bookmark = InsertionUserBookmark::create(["user" => $user, "insertion" => $insertion]);
            $this->entityManager()->create($bookmark);

            return true;
        }

        return false;
    }

    /**
     * @param Integer $id
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     */
    public function remove(int $id) : bool {
        $bookmark = $this->repository("user")->find($id);
        if (isset($bookmark)) {
            $this->entityManager()->remove($bookmark);

            return true;
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function list(User $user) : array {
        $bookmarks = $this->repository("user")->findBy(["user" => $user], ["createdAt" => "DESC"]);

        $result = [];
        if (isset($bookmarks) && sizeof($bookmarks) > 0) {
            foreach ($bookmarks as $bookmark) {
                $result[] = $bookmark->toCleanedArray(3);
            }
        }

        return $result;
    }

    /**
     * @param Insertion $insertion
     * @param User $user
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function toggle(Insertion $insertion, User $user) : bool {

        $bookmark = $this->repository("user")->findOneBy(["insertion" => $insertion, "user" => $user]);
        if (isset($bookmark)) {
            return $this->remove($bookmark->getId());
        }

        return $this->add($insertion, $user);
    }

    /**
     * @param int $insertion
     * @param int $user
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function hasBookmark(int $insertion, int $user) : bool {
        $bookmark = $this->repository("user")->findOneBy(["insertion" => $insertion, "user" => $user]);

        return $bookmark != null;
    }

    /**
     * @param $userId
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function userHasBookmark($userId) {
        $bookmark = $this->repository("user")->findOneBy(["user" => $userId]);

        return $bookmark != null;
    }
}
