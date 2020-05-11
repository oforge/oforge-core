<?php

namespace TestPlugin\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Doctrine\ORM\ORMException;
use TestPlugin\Models\TestModel;

class TestService extends AbstractDatabaseAccess
{
    public function __construct()
    {
        parent::__construct([
            'testmodel' => TestModel::class,
        ]);
    }

    /**
     * @return mixed
     * @throws ORMException
     */
    public function getAll()
    {
        $repository = $this->repository('testmodel');
        $datein = $repository->findAll();

        /*$result = [];
        foreach ($datein as $data) {*/
        /** var TestModel $test_1 */
        /* $result = [$data->getTest1(), $test->getTest2()];
        }*/
        return $datein;
    }
}
