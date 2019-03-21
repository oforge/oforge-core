<?php
/**
 * Created by PhpStorm.
 * User: steffen
 * Date: 20.03.19
 * Time: 13:44
 */

namespace Helpdesk\Services;

use Helpdesk\Models\Ticket;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class HelpdeskTicketService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default' => Ticket::class,
        ]);
    }

}