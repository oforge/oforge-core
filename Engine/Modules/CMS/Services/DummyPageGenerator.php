<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 17.01.2019
 * Time: 10:52
 */

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Import\Services\ImportService;

class DummyPageGenerator
{
    public function create()
    {
        /**
         * @var $importService ImportService
         */
        $importService = Oforge()->Services()->get("import");
        $files = [
            "oforge_i18n_language",
            "oforge_cms_content_type_group",
            "oforge_cms_content_type",
            "oforge_cms_content",
            "oforge_cms_site",
            "oforge_cms_page",
            "oforge_cms_page_path",
            "oforge_cms_page_content"
        ];

        $fullDir = ROOT_PATH . DIRECTORY_SEPARATOR . Statics::VAR_DIR . DIRECTORY_SEPARATOR . "dummy_data" . DIRECTORY_SEPARATOR;

        foreach ($files as $file) {
            try {
                $importService->processFile($fullDir, $file, false);
            } catch (\Exception $e) {
                echo $e;
            }
        }

    }


}