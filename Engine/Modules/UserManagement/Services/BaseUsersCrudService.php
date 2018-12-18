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
        
        if (!$this->isValid($userData)) {
            Oforge()->Logger()->get()->addWarning("Invalid user data.");
            return false;
        }
        
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
        if (!$this->isValid($userData)) {
            Oforge()->Logger()->get()->addWarning("Invalid user data.");
            return false;
        }
        
        if (key_exists("password", $userData)) {
            unset($userData["password"]);
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
        if (sizeof($userData) < 1) {
            return false;
        }
        
        if (!key_exists("email", $userData) ||
            !key_exists("password", $userData)) {
            return false;
        }
        
        if ($this->userModel == BackendUser::class &&
            !key_exists("role", $userData)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * @param array $params
     *
     * @return array
     */
    public function list(array $params) {
        return $this->crudService->list($this->userModel, $params);
    }
}