---
paths:
  - 'tests/Feature/**/Jobs/**'
---

# Feature Jobs

## Stub Artisan for command-wrapping jobs
For jobs that call an Artisan command, assert the exact command and options with Artisan::shouldReceive('call')->once()->with('cmd', [...])->andReturn(0) instead of running it.
