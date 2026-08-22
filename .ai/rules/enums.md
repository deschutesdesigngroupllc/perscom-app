---
paths:
  - 'app/Models/Enums/**'
---

# Enums

## Backed enums in app/Models/Enums
Place enums in app/Models/Enums. Make them backed (: string by default, : int only when semantically numeric) and name cases in UPPER_SNAKE_CASE. Implement Filament contracts (HasLabel/HasColor) with match-based methods where the enum drives UI.
