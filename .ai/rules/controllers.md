---
paths:
  - 'app/Http/Controllers/**'
---

# Controllers

## Keep controllers thin
Keep controllers thin. Delegate work to Orion hooks and Request classes for the API, and to invokable Action classes for web flows, rather than embedding business logic.

## Build URLs from named routes
Reference routes by name via route('name'). Do not use url('/path') or action([...]).
