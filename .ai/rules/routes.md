---
paths:
  - 'routes/**'
---

# Routes

## Route handler and middleware style
Register API routes with Orion::resource() helpers and web routes with controller-class references ([Controller::class, 'method'] or invokable class); avoid closure handlers except trivial redirects/fallbacks. Assign middleware in route files via group arrays or ->middleware(), never HasMiddleware or #[Middleware] in controllers.

## Named rate limiters
Define limiters with RateLimiter::for() in RouteServiceProvider and apply them by name (throttle:name). Do not use inline throttle:max,minutes.
