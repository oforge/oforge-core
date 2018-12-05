<?php
namespace Oforge\Engine\Modules\Core\Services;

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
     */
    public function register(Array $endpoints) {
        $this->saveEndpoints($endpoints);
    }
    
    /**
     * Remove Route endpoints
     */
    public function unregister() {
        // TODO: implement unregister function
    }
    
    /**
     * Save the Route endpoints
     *
     * @param $endpoints
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function saveEndpoints($endpoints) {
        $em = Oforge()->DB()->getManager();
        $repo =  $em->getRepository(Route::class);

        $methodCollection = [];

        foreach($endpoints as $path => $config ) {
            $isRoot = $path == "/" || "";

            if(is_array($config)) {
                if(array_key_exists("controller", $config)) {

                    $methods = get_class_methods($config["controller"]);
                    foreach($methods as $method) {
                        if(StringHelper::endsWith($method, "Action")) {
                            if($method == "indexAction") {
                                $key = array_key_exists("name", $config) ? $config["name"] : str_replace("/", "_", $path);

                                array_push($methodCollection, ["path" => $path, "controller" => $config["controller"] . ':' . $method, "name" =>  $key]);
                            } else {
                                $custom_path = $path . ($isRoot ? "" : "/"). substr($method, 0, -6);
                                $key = array_key_exists("name", $config) ? ($config["name"] . "_"  . substr($method, 0, -6)) : ($path . ($isRoot ? "" : "/"). substr($method, 0, -6));

                                array_push($methodCollection, ["path" => $custom_path, "controller" => $config["controller"] . ':' . $method, "name" =>  $key]);
                            }
                        }
                    }
                }
            } else {
                $methods = get_class_methods($config);

                foreach($methods as $method) {
                    if(StringHelper::endsWith($method, "Action")) {
                        if($method == "indexAction") {
                            $key = str_replace("/", "_", $path);

                            array_push($methodCollection, ["path" => $path, "controller" => $config . ':' . $method, "name" =>  $key]);
                        } else {
                            $custom_path = $path . ($isRoot ? "" : "/"). substr($method, 0, -6);
                            $key = str_replace("/", "_", $custom_path);

                            array_push($methodCollection, ["path" => $custom_path, "controller" => $config . ':' . $method, "name" =>  $key]);
                        }
                    }
                }
            }
        }

        foreach($methodCollection as $method) {
            $r = $repo->findBy(array('name' => $method["name"]));

            if(sizeof($r) == 0) {
                $route = new Route();
                $route->fromArray($method);
                $route->setLanguageId("de");
                $route->setActivate(true);
                $em->persist($route);
            }
        }

        $em->flush();
    }
}
