<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 23.11.2018
 * Time: 12:25
 */

namespace Test\Services;

use Test\Models\Test\Test;

class TestService {
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addTestData() {
        $em = Oforge()->DB()->getEnityManager();
        $repo = $em->getRepository(Test::class);
        $testData = $repo->findOneBy(["name" => "Test"]);
        
        if (!isset($testData)) {
            $testData = new Test();
            $testData->setName("Test");
            $em->persist($testData);
        }
        $em->flush();
    }
}
