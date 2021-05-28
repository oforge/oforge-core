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
     * @param int $insertionID
     * @param int $userID
     *
     * @return bool
     */
    public function hasBookmark(int $insertionID, int $userID) : bool {
        try {
            $bookmark = $this->repository("user")->findOneBy(["insertion" => $insertionID, "user" => $userID]);

            return $bookmark != null;
        } catch (\Exception $exception) {
            Oforge()->Logger()->logException($exception);

            return false;
        }
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

    /**
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getUsersWithBookmarks() {
        $query = 'SELECT DISTINCT b.insertion_user AS id ' . 'FROM oforge_insertion_user_bookmarks AS b ' . 'JOIN oforge_insertion AS i '
            . 'WHERE b.insertion_id = i.id AND ' . 'i.active = 1 AND ' . 'i.moderation = 1 AND ' . 'i.deleted = 0;';

        $sqlResult = $this->entityManager()->getEntityManager()->getConnection()->executeQuery($query);

        return array_column($sqlResult->fetchAll(), 'id');
    }

    /**
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getInsertionsWithBookmarks($insertion_id) {
        $query = 'SELECT insertion_id, count(insertion_user) AS user ' . 'FROM oforge_insertion_user_bookmarks ' . 'WHERE insertion_id = '.$insertion_id;

        $sqlResult = $this->entityManager()->getEntityManager()->getConnection()->executeQuery($query);

        return array_column($sqlResult->fetchAll(), 'user');
    }


    public function deleteInserationBookmarks(int $insertionID) {
        try {
            $bookmarks = $this->repository("user")->findBy(["insertion" => $insertionID]);
            foreach ($bookmarks as $bookmark) {
                $this->entityManager()->remove($bookmark);
            }
        } catch (\Exception $exception) {
            Oforge()->Logger()->logException($exception);

            return false;
        }
    }

    public function deleteBookmarksForUser(int $userID) {
        try {
            $bookmarks = $this->repository("user")->findBy(["user" => $userID]);
            foreach ($bookmarks as $bookmark) {
                $this->entityManager()->remove($bookmark, false);
            }
        } catch (\Exception $exception) {
            Oforge()->Logger()->logException($exception);

            return false;
        }
    }
}
