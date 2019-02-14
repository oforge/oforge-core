<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:11
 */

namespace Oforge\Engine\Modules\I18n\Services;

use Monolog\Logger;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\I18n\Models\Snippet;


/**
 * Class InternationalizationService
 * @package Oforge\Engine\Modules\I18n\Services
 */
class InternationalizationService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => Snippet::class]);
    }


    public function get($key, $language)
    {
        /**
         * @var $element Snippet
         */
        $element = $this->repository()->findOneBy(["scope" => $language, "name" => $key]);
        if (isset($element)) {
            return $element->getValue();
        }

        $element = Snippet::create(["scope" => $language, "name" => $key, "value" => $key]);

        $this->entityManager()->persist($element);
        $this->entityManager()->flush();

        return $element->getValue();
    }
}
