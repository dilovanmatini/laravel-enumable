<?php

namespace DilovanMatini\Enumable\Traits;

use BackedEnum;
use DilovanMatini\Enumable\Contracts\EnumSubsetContract;
use DilovanMatini\Enumable\Support\SubsetFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum as EnumRule;
use UnitEnum;

trait Enumable
{
    /*
    |--------------------------------------------------------------------------
    | Class related methods
    |--------------------------------------------------------------------------
    */

    /**
     * @return list<int|string>
     */
    public static function values(): array
    {
        static::ensureBackedEnum();

        return array_column(static::cases(), 'value');
    }

    /**
     * @return list<string>
     */
    public static function names(): array
    {
        return array_column(static::cases(), 'name');
    }

    /**
     * @return array<int|string, string>
     */
    public static function labels(): array
    {
        static::ensureBackedEnum();

        $items = [];

        foreach (static::cases() as $case) {
            $items[$case->value] = $case->label();
        }

        return $items;
    }

    /**
     * @return array<string, string>
     */
    public static function toSelectArrayByName(): array
    {
        $items = [];

        foreach (static::cases() as $case) {
            $items[$case->name] = $case->label();
        }

        return $items;
    }

    /**
     * @param  int|string  $value
     * @return static|null
     */
    public static function getCase(int|string $value): ?static
    {
        if (static::isBackedEnum()) {
            return static::tryFrom($value);
        }

        foreach (static::cases() as $case) {
            if ($case->name === (string) $value) {
                return $case;
            }
        }

        return null;
    }

    /**
     * @param  int|string  $value
     */
    public static function tryFromValue(int|string $value): ?static
    {
        return static::getCase($value);
    }

    public static function fromName(string $name): ?static
    {
        foreach (static::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }

    public static function fromNameOrDefault(string $name): ?static
    {
        return static::fromName($name) ?? static::default();
    }

    /**
     * @param  int|string  $value
     */
    public static function getName(int|string $value): string
    {
        $case = static::getCase($value);

        return $case === null ? '' : $case->name;
    }

    /**
     * @param  int|string  $value
     */
    public static function getLabel(int|string $value): string
    {
        $labels = static::resolvedCustomLabels();

        if (isset($labels[$value])) {
            return $labels[$value];
        }

        return static::getCase($value)?->headline() ?? '';
    }

    /**
     * @return array<int|string, string>
     */
    public static function toArray(): array
    {
        static::ensureBackedEnum();

        return array_combine(static::values(), static::names());
    }

    /**
     * @return Collection<int, mixed>
     */
    public static function toCollection(): Collection
    {
        return collect(static::cases());
    }

    /**
     * @return array<int|string, string>
     */
    public static function toSelectArray(): array
    {
        return static::labels();
    }

    public static function exists(UnitEnum|string|int $value): bool
    {
        if ($value instanceof UnitEnum) {
            if ($value instanceof BackedEnum) {
                $value = $value->value;
            } else {
                $value = $value->name;
            }
        }

        if (static::isBackedEnum()) {
            return in_array($value, static::values(), true);
        }

        return in_array($value, static::names(), true);
    }

    public static function random(): static
    {
        $cases = static::cases();

        return $cases[array_rand($cases)];
    }

    public static function default(): static
    {
        return static::first();
    }

    public static function first(): static
    {
        return static::cases()[0];
    }

    public static function last(): static
    {
        $cases = static::cases();

        return $cases[count($cases) - 1];
    }

    public static function count(): int
    {
        return count(static::cases());
    }

    /**
     * @deprecated Use labelsMap() instead. Will be removed in v2.0.
     *
     * @return array<int|string, string>|null
     */
    public static function setLabels(): ?array
    {
        return static::labelsMap();
    }

    /**
     * Override to provide custom labels keyed by backed value.
     *
     * @return array<int|string, string>|null
     */
    public static function labelsMap(): ?array
    {
        return null;
    }

    /**
     * @return array<int|string, string>
     */
    protected static function resolvedCustomLabels(): array
    {
        static $cache = [];

        if (! array_key_exists(static::class, $cache)) {
            $labels = static::setLabels();

            $cache[static::class] = $labels ?? [];
        }

        return $cache[static::class];
    }

    /**
     * @param  array<int|string|UnitEnum>  $cases
     */
    public static function only(array $cases): EnumSubsetContract
    {
        return static::generate($cases);
    }

    /**
     * @param  array<int|string|UnitEnum>  $cases
     */
    public static function except(array $cases): EnumSubsetContract
    {
        static::ensureBackedEnum();

        $excluded = static::normalizeToValues($cases);

        return static::generate(array_values(array_diff(static::values(), $excluded)));
    }

    /**
     * @param  array<int|string|UnitEnum>  $cases
     */
    public static function generate(array $cases): EnumSubsetContract
    {
        static::ensureBackedEnum();

        $resolved = [];

        foreach ($cases as $case) {
            $resolvedCase = static::resolveCaseReference($case);

            if ($resolvedCase !== null) {
                $resolved[] = $resolvedCase;
            }
        }

        return SubsetFactory::create(static::class, $resolved);
    }

    public static function rule(): EnumRule
    {
        return Rule::enum(static::class);
    }

    /**
     * @param  array<int|string|UnitEnum>  $cases
     */
    public static function ruleOnly(array $cases): \Illuminate\Validation\Rules\In
    {
        return Rule::in(static::valuesFromSubset(static::only($cases)));
    }

    /**
     * @param  array<int|string|UnitEnum>  $cases
     */
    public static function ruleExcept(array $cases): \Illuminate\Validation\Rules\In
    {
        return Rule::in(static::valuesFromSubset(static::except($cases)));
    }

    /**
     * @return list<int|string>
     */
    protected static function valuesFromSubset(EnumSubsetContract $subset): array
    {
        $values = [];

        foreach ($subset::cases() as $case) {
            if ($case instanceof BackedEnum) {
                $values[] = $case->value;
            }
        }

        return $values;
    }

    /*
    |--------------------------------------------------------------------------
    | Instance methods
    |--------------------------------------------------------------------------
    */

    public function label(): string
    {
        if ($this instanceof BackedEnum) {
            return static::getLabel($this->value);
        }

        return $this->headline();
    }

    public function headline(): string
    {
        return Str::headline($this->name);
    }

    /**
     * @param  array<string, string>  $replace
     */
    public function trans(?string $key = null, array $replace = [], ?string $locale = null): string
    {
        if ($key === null) {
            $basename = Str::snake(class_basename(static::class));
            $suffix = $this instanceof BackedEnum ? (string) $this->value : $this->name;
            $key = "enums.{$basename}.{$suffix}";
        }

        return __($key, $replace, $locale);
    }

    public function is(UnitEnum|string|int $other): bool
    {
        if ($other instanceof UnitEnum) {
            return $this === $other;
        }

        if ($this instanceof BackedEnum) {
            return $this->value === $other;
        }

        return $this->name === $other;
    }

    /**
     * @param  array<int|string|UnitEnum>  $others
     */
    public function isAny(array $others): bool
    {
        foreach ($others as $other) {
            if ($this->is($other)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int|string|UnitEnum>  $others
     */
    public function in(array $others): bool
    {
        return $this->isAny($others);
    }

    public function str(bool $label = false): EnumStringable
    {
        $value = $label ? $this->label() : ($this instanceof BackedEnum ? (string) $this->value : $this->name);

        return new EnumStringable($value);
    }

    /**
     * @param  int|string|UnitEnum  $case
     * @return static|null
     */
    protected static function resolveCaseReference(int|string|UnitEnum $case): ?static
    {
        if ($case instanceof UnitEnum) {
            return $case instanceof static ? $case : null;
        }

        return static::getCase($case) ?? static::fromName((string) $case);
    }

    /**
     * @param  array<int|string|UnitEnum>  $cases
     * @return list<int|string>
     */
    protected static function normalizeToValues(array $cases): array
    {
        static::ensureBackedEnum();

        return array_values(array_map(function (int|string|UnitEnum $case): int|string {
            if ($case instanceof BackedEnum) {
                return $case->value;
            }

            if (is_string($case) || is_int($case)) {
                $resolved = static::getCase($case) ?? static::fromName((string) $case);

                if ($resolved instanceof BackedEnum) {
                    return $resolved->value;
                }

                return $case;
            }

            return $case->name;
        }, $cases));
    }

    protected static function isBackedEnum(): bool
    {
        static $cache = [];

        if (! array_key_exists(static::class, $cache)) {
            if (enum_exists(static::class)) {
                $cache[static::class] = (new \ReflectionEnum(static::class))->isBacked();
            } else {
                // Subset classes are only created from backed enums via generate().
                $cache[static::class] = true;
            }
        }

        return $cache[static::class];
    }

    protected static function ensureBackedEnum(): void
    {
        if (! static::isBackedEnum()) {
            throw new \InvalidArgumentException(sprintf(
                '%s must be a backed enum (string or int). Unit enums are not supported by this method.',
                static::class,
            ));
        }
    }
}

/**
 * Fluent string helper wrapper for enum labels and values.
 */
final class EnumStringable
{
    public function __construct(private readonly string $value) {}

    public function camel(): string
    {
        return Str::camel($this->value);
    }

    public function slug(): string
    {
        return Str::slug($this->value);
    }

    public function snake(): string
    {
        return Str::snake($this->value);
    }

    public function headline(): string
    {
        return Str::headline($this->value);
    }

    public function upper(): string
    {
        return Str::upper($this->value);
    }

    public function lower(): string
    {
        return Str::lower($this->value);
    }

    public function plural(): string
    {
        return Str::plural($this->value);
    }

    public function singular(): string
    {
        return Str::singular($this->value);
    }

    public function title(): string
    {
        return Str::title($this->value);
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * Forward unknown methods to Laravel's Str helper.
     */
    /**
     * @param  array<int, mixed>  $arguments
     */
    public function __call(string $name, array $arguments = []): string
    {
        return Str::$name($this->value, ...$arguments);
    }
}
