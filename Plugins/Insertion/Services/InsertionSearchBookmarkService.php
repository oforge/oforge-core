<?php

namespace Insertion\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserService;
use Insertion\Models\AttributeKey;
use Insertion\Models\AttributeValue;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionAttributeValue;
use Insertion\Models\InsertionContent;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionUserBookmark;
use Insertion\Models\InsertionUserSearchBookmark;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Models\Language;
use Slim\Router;

class InsertionSearchBookmarkService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'insertion' => Insertion::class,
            'search'    => InsertionUserSearchBookmark::class,
        ]);
    }

    public function add(InsertionType $insertionType, User $user, array $params) : bool {
        $bookmark = InsertionUserSearchBookmark::create(["insertionType" => $insertionType, "user" => $user, "params" => $params]);
        $this->entityManager()->create($bookmark);

        return true;
    }

    public function remove(int $id) : bool {
        $bookmark = $this->repository("search")->find($id);
        if (isset($bookmark)) {
            $this->entityManager()->remove($bookmark);

            return true;
        }

        return false;
    }

    public function list(User $user) : array {
        $bookmarks = $this->repository("search")->findBy(["user" => $user]);

        $result = [];
        if (isset($bookmarks) && sizeof($bookmarks) > 0) {
            foreach ($bookmarks as $bookmark) {
                $data = $bookmark->toArray(0);

                $data["url"] = $this->getUrl($data["insertionType"], $data["params"]);
                $result[]    = $data;
            }
        }

        return $result;
    }

    public function toggle(InsertionType $insertionType, User $user, array $params) {
        $bookmarks = $this->repository("search")->findBy(["insertionType" => $insertionType, "user" => $user]);

        $bookmark = null;
        /**
         * @var $found InsertionUserSearchBookmark
         */
        foreach ($bookmarks as $found) {
            if (sizeof($found->getParams()) == sizeof($params) && !array_diff($found->getParams(), $params) && !array_diff($params, $found->getParams())) {
                $bookmark = $found;
            }
        }

        if ($bookmark != null) {
            return $this->remove($bookmark->getId());
        }

        return $this->add($insertionType, $user, $params);
    }

    public function hasBookmark(int $insertionType, int $user, array $params) : bool {
        $bookmarks = $this->repository("search")->findBy(["insertionType" => $insertionType, "user" => $user]);

        /**
         * @var $found InsertionUserSearchBookmark
         */
        foreach ($bookmarks as $found) {
            if (sizeof($found->getParams()) == sizeof($params) && !array_diff($found->getParams(), $params) && !array_diff($params, $found->getParams())) {
                return true;
            }
        }

        return false;
    }

    public function getUrl($id, ?array $params) {
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $url    = $router->pathFor('insertions_listing', ["type" => $id]);

        $first = true;


        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $url   .= ($first ? "?" : "&") . urlencode($key . '[]') . "=" . urlencode($v);
                    $first = false;
                }
            } else {
                $url   .= ($first ? "?" : "&") . urlencode($key) . "=" . urlencode($value);
                $first = false;
            }
        }

        return $url;
    }

}
