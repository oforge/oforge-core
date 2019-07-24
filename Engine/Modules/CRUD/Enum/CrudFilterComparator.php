<?php

namespace Oforge\Engine\Modules\CRUD\Enum;

/**
 * Class CrudFilterComparator
 *
 * @package Oforge\Engine\Modules\CRUD\Enum
 */
class CrudFilterComparator {
    public const LIKE           = 'like';
    public const NOT_LIKE       = 'notLike';
    public const GREATER        = 'gt';
    public const GREATER_EQUALS = 'gte';
    public const EQUALS         = 'eq';
    public const NOT_EQUALS     = 'neq';
    public const LESS           = 'lt';
    public const LESS_EQUALS    = 'lte';
    // public const IN             = 'in';
    // public const NOT_IN         = 'notIn';
}
