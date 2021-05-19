<?php

namespace Oforge\Engine\Modules\Mailer\Library;

use Oforge\Engine\Modules\Core\Helper\FileSystemHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Class OforgePHPMailer
 *
 * @package Oforge\Engine\Modules\Mailer\Library
 */
class OforgePHPMailer extends PHPMailer
{
    private const DIR_MAILS = ROOT_PATH . Statics::GLOBAL_SEPARATOR . Statics::VAR_DIR . Statics::GLOBAL_SEPARATOR . 'mail';

    /**
     * @inheritDoc
     */
    public function postSend() : bool
    {
        $sendMode = Oforge()->Settings()->get('Modules.Mailer.mode', 'send');
        if ($sendMode === 'file') {
            $message  = $this->getSentMIMEMessage();
            $filepath = self::DIR_MAILS . Statics::GLOBAL_SEPARATOR . date('Y_m_d_H_i_s') . '.eml';
            FileSystemHelper::mkdir(self::DIR_MAILS);
            file_put_contents($filepath, $message);

            return file_exists($filepath);
        } else {
            return parent::postSend();
        }
    }

}
