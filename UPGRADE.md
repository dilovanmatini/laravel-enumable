# Upgrade guide

## Testing 1.1.0-beta

Install the beta explicitly (stable `1.0.2` remains the default on Packagist):

```bash
composer require dilovanmatini/laravel-enumable:1.1.0-beta.1
```

Report issues on GitHub with the **1.1.0-beta** label. Include your PHP version, Laravel version, and a minimal code sample.

---

## Upgrading from 1.0.x to 1.1.x

### No method renames

All trait method names from `1.0.x` are unchanged. You do not need to search-and-replace method names.

### `only()`, `except()`, and `generate()` return value

These methods still return an `object` that uses the **full `Enumable` API**, including static calls:

```php
// Still supported (1.0.x and 1.1.x)
Status::only(['draft', 'published'])::labels();
Status::only(['draft'])->cases();

// Instance-style calls also work
Status::except(['draft'])->labels();
```

**What changed internally:** each subset is an isolated class with its own case list (fixes a bug where multiple subsets could overwrite each other in long-running apps).

### Stricter value matching

`getCase()` and `exists()` use strict matching for backed enums (`tryFrom()` / `in_array(..., true)`).

If you relied on loose coercion (e.g. `'1'` matching `1` on an int-backed enum), use the correct type or cast before calling these methods.

### `first()`, `last()`, `random()`, `default()`

Return types are non-nullable `static` (PHP enums always have at least one case). Remove dead `null` checks if you had them.

### Custom labels

`setLabels()` still works. You may optionally switch to `labelsMap()` — same purpose, clearer name.

### Service provider removed

The package is zero-config. Remove `DilovanMatini\Enumable\EnumableServiceProvider` from your app config if you registered it manually. No replacement is required.

### New optional helpers

These are additive; existing code does not need to use them:

- `rule()`, `ruleOnly()`, `ruleExcept()`
- `tryFromValue()`, `fromName()`, `fromNameOrDefault()`
- `is()`, `isAny()`, `in()`
- `toSelectArrayByName()`, `labelsMap()`, `trans()`

### Requirements

- PHP 8.1+ (Laravel 13 requires PHP 8.3+)
- Laravel 10, 11, 12, or 13
- `illuminate/support`, `illuminate/collections`, and `illuminate/validation` are now explicit dependencies (pulled in automatically via Composer)
