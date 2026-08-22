---
paths:
  - 'app/Http/Requests/**'
---

# Requests

## Validate via Request classes only
Put all validation in dedicated Request classes. Never use inline $request->validate() or Validator::make() in controllers.
