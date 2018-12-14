<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:11
 */

namespace Oforge\Engine\Modules\I18n\Services;
use Oforge\Engine\Modules\I18n\Models\Snippets;


/**
 * Class InternationalizationService
 * @package Oforge\Engine\Modules\I18n\Services
 */
class InternationalizationService {
    /**
     * InternationalizationService constructor.
     */
    public function __construct() {
        $this->em = Oforge()->DB()->getManager();
        $this->repo = $this->em->getRepository(Snippets::class);
    }
}
