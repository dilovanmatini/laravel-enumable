<?php

namespace DilovanMatini\LaravelEnumable\Traits;

use Illuminate\Support\Str;

trait Enumable
{
    /*
    |--------------------------------------------------------------------------
    | Class related methods
    |--------------------------------------------------------------------------
    |
    | These methods are used to manipulate the class itself.
    | Ex: Enum::toArray(); // Output: ['value1' => 'Name1', 'value2' => 'Name2']
    |
    */

    public static function values (): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names (): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all cases and convert them to an array with values as keys and labels as values.
     *
     * Ex: Enum::labels(); // Output: ['value1' => 'Name 1', 'value2' => 'Name 2']
     *
     * @return array
     */
    public static function labels (): array
    {
        $items = [];

        foreach (static::cases() as $case) {
            $items[$case->value] = $case->label();
        }

        return $items;
    }

    /**
     * Get the case by value.
     *
     * Ex: Enum::getCase('value1'); // Output: Enum::Case1
     *
     * @param string $value
     * @return self|null
     */
    public static function getCase ($value): ?self
    {
        foreach (static::cases() as $case) {
            if ($case->value == $value) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Get the name by value.
     *
     * Ex: Enum::getName('value1'); // Output: 'Name1'
     *
     * @param string $value
     * @return string
     */
    public static function getName ($value): string
    {
        return static::getCase($value)?->name ?? '';
    }

    /**
     * Get the label by value.
     *
     * Ex: Enum::getLabel('value1'); // Output: 'Name 1'
     *
     * @param string $value
     * @return string
     */
    public static function getLabel ($value): string
    {
        return static::setLabels()[$value] ?? static::getCase($value)?->headline() ?? '';
    }

    public static function toArray (): array
    {
        return array_combine(self::values(), self::names());
    }

    public static function toCollection (): \Illuminate\Support\Collection
    {
        return collect(self::cases());
    }

    /**
     * Get all cases and convert them to an array with values as keys and labels as values. This method is used for select fields.
     *
     * Ex: Enum::cases(); // Output: ['value1' => 'Name 1', 'value2' => 'Name 2']
     *
     * @return array
     */
    public static function toSelectArray (): array
    {
        return static::labels();
    }

    public static function exists (object|string $value): bool
    {
        if (is_object($value)) {
            $value = $value->value;
        }

        return in_array($value, static::values());
    }

    public static function random (): ?object
    {
        $cases = static::cases();

        if (empty($cases)) {
            return null;
        }

        return $cases[array_rand($cases)];
    }

    /**
     * Get the default case. If default method is not override in the Enum class, it will return the first case.
     *
     * Ex: Enum::default(); // Output: Enum::Case1
     *
     * @return self|null
     */
    public static function default (): ?self
    {
        return static::first();
    }

    public static function first (): ?object
    {
        $cases = static::cases();

        if (empty($cases)) {
            return null;
        }

        return $cases[0];
    }

    public static function last (): ?object
    {
        $cases = static::cases();

        if (empty($cases)) {
            return null;
        }

        return $cases[count($cases) - 1];
    }

    public static function count (): int
    {
        return count(static::cases());
    }

    /**
     * Set the custom labels for the cases. You need to override this method in the Enum class for the cases need to have custom labels.
     *
     * public static function setLabels () {
     *     return [
     *        Enum::Case1->value => 'Custom Label 1',
     *        Enum::Case2->value => 'Custom Label 2',
     *     ];
     * }
     *
     * @return array|null
     */
    public static function setLabels (): ?array
    {
        return null;
    }

    /**
     * Get only the cases by the given values. Then you can apply the methods to the new object.
     *
     * Ex: Enum::only(['value1', 'value2']); // Output: Enum::Case1, Enum::Case2
     *
     * @param array $cases
     * @return object
     */
    public static function only (array $cases): object {
        return static::generate($cases);
    }

    /**
     * Get all cases except the given values. Then you can apply the methods to the new object.
     *
     * Ex: Enum::except(['value1', 'value2']); // Output: Enum::Case3, Enum::Case4
     *
     * @param array $cases
     * @return object
     */
    public static function except (array $cases): object {
        $cases = array_map(function($value) {
            if (is_object($value)) {
                return $value->value;
            }

            return $value;
        }, $cases);

        return static::generate(array_diff(static::values(), $cases));
    }

    /**
     * Generate a new object with the given cases.
     *
     * Ex 1: Enum::generate(['value1', 'value2']); // Output: Enum::Case1, Enum::Case2
     * Ex 2: Enum::generate([Enum::Case1, Enum::Case2]); // Output: Enum::Case1, Enum::Case2
     * Ex 3: Enum::generate(['value1', Enum::Case2]); // Output: Enum::Case1, Enum::Case2
     *
     * @param array $cases
     * @return object
     */
    public static function generate (array $cases): object
    {
        $cases = array_filter($cases, fn($case) => static::exists($case));

        $cases = array_map(function($case) {
            if (is_object($case)) {
                return $case;
            }

            return static::getCase($case);
        }, $cases);

        return new class ($cases) {
            use Enumable;

            public static array $cases;

            public function __construct(array $cases) {
                self::$cases = $cases;
            }

            public static function cases(): array
            {
                return self::$cases;
            }
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Object related methods
    |--------------------------------------------------------------------------
    |
    | These methods are used to manipulate the object itself.
    | Ex: Enum::MyCase->label(); // Output: "My Case"
    |
    */

    /**
     * Get the label of the enum.
     *
     * Ex: Enum::MyCase->label(); // Output: "My Case"
     *
     * @return string
     */
    public function label (): string
    {
        return static::getLabel($this->value);
    }

    /**
     * Get the headline of the enum.
     *
     * Ex: Enum::MyCase->headline(); // Output: "My Case"
     *
     * @return string
     */
    public function headline (): string
    {
        return Str::headline($this->name);
    }

    /**
     * This method is used to apply all the methods from Laravel Str helpers.
     *
     * Ex: Enum::MyCase->str()->slug(); // Output: "my-case"
     *
     * @param bool $label
     * @return object
     */
    public function str (bool $label = false): object
    {
        return new class ($label ? $this->label() : $this->value) {
            private string $value;

            public function __construct(string $value) {
                $this->value = $value;
            }

            public function __call($name, $arguments = []): string
            {
                return Str::$name($this->value, ...$arguments);
            }
        };
    }
}
