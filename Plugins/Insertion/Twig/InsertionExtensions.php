<?php

namespace Insertion\Twig;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\FrontendUserService;
use FrontendUserManagement\Services\UserService;
use Insertion\Models\Insertion;
use Insertion\Services\AttributeService;
use Insertion\Services\InsertionBookmarkService;
use Insertion\Services\InsertionSearchBookmarkService;
use Insertion\Services\InsertionService;
use Insertion\Services\InsertionSliderService;
use Insertion\Services\InsertionTypeService;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Twig_Extension;
use Twig_ExtensionInterface;
use Twig_Filter;
use Twig_Function;

/**
 * Class AccessExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class InsertionExtensions extends Twig_Extension implements Twig_ExtensionInterface {

    /**
     * @inheritDoc
     */
    public function getFunctions() {
        return [
            new Twig_Function('getInsertionValues', [$this, 'getInsertionValues']),
            new Twig_Function('getInsertionAttribute', [$this, 'getAttribute']),
            new Twig_Function('getInsertionValue', [$this, 'getValue']),
            new Twig_Function('hasBookmark', [$this, 'hasBookmark']),
            new Twig_Function('hasSearchBookmark', [$this, 'hasSearchBookmark']),
            new Twig_Function('getInsertionSliderContent', [$this, 'getInsertionSliderContent']),
            new Twig_Function('getSimilarInsertion', [$this, 'getSimilarInsertion']),
            new Twig_Function('getLatestBlogPostTile', [$this, 'getLatestBlogPostTile']),
            new Twig_Function('getQuickSearch', [$this, 'getQuickSearch']),
            new Twig_Function('getChatPartnerInformation', [$this, 'getChatPartnerInformation']),
        ];
    }

    /** @inheritDoc */
    public function getFilters() {
        return [
            new Twig_Filter('age', [$this, 'getAge'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * Format DateTimeObjects.
     *
     * @param DateTimeInterface|null $dateTimeObject
     *
     * @return string
     */
    public function getAge(?string $dateTimeObject, ?string $type) : ?string {
        $bday  = DateTime::createFromFormat('Y-m-d', $dateTimeObject); // Your date of birth
        $today = new Datetime();

        $diff   = $today->diff($bday);
        $suffix = $type == 'datemonth' ? I18N::translate('month_suffix') : ($type == 'dateyear' ? I18N::translate('year_suffix') : '');

        return ($type == 'datemonth' ? ($diff->y * 12 + $diff->m) : ($type == 'dateyear' ? $diff->y : $dateTimeObject)) . ' ' . $suffix;
    }

    /**
     * @param mixed ...$vars
     *
     * @return mixed|string
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function getInsertionValues(...$vars) {
        $result = '';
        if (count($vars) == 1) {
            /** @var AttributeService $attributeService */
            $attributeService = Oforge()->Services()->get('insertion.attribute');
            $tmp              = $attributeService->getAttribute($vars[0]);
            if (isset($tmp)) {
                $result = $tmp->toArray(3);
            }
        }

        return $result;
    }

    /**
     * @param mixed ...$vars
     *
     * @return boolean
     * @throws ServiceNotFoundException
     */
    public function hasBookmark(...$vars) {
        if (count($vars) == 1) {
            /** @var $authService AuthService */
            $authService = Oforge()->Services()->get("auth");
            if (isset($_SESSION["auth"])) {
                $user = $authService->decode($_SESSION["auth"]);
                if (isset($user) && isset($user['id'])) {
                    /** @var InsertionBookmarkService $bookmarkService */
                    $bookmarkService = Oforge()->Services()->get("insertion.bookmark");

                    return $bookmarkService->hasBookmark($vars[0], $user['id']);
                }
            }
        }

        return false;
    }

    /**
     * @param mixed ...$vars
     *
     * @return boolean
     * @throws ServiceNotFoundException
     */
    public function hasSearchBookmark(...$vars) {
        if (count($vars) == 2) {
            /** @var $authService AuthService */
            $authService = Oforge()->Services()->get("auth");
            if (isset($_SESSION["auth"])) {
                $user = $authService->decode($_SESSION["auth"]);
                if (isset($user) && isset($user['id'])) {
                    /** @var InsertionSearchBookmarkService $bookmarkService */
                    $bookmarkService = Oforge()->Services()->get("insertion.search.bookmark");

                    return $bookmarkService->hasBookmark($vars[0], $user['id'], $vars[1]);
                }
            }
        }

        return false;
    }

    /**
     * @return array
     * @throws ServiceNotFoundException
     */
    public function getInsertionSliderContent() {
        /** @var InsertionSliderService $insertionSliderService */
        $insertionSliderService = Oforge()->Services()->get("insertion.slider");
        $insertions             = $insertionSliderService->getRandomInsertions();

        return ['insertions' => $insertions];
    }

    /**
     * @param array $vars
     *
     * @return array
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function getSimilarInsertion(...$vars) {
        /** @var InsertionSliderService $insertionSliderService */
        $insertionSliderService = Oforge()->Services()->get("insertion.slider");
        $insertion              = $insertionSliderService->getRandomInsertion(1, $vars[0], $vars[1]);

        return $insertion;
    }

    /**
     * @return array
     * @throws ServiceNotFoundException
     */
    public function getLatestBlogPostTile() {
        $blogService = Oforge()->Services()->get("blog.post");
        $blogPost    = $blogService->getLatestPost();

        return isset($blogPost) ? $blogPost->toArray(3) : [];
    }

    /**
     * @return array
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function getQuickSearch() {
        /** @var InsertionTypeService $insertionTypeService */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');
        $quickSearch          = $insertionTypeService->getQuickSearchInsertions();

        return ['types' => $quickSearch, 'attributes' => $insertionTypeService->getInsertionTypeAttributeMap()];
    }

    /**
     * @param array $vars
     *
     * @return array|bool
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function getChatPartnerInformation(...$vars) {
        if (count($vars) === 2) {
            if ($vars[0] === 'requested') {
                /** @var InsertionService $insertionService */
                $insertionService = Oforge()->Services()->get('insertion');
                /** @var Insertion $insertion */
                $insertion = $insertionService->getInsertionById($vars[1]);
                $insertionMedia = $insertion->getMedia()[0];
                $imageId = null;
                if (isset($insertionMedia)) {
                    $imageId = $insertionMedia->getContent()->getId();
                }
                return [
                    'imageId' => $imageId,
                    'title'   => $insertion->getContent()[0]->getTitle(),
                ];
            } else {
                /** @var UserService $userService */
                $userService = Oforge()->Services()->get('frontend.user.management.user');
                /** @var User $user */
                $user      = $userService->getUserById($vars[1]);
                $imageId   = null;
                $title     = null;
                try {
                    $imageId = $user->getDetail()->getImage()->getId();
                    $title   = $user->getDetail()->getNickName();
                } catch (\Throwable $e) {

                }

                return [
                    'imageId' => $imageId,
                    'title'   => $title,
                ];
            }
        }

        return false;
    }

    /**
     * @return array
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function getAttribute(...$vars) {
        if (count($vars) == 1) {
            /** @var InsertionTypeService $insertionTypeService */
            $insertionTypeService = Oforge()->Services()->get('insertion.type');

            return $insertionTypeService->getAttribute($vars[0]);
        }

        return null;
    }

    /**
     * @return array
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function getValue(...$vars) {
        if (count($vars) == 1) {
            /** @var InsertionTypeService $insertionTypeService */
            $insertionTypeService = Oforge()->Services()->get('insertion.type');

            return $insertionTypeService->getValue($vars[0]);
        }

        return null;
    }
}
