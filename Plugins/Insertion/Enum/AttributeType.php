<?php

namespace Insertion\Enum;

abstract class AttributeType {
    const MULTI     = 'multi';
    const SINGLE    = 'single';
    const RANGE     = 'range';
    const DATE      = 'date';
    const DATEYEAR  = 'dateyear';
    const DATEMONTH = 'datemonth';
    const BOOLEAN   = 'boolean';
    const NUMBER    = 'number';
    const TEXT      = 'text';
    const CONTAINER = 'container';
    const PEDIGREE  = 'pedigree';
}
