---
paths:
  - 'app/Http/Requests/Api/**'
---

# Api

## Orion API request structure
API request classes extend Orion\Http\Requests\Request and split rules across commonRules() plus storeRules()/updateRules(), not a single rules() method.
