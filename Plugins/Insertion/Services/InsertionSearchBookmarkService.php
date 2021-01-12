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
    const NONE = 'none';
    const DAILY = 'daily';
    const DAYS_2 = 'days_2';
    const DAYS_3 = 'days_3';
    const WEEKLY = 'weekly';

    public function __construct() {
        parent::__construct([
            'insertion' => Insertion::class,
            'search'    => InsertionUserSearchBookmark::class,
        ]);
    }

    /**
     * @param InsertionType $insertionType
     * @param User $user
     * @param array $params
     * @param array $options
     *
     * @return bool
     * @throws ORMException
     */
    public function add(InsertionType $insertionType, User $user, array $params, array $options) : bool {
        $bookmark = InsertionUserSearchBookmark::create([
            'insertionType' => $insertionType,
            'user' => $user,
            'params' => $params,
            'searchName' => $options['name'],
            'checkInterval' => $options['interval'],
        ]);
        $this->entityManager()->create($bookmark);
        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(int $id) : bool {
        $bookmark = $this->repository("search")->find($id);
        if (isset($bookmark)) {
            $this->entityManager()->remove($bookmark);

            return true;
        }

        return false;
    }

    /**
     * @param User|null $user
     *
     * @return array
     * @throws ORMException
     */
    public function list(User $user = null) : array {
        if (is_null($user)) {
            return $bookmarks = $this->repository("search")->findAll();
        } else {
            return $bookmarks = $this->repository("search")->findBy(["user" => $user]);
        }
    }

    /**
     * @param $bookmark
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function setLastChecked($bookmark) {
        if (is_a($bookmark, InsertionUserSearchBookmark::class)) {
            $bookmark->setChecked();
            $this->entityManager()->update($bookmark);
        } elseif (is_int($bookmark)) {
            $bookmark = $this->repository('search')->find($bookmark);
            $bookmark->setChecked();
            $this->entityManager()->update($bookmark);
        }
        $this->entityManager()->flush();
    }

    /**
     * @param InsertionType $insertionType
     * @param User $user
     * @param array $params
     * @param array|null $options
     *
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function toggle(InsertionType $insertionType, User $user, array $params, array $options = null) {
        $bookmarks = $this->repository("search")->findBy(["insertionType" => $insertionType, "user" => $user]);

        $bookmark = null;
        /** @var InsertionUserSearchBookmark $found */
        foreach ($bookmarks as $found) {
            if (!empty(array_diff_key($found->getParams(), $params)) || !empty(array_diff_key($params, $found->getParams()))) {
                continue;
            }

            if (empty($this->array_diff_assoc_recursive($found->getParams(), $params))
                && empty($this->array_diff_assoc_recursive($params, $found->getParams()))) {
                $bookmark = $found;
            }
        }

        if ($bookmark != null) {
            return $this->remove($bookmark->getId());
        }

        return $this->add($insertionType, $user, $params, $options);
    }

    /**
     * @param int $insertionType
     * @param int $user
     * @param array $params
     *
     * @return bool
     * @throws ORMException
     */
    public function hasBookmark(int $insertionType, int $user, array $params) : bool {
        $bookmarks = $this->repository("search")->findBy(["insertionType" => $insertionType, "user" => $user]);

        /** @var InsertionUserSearchBookmark $found */
        foreach ($bookmarks as $found) {
            if (!empty(array_diff_key($found->getParams(), $params)) || !empty(array_diff_key($params, $found->getParams()))) {
                continue;
            }

            if (empty($this->array_diff_assoc_recursive($found->getParams(), $params))
                && empty($this->array_diff_assoc_recursive($params, $found->getParams()))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $array1
     * @param $array2
     *
     * @return array
     */
    private function array_diff_assoc_recursive($array1, $array2) {
        $difference = [];
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $newDiff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                    if (!empty($newDiff)) {
                        $difference[$key] = $newDiff;
                    }
                }
            } elseif (!isset($array2[$key]) || $array2[$key] !== $value) {
                $difference[$key] = $value;
            }
        }

        return $difference;
    }

    /**
     * @param $typeId
     * @param array|null $params
     *
     * @return string
     */
    public function getUrl($typeId, ?array $params) {
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $url    = $router->getBasePath();
        $url    .= $router->pathFor('insertions_listing', ["type" => $typeId], isset($params) ? $params : [] );

        return $url;
    }

    /**
     * @param $searchBookmarkId
     *
     * @return object|null
     * @throws ORMException
     */
    public function get($searchBookmarkId) {
        return $this->repository('search')->find($searchBookmarkId);
    }

    /**
     * @param $searchBookmarkId
     * @param $options
     *
     * @throws ORMException
     */
    public function update($searchBookmarkId, $options) {
        /** @var InsertionUserSearchBookmark $bookmark */
        $bookmark = $this->get($searchBookmarkId);
        $bookmark->setSearchName($options['name']);
        $bookmark->setCheckInterval($options['interval']);
        $this->entityManager()->update($bookmark);
    }

    public function deleteBookmarksForUser(int $userID) {
        try {
            $bookmarks = $this->repository("search")->findBy(["user" => $userID]);
            foreach ($bookmarks as $bookmark) {
                $this->entityManager()->remove($bookmark, false);
            }
        } catch (\Exception $exception) {
            Oforge()->Logger()->logException($exception);

            return false;
        }
    }
}
