# Changelog

All notable changes to `laravel-enumable` will be documented in this file.

## [1.1.0] - TBD

Stable release after beta feedback. See [1.1.0-beta.1](#110-beta1---2026-05-23) below.

## [1.1.0-beta.1] - 2026-05-23

### Added

- Validation helpers: `rule()`, `ruleOnly()`, `ruleExcept()`
- Comparison helpers: `is()`, `isAny()`, `in()`
- Lookup helpers: `tryFromValue()`, `fromName()`, `fromNameOrDefault()`
- `toSelectArrayByName()` for name-keyed select options
- `labelsMap()` as the preferred override for custom labels (`setLabels()` remains supported)
- `trans()` for translation keys
- Explicit `EnumStringable` helpers on `str()` (camel, slug, snake, headline, upper, lower, plural, singular, title) with `__call` for other `Str` methods
- GitHub Actions CI and PHPStan configuration
- Expanded test coverage
- [UPGRADE.md](UPGRADE.md) for beta testers and stable upgrades

### Changed

- `getCase()` uses native `tryFrom()` with strict value matching for backed enums
- `getLabel()` safely handles enums without custom labels
- `except()` and `generate()` always return zero-indexed case lists
- `only()` accepts enum instances, names, and values consistently with `except()`
- Subset generation uses isolated classes per call (fixes shared-state bug) while keeping 1.0.x static API compatibility
- Package is now zero-config (no service provider required)
- Added explicit `illuminate/*` dependencies

### Removed

- Empty `EnumableServiceProvider` and `spatie/laravel-package-tools` dependency

### Fixed

- Subset generation no longer shares mutable static state between calls
- README examples and method documentation corrected

### Upgrade notes

See [UPGRADE.md](UPGRADE.md). **All existing trait method names are unchanged.** Subset methods (`only`, `except`, `generate`) continue to support static calls such as `YourEnum::only([...])::labels()`.

## [1.0.2] - 2024-08-10

- Maintenance release

## [1.0.1] - 2024-08-10

- Initial public releases

## [1.0.0] - 2024-08-10

- Initial release
