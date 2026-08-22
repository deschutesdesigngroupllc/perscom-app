---
paths:
  - 'tests/Feature/Tenant/Http/Controllers/Api/**'
---

# Http Controllers Api

## Contract-driven API tests
Extend ApiResourceTestCase and implement ApiResourceTestContract (endpoint/model/factory/storeData/updateData/scopes) rather than writing CRUD assertions by hand. Assert responses with assertJsonStructure/assertJsonPath (not the fluent AssertableJson API). Authenticate with withToken($this->apiKey()).
