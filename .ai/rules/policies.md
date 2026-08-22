---
paths:
  - 'app/Policies/**'
---

# Policies

## Policies delegate to permission strings
Put authorization in a per-model Policy class; each ability method returns $authUser->can('<ability>_<model>') against Spatie permissions. Reserve Gate::define for framework dashboards (Horizon/Pulse/Telescope).
