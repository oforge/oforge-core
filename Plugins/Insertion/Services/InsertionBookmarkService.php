<?php

namespace Insertion\Services;

use FrontendUserManagement\Models\User;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionUserBookmark;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class InsertionBookmarkService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'insertion' => Insertion::class,
            'user'      => InsertionUserBookmark::class,
        ]);
    }

    public function add(Insertion $insertion, User $user) : bool {
        if (isset($insertion)) {
            $bookmark = InsertionUserBookmark::create(["user" => $user, "insertion" => $insertion]);
            $this->entityManager()->create($bookmark);

            return true;
        }

        return false;
    }

    public function remove(int $id) : bool {
        $bookmark = $this->repository("user")->find($id);
        if (isset($bookmark)) {
            $this->entityManager()->remove($bookmark);

            return true;
        }

        return false;
    }

    public function list(User $user) : array {
        $bookmarks = $this->repository("user")->findBy(["user" => $user]);

        $result = [];
        if (isset($bookmarks) && sizeof($bookmarks) > 0) {
            foreach ($bookmarks as $bookmark) {
                $result[] = $bookmark->toCleanedArray(3);
            }
        }

        return $result;
    }

    public function toggle(Insertion $insertion, User $user) : bool {
        $bookmark = $this->repository("user")->findOneBy(["insertion" => $insertion, "user" => $user]);
        if (isset($bookmark)) {
            return $this->remove($bookmark->getId());
        }

        return $this->add($insertion, $user);
    }

    public function hasBookmark(int $insertion, int $user) : bool {
        $bookmark = $this->repository("user")->findOneBy(["insertion" => $insertion, "user" => $user]);

        return $bookmark != null;
    }
}
