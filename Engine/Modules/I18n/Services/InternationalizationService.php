<?php

namespace Oforge\Engine\Modules\I18n\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\I18n\Models\Snippet;

/**
 * Class InternationalizationService
 *
 * @package Oforge\Engine\Modules\I18n\Services
 */
class InternationalizationService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(['default' => Snippet::class]);
    }

    /**
     * @param $key
     * @param $language
     * @param null $defaultValue
     *
     * @return string
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function get($key, $language, $defaultValue = null) {
        /** @var Snippet $snippet */
        $snippet = $this->repository()->findOneBy([
            'name'  => $key,
            'scope' => $language,
        ]);
        if (!isset($snippet)) {
            $snippet = Snippet::create([
                'name'  => $key,
                'scope' => $language,
                'value' => $defaultValue ? : $key,
            ]);
            $this->entityManager()->persist($snippet);
            $this->entityManager()->flush($snippet);
        }

        return $snippet->getValue();
    }
}
