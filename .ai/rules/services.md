---
paths:
  - 'app/Services/**'
---

# Services

## Stateless services expose static methods
Write stateless service logic as public static methods called as XService::method(). For services that hold state or configuration, obtain an instance with resolve(XService::class) plus an @var docblock rather than constructor injection.
