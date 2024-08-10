# Enumable Trait for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dilovanmatini/laravel-enumable.svg?style=flat-square)](https://packagist.org/packages/dilovanmatini/laravel-enumable)

## Overview

The Laravel `Enumable` trait provides a set of utility methods to enhance PHP enums in Laravel. It includes methods for manipulating enum cases, retrieving labels, and converting enums to various formats.

## Installation

To install the Laravel `Enumable` trait, add it to your project via Composer:

```bash
composer require dilovanmatini/laravel-enumable
```

## Requirements

- PHP 8.1 or higher
- Laravel 10.0 or higher

## Usage

Include the `Enumable` trait in your enum classes:

```php
use DilovanMatini\Enumable\Traits\Enumable;

enum YourEnum: string
{
    use Enumable;

    case Example = 'example';
    // Add your cases here
}
```

### Methods

#### Class Methods

- `values()`: Returns an array of all enum values.
- `names()`: Returns an array of all enum names.
- `labels()`: Converts the enum to an associative array with values as keys and labels as values.
- `getCase(string $value)`: Returns the enum case for the given value.
- `getName(string $value)`: Returns the name of the enum case for the given value.
- `getLabel(string $value)`: Returns the label of the enum case for the given value.
- `toArray()`: Converts the enum to an associative array with values as keys and names as values.
- `toCollection()`: Converts the enum to a Laravel collection.
- `toSelectArray()`: Alias for toLabelsArray().
- `exists(object|string $value)`: Checks if the given value exists in the enum.
- `random()`: Returns a random enum case.
- `default()`: Returns the default enum case.
- `first()`: Returns the first enum case.
- `last()`: Returns the last enum case.
- `count()`: Returns the number of enum cases.
- `only(array $cases)`: Returns a new object with only the specified cases.
- `except(array $cases)`: Returns a new object with all cases except the specified ones.
- `generate(array $cases)`: Generates a new object with the given cases.

#### Instance (Object) Methods

- `label()`: Returns the label of the enum case.
- `headline()`: Returns the headline of the enum case.
- `str(bool $label = false)`: Returns a new object to apply Laravel string helper methods.

### Examples

```php
// Class methods
YourEnum::values(); // ['example']
YourEnum::names(); // ['Example']
YourEnum::labels(); // ['example' => 'Example']
YourEnum::getCase('example'); // YourEnum::Example
YourEnum::getName('example'); // 'Example'
YourEnum::getLabel('example'); // 'Example'
YourEnum::toArray(); // ['example' => 'Example']
YourEnum::toCollection(); // Collection {#1 ▼
    #items: App\Enums\YourEnum [▼
        "name" => "Example",
        "value" => "example",
    ]
}
YourEnum::toSelectArray(); // ['example' => 'Example']
YourEnum::exists('example'); // true
YourEnum::random(); // YourEnum::Example
YourEnum::default(); // YourEnum::Example
YourEnum::first(); // YourEnum::Example
YourEnum::last(); // YourEnum::Example
YourEnum::count(); // 1
YourEnum::only(['Example']); // YourEnum::Example
YourEnum::except(['Example']); // YourEnum::Example
YourEnum::generate(['Example']); // YourEnum::Example

// Instance methods
YourEnum::Example->label(); // 'Example'
YourEnum::Example->headline(); // 'Example'
YourEnum::Example->str()->camel(); // 'example'
YourEnum::Example->str()->plural(); // 'Examples'
```

### Custom Labels

Override the `setLabels` method in your enum class to provide custom labels:

```php
public static function setLabels(): array
{
    return [
        self::Example->value => 'Example Custom Label',
    ];
}
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
