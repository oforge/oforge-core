<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 17.12.2018
 * Time: 09:58
 */

namespace Oforge\Engine\Modules\UserManagement\Services;

use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\PasswordService;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;

class BaseUsersCrudService {

    /**
     * @var $passwordService PasswordService
     */
    protected $passwordService;

    /**
     * @var $crudService GenericCrudService
     */
    protected $crudService;

    /**
     * @var $userModel string
     */
    protected $userModel;

    /**
     * UserCrudService constructor.
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function __construct() {
        $this->passwordService = Oforge()->Services()->get("password");
        $this->crudService     = Oforge()->Services()->get("crud");
    }

    /**
     * @param $userData
     *
     * @return null
     */
    public function create($userData) {
        if (isset($userData["password"]) && strlen($userData["password"]) > 5) {
            $userData["password"] = $this->passwordService->hash($userData["password"]);
            try {
                $this->crudService->create($this->userModel, $userData);
            } catch (\Exception $e) {
                $msg = $e->getPrevious();
                if (isset($msg)) {
                    $msg = $msg->getMessage();
                } else {
                    $msg = $e->getMessage();
                }
                Oforge()->Logger()->get()->addWarning("Error trying to create a new user. ", ["e" => $msg]);

                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @param $userData array
     *
     * @return bool
     * @throws \Oforge\Engine\Modules\Core\Exceptions\NotFoundException
     */
    public function update($userData) {
        if (key_exists("password", $userData)) {
            $userData["password"] = $this->passwordService->hash($userData["password"]);
        }
        $this->crudService->update($this->userModel, $userData);

        return true;
    }

    /**
     * @param $userId
     */
    public function delete($userId) {
        $this->crudService->delete($this->userModel, $userId);
    }

    /**
     * TODO: Check if the user data is valid. What data has to be validated?
     *
     * @param $userData array
     *
     * @return bool
     */
    public function isValid($userData) {
        // TODO: validation
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function list(array $params) {
        return $this->crudService->list($this->userModel, $params);
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getById(int $id) {
        return $this->crudService->getById($this->userModel, $id);;
    }
}