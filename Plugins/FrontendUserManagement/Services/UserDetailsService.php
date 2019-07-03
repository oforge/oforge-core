<?php

namespace FrontendUserManagement\Services;

use Doctrine\ORM\ORMException;
use Exception;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Models\UserDetail;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Media\Services\MediaService;

/**
 * Class UserDetailsService
 *
 * @package FrontendUserManagement\Services
 */
class UserDetailsService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => UserDetail::class, "user" => User::class]);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function save(array $data) {
        try {
            $detail = $this->get($data['userId']);

            if ($detail == null) {
                $detail = UserDetail::create($data);
                $user   = $this->repository("user")->find($data['userId']);
                $detail->setUser($user);
                $this->entityManager()->create($detail);
            } else {
                $detail->fromArray($data);
                $this->entityManager()->update($detail);
            }

            return true;

        } catch (Exception $ex) {
            Oforge()->Logger()->get()->addError('Could not persist / flush userDetails', ['msg' => $ex->getMessage()]);
        }

        return false;
    }

    /**
     * @param $userID
     *
     * @return UserDetail|null
     * @throws ORMException
     */
    public function get($userID) : ?UserDetail {
        /** @var UserDetail|null $detail */
        $detail = $this->repository()->findOneBy(['user' => $userID]);

        return $detail;
    }

    public function updateImage(User $user, $file) {
        /** @var MediaService $mediaService */
        $mediaService = Oforge()->Services()->get('media');
        $media        = $mediaService->add($file);

        if ($media != null && $user != null) {
            if ($user->getDetail() != null) {
                $oldId = null;

                if ($user->getDetail()->getImage() != null) {
                    $oldId = $user->getDetail()->getImage()->getId();
                }

                $user->getDetail()->setImage($media);
                $this->entityManager()->update($user->getDetail());

                if ($oldId != null) {
                    $mediaService->delete($oldId);
                }
            } else {
                $detail = new UserDetail();
                $detail->setUser($user);
                $detail->setImage($media);

                $this->entityManager()->create($detail);
                $user->setDetail($detail);
            }
        }

        return $user;
    }
}
