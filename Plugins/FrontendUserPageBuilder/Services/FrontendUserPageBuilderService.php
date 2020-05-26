<?php


namespace FrontendUserPageBuilder\Services;


use CMS\Models\ContextArea\ContextAreaPagePath;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class FrontendUserPageBuilderService extends AbstractDatabaseAccess
{

    public function __construct()
    {
        parent::__construct(['default' => ContextAreaPagePath::class]);
    }

    public function getAllUserPages()
    {
        $userPages = [
            [
                'id' => 12345,
                'name' => 'Wamo',
            ],
            [
                'id' => 00000,
                'name' => 'Sama',
            ],
            [
                'id' => 6734567,
                'name' => 'Playa',
            ]
        ];

        return $userPages;
    }
}
