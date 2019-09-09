<?php
/**
 * After the insertion creation process, users can submit feedback. This widget handles and displays that data in the backend.
 */

namespace Insertion\Widgets;

use Insertion\Models\InsertionFeedback;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\DashboardWidgetInterface;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class InsertionFeedbackWidget
 *
 * @package Insertion\Widgets
 */
class InsertionFeedbackWidget extends AbstractDatabaseAccess implements DashboardWidgetInterface {

    public function __construct() {
        parent::__construct(InsertionFeedback::class);
    }

    /** @inheritDoc */
    function prepareData() : array {
        $feedbackEntries = $this->repository()->count([]);

        return ['count' => $feedbackEntries];
    }

}
