---
paths:
  - 'app/Facades/**'
---

# Facades

## Facade-fronted singleton services
Expose singleton services through a custom Facade whose getFacadeAccessor() returns the service ::class, documenting the API with @method annotations.
