<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 16.01.2019
 * Time: 11:45
 */

namespace Oforge\Engine\Modules\CMS\Abstracts;

use Oforge\Engine\Modules\CMS\Models\Content\ContentType;

abstract class AbstractContentType {
    protected $entityManager;
    protected $repository;
    
    private $configuration = [];
    private $localizable = false;
    private $content = Null;
    
    public function __construct() {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->repository = $this->entityManager->getRepository(ContentType::class);
    }
    
    abstract public function init();
    
    public  function getConfigurationKeys() {
        if (is_array($this->configuration)) {
            return array_keys($this->configuration);
        } else {
            return false;
        }
    }
    
    public function getConfigurationValue($key) {
        if (is_array($this->configuration) && array_key_exists($key, $this->configuration)) {
            return $this->configuration[$key];
        } else {
            return false;
        }
    }
    
    public function setConfigurationValue($key, $value) {
        if (is_array($this->configuration) && array_key_exists($key, $$this->configuration)) {
            $$this->configuration[$key] = $value;
        }
    }
    
    public function getLocalizable(): bool {
        return $this->localizable;
    }
    
    public function setLocalizable(bool $localizable) {
        $this->localizable = $localizable;
    }
    
    abstract public function getContent();
    
    abstract public function setContent($content);
    
    abstract public function save(array $params);
    
    abstract public function load(int $id);
}