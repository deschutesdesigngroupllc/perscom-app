---
paths:
  - 'resources/js/**'
---

# Js

## Frontend stack split
Build admin/app UI with Filament + Livewire. Reserve Inertia + React for the public landing/registration flow, the Passport authorize view, and the standalone editor page.

## React is plain JSX with shadcn/ui
Build React UI as plain JSX (no TypeScript) using shadcn-style primitives: cva variants, the cn() helper, and Radix components under components/ui.

## Ziggy route() and useForm in Inertia pages
Reference Laravel routes with Ziggy's global route('name', params). Submit forms with Inertia useForm and post(route(...)) from an onSubmit handler, not <Form> or Wayfinder.
