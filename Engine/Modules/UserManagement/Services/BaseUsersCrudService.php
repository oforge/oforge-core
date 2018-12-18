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
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function __construct() {
        $this->passwordService = Oforge()->Services()->get("password");
        $this->crudService = Oforge()->Services()->get("crud");
    }
    
    /**
     *
     * @param $userData
     *
     * @return null
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists
     */
    public function create($userData) {
        $password = null;
        
        
        $userData["password"] = $this->passwordService->hash($userData["password"]);
        $this->crudService->create($this->userModel, $userData);
        return true;
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
        $user = [];
        /**
         * @var $result User|BackendUser
         */
        $result =  $this->crudService->getById($this->userModel, $id);
        $user["id"] = $result->getId();
        $user["email"] = $result->getEmail();
        
        if ($this->userModel == BackendUser::class) {
            $user["role"] = $result->getRole();
        }
        
        return $user;
    }
}