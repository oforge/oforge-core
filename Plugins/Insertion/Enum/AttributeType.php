<?php

namespace Insertion\Enum;

abstract class AttributeType {
    const MULTI     = 'multi';
    const SINGLE    = 'single';
    const RANGE     = 'range';
    const DATE     = 'date';
    const DATEYEAR     = 'date-year';
    const DATEMONTH     = 'date-month';
    const BOOLEAN   = 'boolean';
    const NUMBER    = 'number';
    const TEXT      = 'text';
    const CONTAINER   = 'container';
}
