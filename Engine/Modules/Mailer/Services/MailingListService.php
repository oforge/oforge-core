<?php

namespace Oforge\Engine\Modules\Mailer\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Mailer\Models\Mail;
use Doctrine\ORM\ORMException;

class MailingListService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(['default' => Mail::class]);
    }

    /**
     * @param string $method
     * @param array $params
     *
     * @throws ORMException
     */
    public function createNewMail(string $method, array $params) {
        $mail = new Mail();
        $mail->setMethod($method);
        $mail->setParams($params);
        $this->entityManager()->create($mail);
    }

    public function createNewDummyMail() {
        $this->createNewMail('dummy_method', [1,2,3]);
    }

}