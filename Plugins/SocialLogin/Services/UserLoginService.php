<?php

namespace SocialLogin\Services;

use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\FrontendUserLoginService;
use FrontendUserManagement\Services\RegistrationService;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Services\RedirectService;
use Oforge\Engine\Modules\Media\Services\MediaService;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Services\UrlService;
use SocialLogin\Models\LoginProvider;
use SocialLogin\Models\SocialLogin;

/**
 * Class LoginProviderService
 *
 * @package Seo\Services
 */
class UserLoginService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["user" => User::class, 'sociallogin' => SocialLogin::class]);
    }

    public function loginOrRegister(\Hybridauth\User\Profile $profile, $type) : ?string {
        if ($profile->emailVerified != null || $profile->email != null) {
            $email = $profile->emailVerified ? : $profile->email;
            /**
             * @var $user User
             */
            $user = $this->repository("user")->findOneBy([
                'email'  => $email,
                'active' => true,
            ]);

            if ($user == null) {
                /** @var RegistrationService $registrationService */
                $registrationService = Oforge()->Services()->get('frontend.user.management.registration');

                /** @var PasswordService $passwordService */
                $passwordService = Oforge()->Services()->get('password');
                $password        = $passwordService->hash($profile->identifier);

                $userData = $registrationService->register($email, $password);

                $registrationService->activate($userData["guid"]);

                /**
                 * @var $user User
                 */
                $user = $this->repository("user")->findOneBy([
                    'email'  => $email,
                    'active' => true,
                ]);

                if ($profile->displayName != null) {
                    $user->getDetail()->setNickName($profile->displayName);
                }

                if ($profile->firstName != null) {
                    $user->getDetail()->setFirstName($profile->firstName);
                }

                if ($profile->lastName != null) {
                    $user->getDetail()->setLastName($profile->lastName);
                }

                if ($profile->photoURL != null) {
                    /**
                     * @var $mediaService MediaService
                     */
                    $mediaService = Oforge()->Services()->get('media');
                    $media        = $mediaService->download($profile->photoURL, $userData['id'] . '.jpg', 'image/jpg');
                    $user->getDetail()->setImage($media);
                }

                $this->entityManager()->update($user->getDetail());
                $this->entityManager()->update($user);

                $this->entityManager()->create(SocialLogin::create([
                    'user'  => $user,
                    'token' => $profile->identifier,
                    'type'  => $type,
                ]));
            }

            $socialLogin = $this->repository('sociallogin')->findOneBy(['user' => $user, 'type' => $type]);

            if ($socialLogin != null) {
                /**
                 * @var FrontendUserLoginService $loginService
                 */
                $loginService = Oforge()->Services()->get('frontend.user.management.login');

                return $loginService->login($email, $socialLogin->getToken());
            }
        }

        return null;
    }

}
