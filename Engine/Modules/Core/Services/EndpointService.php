<?php

namespace Oforge\Engine\Modules\Core\Services;

use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
use Oforge\Engine\Modules\Core\Models\Routes\Route;

class EndpointService
{
    /**
     * Store Route endpoints in a database table
     *
     * @param array $endpoints
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws ConfigOptionKeyNotExists
     */
    public function register(Array $endpoints)
    {
        $this->saveEndpoints($endpoints);
    }

    /**
     * Remove Route endpoints
     */
    public function unregister()
    {
        // TODO: implement unregister function
    }
    
    /**
     * Save the Route endpoints
     *
     * @param $endpoints
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws ConfigOptionKeyNotExists
     */
    protected function saveEndpoints($endpoints)
    {
        $em = Oforge()->DB()->getManager();
        $repo = $em->getRepository(Route::class);

        $methodCollection = [];

        foreach ($endpoints as $path => $config) {
            $isRoot = $path == "/" || "";

            if ($this->isValid($config)) {
                $methods = get_class_methods($config["controller"]);

                foreach ($methods as $method) {
                    if (strpos($method, "Action") === strlen($method) - 6) {

                        $scope = array_key_exists("asset_scope", $config) ? $config["asset_scope"] : "frontend";
                        $order = array_key_exists("order", $config) ? $config["order"] : 1337;
                        if ($method == "indexAction") {
                            $key = array_key_exists("name", $config) ? $config["name"] : str_replace("/", "_", $path);

                            array_push($methodCollection, ["path" => $path, "controller" => $config["controller"] . ':' . $method, "name" => $key, "asset_scope" => $scope, "order" => $order]);
                        } else {
                            $custom_path = $path . ($isRoot ? "" : "/") . substr($method, 0, -6);
                            $key = array_key_exists("name", $config) ? ($config["name"] . "_" . substr($method, 0, -6)) : ($path . ($isRoot ? "" : "/") . substr($method, 0, -6));

                            array_push($methodCollection, ["path" => $custom_path, "controller" => $config["controller"] . ':' . $method, "name" => $key, "asset_scope" => $scope, "order" => $order]);
                        }
                    }
                }
            }
        }

        foreach ($methodCollection as $method) {
            $r = $repo->findBy(array('name' => $method["name"]));

            if (sizeof($r) == 0) {
                $route = new Route();
                $route->fromArray($method);
                $route->setLanguageId("de"); // TODO make default language out of config
                $route->setActivate(true);
                $em->persist($route);
            }
        }

        $em->flush();
    }

    private function isValid($options)
    {
        /**
         * Check if required keys are within the options
         */
        $keys = ["controller", "name"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) throw new ConfigOptionKeyNotExists($key);
        }

        return true;
    }

}
