<?php

namespace Oforge\Engine\Modules\Mailer\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Mailer\Models\Mail;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

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

    /**
     * @throws ORMException
     */
    public function createNewDummyMails() {
        $this->createNewMail('sendInsertionApprovedInfoMail', [1]);
        $this->createNewMail('sendInsertionApprovedInfoMail', [1]);
        $this->createNewMail('sendInsertionApprovedInfoMail', [1]);
    }

    /**
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function batchSend() {
        /** @var MailService $mailService */
        $mailService = Oforge()->Services()->get('mail');
        /** @var Mail[] $unsentMails */
        $unsentMails = $this->repository()->findBy(['sent' => false]);

        foreach ($unsentMails as $mail) {
            if (call_user_func_array([$mailService, $mail->getMethod()], $mail->getParams())) {
               $mail->setSent(true);
               $this->entityManager()->update($mail);
            }
        }
    }
}
