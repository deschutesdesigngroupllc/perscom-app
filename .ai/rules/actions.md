---
paths:
  - 'app/Actions/**'
---

# Actions

## Actions have one static handle()
Give each Action a single public static handle() entry point. Keep multi-method domain logic in app/Services instead.

## Configurable actions are instance classes
When an action needs options, make handle() an instance method with fluent should*(bool): static toggles and obtain it via resolve(). DispatchTenantJob keeps its static for() entry point.
