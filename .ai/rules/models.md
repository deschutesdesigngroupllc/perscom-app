---
paths:
  - 'app/Models/**'
---

# Models

## Allow-list mass assignment
Declare a protected $fillable allow-list on models. Do not use $guarded.

## Query scopes: global via #[ScopedBy], local via scopeXxx
Register global scopes with the #[ScopedBy] attribute pointing to a dedicated class in app/Models/Scopes. Write query-specific local scopes as scopeXxx() methods (no #[Scope] attribute).

## Observers via #[ObservedBy]
Register model observers with the #[ObservedBy(...)] attribute on the model. Do not call Model::observe() in a provider.

## Attribute-class accessors and mutators
Define accessors and mutators as methods returning an Attribute instance, not legacy getXxxAttribute()/setXxxAttribute() methods.

## Compose model behavior via concern traits
Extract reusable model behavior into App\Traits concern traits (Has*/CanBe*/Clears*) and compose them onto models.

## Enums as string columns with PHP-enum casts
Store enums as string() columns and cast to an App\Models\Enums backed enum on the model. Do not use DB enum() columns.
