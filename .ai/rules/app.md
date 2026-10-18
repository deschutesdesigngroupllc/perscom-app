---
paths:
  - 'app/**'
---

# App

## No repository layer
Query models directly through Eloquent. Do not introduce repository or query-object classes (the sole app/Repositories class is tenancy infrastructure, not a domain repository).

## Plain English string literals
Write UI strings (labels, helper text, notifications) as plain English literals. Do not wrap them in __() or introduce lang files.

## Date construction via now()/today() helpers
Construct current-time values with now()/today() helpers rather than Carbon:: static constructors.

## Collection pipelines over imperative loops
Transform data with collect()/->map()/->filter()/->each() chains rather than foreach or array_map.

## Use filled()/blank() for emptiness checks
Check emptiness with the filled()/blank() helpers, not === '' / !== '' comparisons or empty(). Applies to strings, arrays, and nullable values.

## Use resolve() for service location
When a dependency can't be injected (static Filament methods, closures, notifications, settings classes), obtain it with resolve(Foo::class). Do not use app(), app()->make(), or App::make().

## Facades for Auth/Cache/Session/Log; helpers for config/request/event/redirect
Use the Auth::, Cache::, Session::, and Log:: facades, never auth(), cache(), session(), or logger(). Use the config(), request(), event(), and redirect() helpers rather than their facades.
