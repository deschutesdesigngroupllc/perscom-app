---
paths:
  - 'app/**'
---

# App

## No repository layer
Query models directly through Eloquent. Do not introduce repository or query-object classes (the sole app/Repositories class is tenancy infrastructure, not a domain repository).

## Localize with English string keys
Localize with full English sentences/labels as the key inside __(). Do not introduce short dot-notation keys or per-group PHP lang files.

## Date construction via now()/today() helpers
Construct current-time values with now()/today() helpers rather than Carbon:: static constructors.

## Collection pipelines over imperative loops
Transform data with collect()/->map()/->filter()/->each() chains rather than foreach or array_map.

## Use filled()/blank() for emptiness checks
Check emptiness with the filled()/blank() helpers, not === '' / !== '' comparisons or empty(). Applies to strings, arrays, and nullable values.
