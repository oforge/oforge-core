<?php

namespace Oforge\Engine\Modules\AdminBackend\Services;

class Permissions
{
    private $methods = [];

    public function put($method, $userType, $role = 0)
    {
        $this->methods[$method] = ["method" => $method, "type" => $userType, "role" => $role];
    }

    public function get($method): ?array
    {
        if (key_exists($method, $this->methods)) {
            return $this->methods[$method];
        }

        $explode = explode(":", $method);

        $className = "";
        if (sizeof($explode) == 2) {
            $className = $explode[0];
        }

        $instance = new $className();
        if (method_exists($instance, "initPermissions")) {
            $instance->initPermissions();
        }

        return key_exists($method, $this->methods) ? $this->methods[$method] : null;
    }
}