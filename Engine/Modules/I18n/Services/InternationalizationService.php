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
    /** @var array $cache */
    private $cache;

    public function __construct() {
        parent::__construct(Snippet::class);
    }

    /**
     * @param string $key
     * @param string $language
     * @param string|null $defaultValue
     *
     * @return string
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function get(string $key, string $language, ?string $defaultValue = null) : string {
        if (!isset($this->cache[$key])) {
            /** @var Snippet $snippet */
            $snippet = $this->repository()->findOneBy([
                'name'  => $key,
                'scope' => $language,
            ]);
            if (!isset($snippet)) {
                $snippet = Snippet::create([
                    'name'  => $key,
                    'scope' => $language,
                    'value' => isset($defaultValue) ? $defaultValue : $key,
                ]);
                $this->entityManager()->create($snippet);
            }
            $this->cache[$key] = $snippet->getValue();
        }

        return $this->cache[$key];
    }

    /**
     * Creates / Updates Text-Snippets from given .csv file.
     * A Text-Snippet consists of three values: Scope, Name and Value
     * Therefore a line in the .csv file should consist of exactly three entries:
     * scope|name|value ( no whitespace ), where '|' acts as the separator.
     *
     * As of this point, this function can only be called from the console
     * ( i.e. 'php /bin/console oforge:service:run i18n:insertFromCsv mysnippets.csv' ).
     *
     * @param string $fileName
     *
     * @return bool|string
     * @throws ORMException
     */
    public function insertFromCsv($fileName = '') {
        if ($handle = fopen($fileName, 'r')) {
            $entitiesCreated = 0;
            $entitiesUpdated  = 0;
            while ($row = fgetcsv($handle, 0, '|')) {
                $snippet = $this->repository()->findOneBy([
                    'scope' => $row[0],
                    'name'  => $row[1],
                ]);
                if (!isset($snippet)) {
                    $entitiesCreated += 1;
                    $snippet = Snippet::create([
                        'scope' => $row[0],
                        'name'  => $row[1],
                        'value' => $row[2],
                    ]);
                    $this->entityManager()->create($snippet);
                } else {
                    $entitiesUpdated += 1;
                    $snippet->setValue($row[2]);
                    $this->entityManager()->flush();
                }
            };
            fclose($handle);

            return $entitiesCreated . ' snippet(s) added, ' . $entitiesUpdated . ' snippet(s) updated';
        }
        return false;
    }

    public function showParams($param) {
        return $param;
    }

    /**
     * @param string $key
     * @param string $language
     *
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function exists(string $key, string $language) : bool {
        if (!isset($this->cache[$key])) {
            /** @var Snippet $snippet */
            $snippet = $this->repository()->findOneBy([
                'name'  => $key,
                'scope' => $language,
            ]);

            return isset($snippet);
        }

        return true;
    }

}
