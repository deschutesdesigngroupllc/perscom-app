---
paths:
  - 'app/Http/Resources/Api/**'
---

# Resources Api

## Orion API resources
API responses extend App\Http\Resources\Api\ApiResource (an Orion Resource). Reserve response()->json() for non-Orion utility endpoints (health, OIDC, cache).
