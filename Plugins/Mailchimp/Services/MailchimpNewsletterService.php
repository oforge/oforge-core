<?php

namespace Mailchimp\Services;

use Mailchimp\Models\UserNewsletter;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;

class MailchimpNewsletterService extends AbstractDatabaseAccess
{
    protected $mailchimpUri;

    protected $apiraven;

    protected $configService;

    protected $userName;

    protected $apiKey;

    protected $dataCenter;

    protected $listId;

    /**
     * MailchimpNewsletterService constructor.
     * @throws ServiceNotFoundException
     */
    public function __construct()
    {
        parent::__construct(['default' => UserNewsletter::class]);

        $this->configService = $configService = Oforge()->Services()->get("config");
        $this->apiraven = Oforge()->Services()->get('apiraven');
    }

    /**
     * @param string $email_address
     * @param int|null $userId
     * @param array|null $tags
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addListMember(string $email_address, int $userId = null, $tags = null)
    {
        $this->updateParams();
        $emailMd5 = md5(strtolower($email_address));

        $mailchimpUri = $this->mailchimpUri . '/lists/' . $this->listId . '/members';

        $this->apiraven->setApi($mailchimpUri, $this->userName, $this->apiKey);

        $data = [
            'email_address' => $email_address,
            'status' => 'pending',
        ];

        if($tags != null) {
            $data['tags'] = $tags;
        }

        $result = $this->apiraven->put($emailMd5, $data);

        if ($result != false) {
            if ($userId != null) {
                $this->updateSubscriberStatus($userId, true);
            }
            Oforge()->View()->Flash()->addMessage('success', I18N::translate('subscribe_success', 'Du wurdest erfolgreich zum Newsletter angemeldet!'));
        } else {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('subscribe_failure', 'Ups, da ist wohl was schief gelaufen...'));
        }
    }

    /**
     * @param string $email_address
     * @param int|null $userId
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function removeListMember(string $email_address, int $userId = null)
    {
        $this->updateParams();
        $emailMd5 = md5(strtolower($email_address));

        $mailchimpUri = $this->mailchimpUri . '/lists/' . $this->listId . '/members';

        $this->apiraven->setApi($mailchimpUri, $this->userName, $this->apiKey);

        $data = [
            'email_address' => $email_address,
            'status' => 'unsubscribed'
        ];

        $result = $this->apiraven->put($emailMd5, $data);
        if ($result != false) {
            if ($userId != null) {
                $this->updateSubscriberStatus($userId, false);
            }
            Oforge()->View()->Flash()->addMessage('success', I18N::translate('unsubscribe_success', 'Du wurdest erfolgreich vom Newsletter abgemeldet!'));
        } else if ($result == false) {
            Oforge()->View()->Flash()->addMessage('warning', 'unsubscribe_failure', 'Ups, da ist wohl was schief gelaufen...');
        }
    }


    /**
     * @param int $user_id
     * @return boolean isSubscribed
     * @throws ORMException
     */
    public function isSubscribed(int $user_id)
    {
        /** @var UserNewsletter $user */
        $repository = $this->repository();
        $user = $repository->findOneBy(['userId' => $user_id]);
        if ($user === null) {
            $this->updateSubscriberStatus($user_id, false);
        }
        $user = $repository->findOneBy(['userId' => $user_id]);
        return $user->isSubscribed();
    }

    protected function updateParams()
    {
        $this->userName = $this->configService->get("mailchimp_username");
        $this->apiKey = $this->configService->get("mailchimp_api_key");
        $this->dataCenter = $this->configService->get("mailchimp_data_center");
        $this->listId = $this->configService->get("mailchimp_list_id");
        $this->mailchimpUri = $this->configService->get("mailchimp_uri");
        $this->mailchimpUri = $this->constructMailchimpUri($this->apiKey, $this->dataCenter);
    }

    /**
     * @param string $api_key
     * @param string $dataCenter
     * @return string $mailChimpUrl
     */
    protected function constructMailchimpUri(string $api_key, string $dataCenter)
    {
        $mailchimpUri = str_replace("{dc}", $dataCenter, $this->mailchimpUri);
        return $mailchimpUri;
    }

    /**
     * @param int $user_id
     * @param bool $subscribed
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function updateSubscriberStatus(int $user_id, bool $subscribed)
    {
        $repository = $this->repository();
        $user = $repository->findOneBy(['userId' => $user_id]);
        if ($user === null) {
            $user = new UserNewsletter();
            $user->setUserId($user_id);
            $user->setSubscribed($subscribed);
            $this->entityManager()->create($user);
        } else {
            $user->setUserId($user_id);
            $user->setSubscribed($subscribed);
            $this->entityManager()->update($user);
        }

    }
}
