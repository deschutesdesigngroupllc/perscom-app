---
paths:
  - 'tests/**'
---

# Tests

## Write tests as PHPUnit classes
Write tests as classes extending a project TestCase with test_snake_case methods. Do not use Pest's it()/test()/expect() functional style.

## Build fixtures with factories
Create test data with model factories (prefer createQuietly() when observers/events should not fire). Do not hand-insert rows or call $this->seed().
