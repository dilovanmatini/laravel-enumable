# Contributing

Thank you for considering a contribution to [laravel-enumable](https://github.com/dilovanmatini/laravel-enumable).

## Development setup

```bash
composer install
```

## Running checks

```bash
composer test
composer analyse
```

Or both:

```bash
composer test:ci
```

## Pull requests

1. Fork the repository and create a feature branch.
2. Add or update tests for any behavior you change.
3. Ensure `composer test:ci` passes locally.
4. Update [CHANGELOG.md](CHANGELOG.md) under the appropriate version (or the beta section while testing).
5. Open a pull request with a clear description of the problem and solution.

## Coding standards

- Follow existing naming and structure in the codebase.
- Prefer backed enum examples in tests and documentation.
- Keep the package focused: utilities for native PHP enums, not a replacement for them.
