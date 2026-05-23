<?php

namespace DilovanMatini\Enumable\Support;

use DilovanMatini\Enumable\Contracts\EnumSubsetContract;

/**
 * Builds isolated enum subsets that support the full Enumable static API (1.0.x compatible).
 *
 * Each subset is a unique class with its own registry key, so concurrent subsets do not overwrite each other.
 *
 * @internal
 */
final class SubsetFactory
{
    /** @var array<string, list<\UnitEnum>> */
    private static array $registry = [];

    private static int $sequence = 0;

    /**
     * @param  class-string  $enumClass
     * @param  list<\UnitEnum>  $cases
     */
    public static function create(string $enumClass, array $cases): EnumSubsetContract
    {
        $registryId = bin2hex(random_bytes(8));
        self::$registry[$registryId] = $cases;

        self::$sequence++;
        $className = 'GeneratedEnumSubset_'.self::$sequence;
        $registryKey = var_export($registryId, true);

        if (! class_exists(__NAMESPACE__.'\\'.$className, false)) {
            eval(<<<PHP
namespace DilovanMatini\Enumable\Support;

final class {$className} implements \\DilovanMatini\\Enumable\\Contracts\\EnumSubsetContract
{
    use \\DilovanMatini\\Enumable\\Traits\\Enumable;

    public static function cases(): array
    {
        return \\DilovanMatini\\Enumable\\Support\\SubsetFactory::resolveCases({$registryKey});
    }
}
PHP);
        }

        $fqn = __NAMESPACE__.'\\'.$className;

        $subset = new $fqn();

        assert($subset instanceof EnumSubsetContract);

        return $subset;
    }

    /**
     * @return list<\UnitEnum>
     */
    public static function resolveCases(string $registryId): array
    {
        return self::$registry[$registryId] ?? [];
    }
}
