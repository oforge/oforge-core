<?php

namespace Mailchimp\Services;

use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

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
        parent::__construct(['default' => User::class]);

        $this->configService = $configService = Oforge()->Services()->get("config");
        $this->apiraven = Oforge()->Services()->get('apiraven');
    }

    /**
     * @param string $email_address
     * @throws ServiceNotFoundException
     */
    public function addListMember(string $email_address)
    {
        $this->updateParams();
        $mailchimpUri = $this->mailchimpUri . '/lists/' . $this->listId;

        $this->apiraven->setApi($mailchimpUri, $this->userName, $this->apiKey);

        $data = [
            'email_address' => $email_address,
            'status' => 'pending',
        ];

        $result = $this->apiraven->post('members', $data);

        if ($result != false) {
            Oforge()->View()->addFlashMessage('success', 'You are subscribed successfully!');
        } else {
            Oforge()->View()->addFlashMessage('warning', 'Something went wrong...');
        }
    }

    /**
     * @param string $email_address
     */
    public function removeListMember(string $email_address)
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
        if ($result != '200') {
            Oforge()->View()->addFlashMessage('success', 'If your email was known to us you are now unsubscribed successfully!');
        } else if ($result == false) {
            Oforge()->View()->addFlashMessage('warning', 'Something went wrong...');
        }
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
     * @return string
     */
    protected function constructMailchimpUri(string $api_key, string $dataCenter)
    {
        $mailchimpUri = str_replace("{dc}", $dataCenter, $this->mailchimpUri);
        return $mailchimpUri;
    }
}


