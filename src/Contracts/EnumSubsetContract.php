<?php

namespace DilovanMatini\Enumable\Contracts;

use UnitEnum;

/**
 * Contract for filtered enum subsets returned by only(), except(), and generate().
 */
interface EnumSubsetContract
{
    /**
     * @return list<UnitEnum>
     */
    public static function cases(): array;
}
