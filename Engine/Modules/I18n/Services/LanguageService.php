<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:11
 */

namespace Oforge\Engine\Modules\I18n\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\I18n\Models\Language;


/**
 * Class LanguageService
 * @package Oforge\Engine\Modules\I18n\Services
 */
class LanguageService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => Language::class]);
    }

    public function list() {
        $this->repository()->findAll();
    }

    public function create(array $options) {
        if($this->isValid($options)) {

        }
    }

    public function update(array $options) {
        if($this->isValid($options, false)) {

            /**
             * @var $element Language
             */
            $element = $this->repository()->findOneBy(["iso" => strtolower($options["iso"])]);
            if (!isset($element)) {
                throw new NotFoundException("Language with code ". $options["iso"] . " not found!");
            }

            $element->fromArray($options);
            $this->em->flush();
        }
    }

    public function delete(int $id) {

    }

    private function isValid($options, $checkExisting = true)
    {
        /**
         * Check if required keys are within the options
         */
        $keys = ["iso", "name"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) throw new ConfigOptionKeyNotExists($key);
        }

        /**
         * Check if the element is already within the system
         */

        $element = $this->repository()->findOneBy(["iso" => strtolower($options["iso"])]);
        if (isset($element)) throw new ConfigElementAlreadyExists(strtolower($options["iso"]));

        /**
         * Check if correct type are set
         */
        $keys = ["iso", "name"];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_string($options[$key])) throw new \InvalidArgumentException("$key value should be of type string.");
        }
        return true;
    }
}
