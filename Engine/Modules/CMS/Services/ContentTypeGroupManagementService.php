<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class ContentTypeGroupManagementService
 *
 * @package Oforge\Engine\Modules\CMS\Services
 */
class ContentTypeGroupManagementService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(ContentTypeGroup::class);
    }

    /**
     * Optional label key for i18n value(s), must be string (english default) or array (language => default). The i18n label key will be created internally.
     *
     * @param array $options
     *
     * @return int ID of group.
     * @throws ORMException
     * @throws ConfigOptionKeyNotExistException
     */
    public function add(array $options) : int {
        $element = $this->repository()->findOneBy(['name' => $options['name']]);
        if (!isset($element)) {
            if ($this->isValid($options)) {
                if (isset($options['label'])) {
                    $defaults = $options['label'];
                    $labelKey = 'cms_content_type_group_label_' . $options['name'];
                    I18N::translate($labelKey, $defaults);
                    unset($options['label']);
                }
                $element = ContentTypeGroup::create($options);
                $this->entityManager()->create($element);
            }
        }

        return $element->getId();
    }

    /**
     * @param $name
     *
     * @return array Tree of SidebarNavigation data
     * @throws ORMException
     */
    public function get($name) {
        /** @var ContentTypeGroup[] $entries */
        $entry = $this->repository()->findBy(['name' => $name]);

        return $entry;
    }

    /**
     * @param array $options
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistException
     */
    private function isValid($options) {
        // Check if required keys are within the options
        $keys = ['name'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExistException($key);
            }
        }
        //Check if correct type are set
        $keys = ['label'];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_string($options[$key]) && !is_array($options[$key])) {
                throw new InvalidArgumentException("I18N $key value should be of type string (english default) or array(language=>default).");
            }
        }

        return true;
    }
}
