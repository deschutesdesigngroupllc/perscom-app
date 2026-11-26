# Config Files

Applies to every file in `config/`.

## Structure

A config file is `<?php`, `declare(strict_types=1);`, optional imports, then `return [`, with no
closing `?>`. It declares its own settings with `env()` defaults and never calls `config()` to read
another config file; shared environment variables are the only link between files.

A package's published config keeps the package's name and shape. A config file the app owns is
named for its domain (`automations.php`, `billing.php`, `ai.php`).

## Keys

- Keys are `snake_case`: singular for a single value (`driver`, `model`), plural for a collection
  (`stores`, `connections`, `updatable_models`).
- A file choosing between named entries follows Laravel's shape: a `'default'` key naming an entry
  in a plural collection, each entry starting with `'driver'`.

## Environment Variables

- `UPPER_SNAKE_CASE`, prefixed by the file's domain (`AI_*` in `ai.php`, `BILLING_*` in
  `billing.php`).
- Give a default for a sensible non-secret value; omit it for a secret or an optional feature.
- Cast a non-string value before `env()`: `(int) env('AI_MONTHLY_TOKEN_LIMIT', 2000000)`,
  `(bool) env('APP_DEBUG', false)`.
- A nested `env()` fallback is fine: `env('MAIL_FROM_NAME', env('APP_NAME', 'PERSCOM'))`.

## Formatting

- Single quotes in code; double quotes only inside banner prose.
- A single space on each side of `=>`, with no alignment.
- A trailing comma on every item, including the last.
- One blank line between top-level keys, and none between a banner's closing `*/` and its key.

## Banners

Document each top-level key or logical group with Laravel's banner comment:

```php
/*
|--------------------------------------------------------------------------
| Updatable Models
|--------------------------------------------------------------------------
|
| The models automations may update, and which of their fields they may set.
| Fields in denied_fields are always blocked.
|
| Supported lookups: "id", "email"
|
*/
```

- The dashed line is exactly 74 hyphens, and the title is Title Case.
- The body is one to three lines, longest first, saying what the setting is for, not how the code
  uses it.
- Supported values follow a blank `|` line, double-quoted and comma-separated.
