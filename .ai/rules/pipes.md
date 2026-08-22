---
paths:
  - 'app/Pipes/**'
---

# Pipes

## Invokable pipeline stages
Implement pipeline stages as classes with __invoke($payload, Closure $next) returning $next(...).
