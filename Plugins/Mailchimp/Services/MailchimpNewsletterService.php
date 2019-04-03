<?php

namespace Mailchimp\Services;

use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class MailchimpNewsletterService extends AbstractDatabaseAccess
{

    public function __construct()
    {
        parent::__construct(['default' => User::class]);
    }

    /**
     *
     * @param string $email_address
     */
    public function addListMember(string $email_address)
    {
//        $listId = "foo";
//        $mailchimpUri = 'https://usX.api.mailchimp.com/3.0/lists/' . $listId . '/members';
//
//
//        $data = [
//            'email_address' => $email_address,
//            'status' => 'subscribed',
//        ];
//
//        $options = [
//            'http' => [
//                'header' => 'content-type: application/json',
//                'method' => 'POST',
//                'content' => http_build_query($data),
//            ],
//        ];
//
//        $context = stream_context_create($options);
//        $result = file_get_contents($mailchimpUri, false, $context);
//        if ($result === false) { /* Handle error */
//        }
//
//        var_dump($result);
    }

    public function removeListMember()
    {
    }

    public function deleteListMember()
    {
    }
}