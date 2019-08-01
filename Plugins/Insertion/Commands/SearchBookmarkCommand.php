<?php

namespace Insertion\Commands;

use GetOpt\GetOpt;
use GetOpt\Option;
use Insertion\Services\InsertionSearchBookmarkService;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;

class SearchBookmarkCommand extends AbstractCommand{

    /**
     * ReminderCommand constructor.
     */
    public function __construct() {
        parent::__construct('oforge:plugin:insertion:searchbookmark', self::TYPE_DEFAULT);
        $this->setDescription('Search bookmark mail distributor');
    }

    /**
     * Command handle function.
     *
     * @param Input $input
     * @param Logger $output
     */
    public function handle(Input $input, Logger $output) : void {
        /** @var InsertionSearchBookmarkService $searchBookmarkService */
        $searchBookmarkService = Oforge()->Services()->get('insertion.search.bookmark')
    }
}
