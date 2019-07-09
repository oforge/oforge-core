<?php

namespace Insertion\Twig;

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
use Twig_Extension;
use Twig_ExtensionInterface;
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
            new Twig_Function('hasBookmark', [$this, 'hasBookmark']),
            new Twig_Function('hasSearchBookmark', [$this, 'hasSearchBookmark']),
            new Twig_Function('getInsertionSliderContent', [$this, 'getInsertionSliderContent']),
            new Twig_Function('getQuickSearch', [$this, 'getQuickSearch']),
            new Twig_Function('getChatPartnerInformation', [$this, 'getChatPartnerInformation']),
        ];
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
     * @return array
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function getQuickSearch() {
        /** @var InsertionTypeService $insertionTypeService */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');
        $quickSearch          = $insertionTypeService->getQuickSearchInsertions();

        return ['types' => $quickSearch, 'attributes' => $insertionTypeService->getInsertionTypeAttributeMap()];;
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

                return [
                    'imageId' => $insertion->getMedia()[0]->getId(),
                    'title'   => $insertion->getContent()[0]->getTitle(),
                ];
            } else {
                /** @var UserService $userService */
                $userService = Oforge()->Services()->get('frontend.user.management.user');
                /** @var User $user */
                $user = $userService->getUserById($vars[1]);
                $userImage = $user->getDetail()->getImage();
                if($userImage) {
                    $imageId = $user->getDetail()->getImage()->getId();
                } else {
                    $imageId = 'default';
                }

                return [
                    'imageId' => $imageId,
                    'title' => $user->getDetail()->getNickName(),
                ];
            }
        }

        return false;
    }
}
