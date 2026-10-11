---
paths:
  - 'app/Mail/**'
---

# Mail

## Mailables use queued markdown views
Mailables implement ShouldQueue and render a markdown view at emails.<area>.<kebab-name>, where <area> matches the Mail subfolder (admin, system, tenant, user).
