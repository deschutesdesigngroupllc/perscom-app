---
paths:
  - 'app/Http/Controllers/Api/**'
---

# Controllers Api

## Orion resource controllers
Build API controllers as Orion resource controllers extending Orion\Http\Controllers\Controller with $model and $request properties. Expose behavior via includes(), sortableBy(), searchableBy(), filterableBy() and before*/after* hooks.

## API authorization via AuthorizesRequests trait
Orion API controllers use the AuthorizesRequests trait, whose authorize() routes through ApiPermissionService. Do not call $this->authorize(), Gate::authorize, can middleware, #[Authorize], or @can in Blade.
