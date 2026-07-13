# Enumable Trait for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dilovanmatini/laravel-enumable.svg?style=flat-square)](https://packagist.org/packages/dilovanmatini/laravel-enumable)
[![Total Downloads](https://img.shields.io/packagist/dt/dilovanmatini/laravel-enumable.svg?style=flat-square)](https://packagist.org/packages/dilovanmatini/laravel-enumable)
[![License](https://img.shields.io/packagist/l/dilovanmatini/laravel-enumable.svg?style=flat-square)](https://packagist.org/packages/dilovanmatini/laravel-enumable)
[![PHP 8.1+](https://img.shields.io/badge/PHP-8.3%2B-blue.svg)](https://php.net)
[![Laravel 10|11|12|13](https://img.shields.io/badge/Laravel-10%20%7C%2011%20%7C%2012%20%7C%2013-red.svg)](https://laravel.com)

Native PHP enums with Laravel-friendly helpers for labels, selects, validation, and case filtering.

## Overview

The `Enumable` trait enhances **backed** PHP enums (`string` or `int`) with utilities for forms, APIs, and validation—without replacing native enums.

## Installation

```bash
composer require dilovanmatini/laravel-enumable
```

No service provider or configuration is required.

Upgrading from 1.0.x? See [UPGRADE.md](UPGRADE.md).

## Requirements

- PHP 8.1 or higher (Laravel 13 applications require PHP 8.3+)
- Laravel 10, 11, 12, or 13

## Usage

```php
use DilovanMatini\Enumable\Traits\Enumable;

enum Status: string
{
    use Enumable;

    case Draft = 'draft';
    case Published = 'published';
}
```

## Methods

### Class methods

| Method | Description |
|--------|-------------|
| `values()` | All backed values |
| `names()` | All case names |
| `labels()` | `[value => label]` |
| `toSelectArray()` | Alias of `labels()` |
| `toSelectArrayByName()` | `[name => label]` |
| `toArray()` | `[value => name]` |
| `toCollection()` | Laravel collection of cases |
| `getCase($value)` | Resolve by value (`tryFrom` for backed enums) |
| `tryFromValue($value)` | Alias of `getCase()` |
| `fromName($name)` | Resolve by case name |
| `fromNameOrDefault($name)` | Resolve by name or fall back to `default()` |
| `getName($value)` | Case name for a value |
| `getLabel($value)` | Label for a value |
| `exists($value)` | Whether value, name, or case exists |
| `random()` | Random case |
| `default()` | Default case (override in your enum) |
| `first()` / `last()` | First or last case |
| `count()` | Number of cases |
| `only($cases)` | Filtered subset (full Enumable API) |
| `except($cases)` | Inverse filtered subset |
| `generate($cases)` | Build a filtered subset |
| `rule()` | Laravel `Rule::enum()` |
| `ruleOnly($cases)` | `Rule::in()` for a subset |
| `ruleExcept($cases)` | `Rule::in()` excluding cases |

### Instance methods

| Method | Description |
|--------|-------------|
| `label()` | Human-readable label |
| `headline()` | Headline from case name |
| `trans($key, $replace, $locale)` | Translation helper |
| `is($other)` | Compare to case, value, or name |
| `isAny($others)` | True if any match |
| `in($others)` | Alias of `isAny()` |
| `str($label = false)` | String helpers (`camel`, `slug`, etc.) |

## Examples

```php
// Lists & selects
Status::values();           // ['draft', 'published']
Status::labels();           // ['draft' => 'Draft', 'published' => 'Published']
Status::toSelectArray();    // same as labels()

// Resolution
Status::getCase('draft');   // Status::Draft
Status::fromName('Draft');  // Status::Draft

// Validation (Form Request)
Status::rule();
Status::ruleOnly(['draft', 'published']);

// Filtering (subset supports static and instance calls, same as 1.0.x)
Status::except(['draft'])::labels();
Status::only([Status::Published])->cases();

// Instance helpers
Status::Draft->label();
Status::Draft->str()->slug();       // "draft"
Status::Draft->is('draft');         // true
```

### Custom labels

Prefer `labelsMap()` (or keep using `setLabels()` for backward compatibility):

```php
public static function labelsMap(): array
{
    return [
        self::Draft->value => 'Draft (hidden)',
        self::Published->value => 'Live',
    ];
}
```

### Default case

Override `default()` on your enum:

```php
public static function default(): self
{
    return self::Draft;
}
```

### Blade select

```blade
<select name="status">
    @foreach (Status::toSelectArray() as $value => $label)
        <option value="{{ $value }}">{{ $label }}</option>
    @endforeach
</select>
```

### Eloquent

Use Laravel's native enum casting:

```php
protected $casts = [
    'status' => Status::class,
];
```

## Backed enums only

Methods that rely on a backed `value` (`values()`, `labels()`, `only()`, etc.) require `string` or `int` backed enums. Unit enums support name-based helpers such as `fromName()` and `exists()` by name.

## Migrating from BenSampo/laravel-enum

| BenSampo | Enumable |
|----------|----------|
| `getValue()` | `$case->value` |
| `getDescription()` | `$case->label()` |
| `toSelectArray()` | `toSelectArray()` or `labels()` |
| `coerce()` | `tryFromValue()` or `getCase()` |

## Comparison with Other Enum Packages

| Feature / Package                                    | Laravel Enumable (this package)                        | BenSampo\\Enum                                     | Native PHP Enums                   |
|------------------------------------------------------|--------------------------------------------------------|----------------------------------------------------|------------------------------------|
| **Laravel Version Support**                          | 9.x, 10.x, 11.x, 12.x, 13.x (with `illuminate/*` deps) | 8.x, 9.x, 10.x, 11.x                               | Laravel 9+ (for PHP 8.1+ enums)    |
| **Enum Backing**                                     | Native PHP 8.1+ enums                                  | Class-based emulation pre-8.1, native post-8.1     | Native                              |
| **Get All Cases/Values/Names**                       | ✔️ cases(), names(), values()                          | ✔️ getValues(), getNames(), getInstances()          | ✔️ cases()                         |
| **Labeling/Custom Labels**                           | ✔️ labels(), setLabels(), labelsMap() (+translate)     | ✔️ description, custom labels via method            | Manual implementation               |
| **Array/Map Conversion**                             | ✔️ toArray(), toSelectArray(), toSelectArrayByName()   | ✔️ asArray(), asSelectArray()                       | Manual (array_map/cases)            |
| **Validation Rules**                                 | ✔️ rule(), ruleOnly(), ruleExcept(), integrate w/Validator | ✔️ validationRule()                            | Manual                             |
| **Lookup Helpers**                                   | ✔️ tryFromValue(), fromName(), fromNameOrDefault()     | ✔️ coerce(), fromKey(), fromValue(), etc.           | Native tryFrom()/from() (limited)   |
| **Comparison & In Checks**                           | ✔️ is(), isAny(), in()                                 | ✔️ isOneOf()                                       | Manual or custom                    |
| **String Helpers**                                   | ✔️ str(), slug(), snake(), camel(), etc. via EnumStringable | ❌                                            | ❌                                  |
| **Translation Support**                              | ✔️ trans(), translation key helpers                    | ❌                                                | Manual                              |
| **Subset/Filtering**                                 | ✔️ only(), except(), generate()                        | ✔️ filter()                                        | Manual                              |
| **Macroable/Extendability**                          | ❌ (not macroable, but adds helpers)                   | ✔️ Macroable (extend enum classes)                  | Manual                              |
| **Test/Type Coverage**                               | ✔️ (PHPStan level 8, extensive tests)                  | Good                                              | N/A                                 |
| **Zero Config (out-of-the-box)**                     | ✔️                                                    | ❌ (service provider if using Laravel integration)   | ✔️ (once enum is defined)           |
| **Extra Features**                                   | EnumStringable (String inflection), advanced labels    | Enum annotations, custom casting, traits           | N/A                                 |

**Legend**: ✔️ = Supported; ❌ = Not Supported; Manual = Can be achieved but requires additional code.  
_Last reviewed: Laravel Enumable v1.1.0, BenSampo\\Enum v5.x, PHP 8.1+ enums._

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for release notes.

## License

MIT © [Dilovan Matini](https://github.com/dilovanmatini)
