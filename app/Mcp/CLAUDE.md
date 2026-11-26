# MCP Server

This directory holds the tenant's read-only Model Context Protocol server: the server definition,
its tools and resources, and `ApiResourceRegistry`, the list of REST API resources MCP clients can
read. The OAuth discovery endpoints that let clients connect live in `app/Http/Controllers/Mcp/`.

## How a Request Flows

1. `routes/mcp.php` registers everything under the tenant subdomain, behind
   `InitializeTenancyBySubdomain` and `PreventAccessFromCentralDomains`. `Mcp::web('mcp', ...)`
   mounts `PerscomServer` at `/mcp`, guarded by `AuthenticateApi` and `throttle:api`.
2. A client without a token gets a 401 whose `WWW-Authenticate` header points at
   `/.well-known/oauth-protected-resource/mcp`. laravel/mcp builds that header from the route
   named `mcp.oauth.protected-resource.nested`, so the app's route must keep that exact name.
3. The client reads `ProtectedResourceMetadataController` (RFC 9728), which names the tenant's own
   host as the authorization server, then `AuthorizationServerMetadataController` (RFC 8414),
   which points at the tenant's Passport authorize and token endpoints, the OIDC JWKS, and
   `oauth/register`.
4. The client registers itself through laravel/mcp's `OAuthRegisterController` (dynamic client
   registration, `throttle:register`, five per minute per IP), runs the authorization code flow
   with PKCE (the metadata advertises `S256` only), and calls `/mcp` with the bearer token.
5. Every tool and resource reads data by calling the REST API in-process through
   `App\Services\InternalApiService`, which forwards the caller's `Authorization` header and the
   tenant header. Nothing in this directory queries a model directly.

The app does not call `Mcp::oauthRoutes()`. `routes/mcp.php` registers its own metadata and
registration routes so they sit behind tenancy middleware and publish the registry's scopes. Both
metadata endpoints accept an optional trailing `{path?}`, so the bare and per-resource discovery
URLs resolve to the same document.

## The API Is the Source of Truth

MCP reads go through the API on purpose: the API's authentication, token scopes, policies,
validation, and serialization apply exactly as they do for any external client, and MCP never
maintains a second permission model. Keep it that way. A new tool that needs data should call
`InternalApiService`, not Eloquent, and return the result through
`Tools\Concerns\RespondsWithApiResults::respondWithApiResult()`, which turns API status codes
(401, 402, 403, 404, 422, and 429) into messages an AI client can act on. Pass the resource's
scope so a 403 names the scope the connection is missing.

`OrganizationStructureResource` and `GetCurrentUserTool` call the API directly (`ranks`, `units`,
`positions`, and `me`) rather than through registry entries. The structure resource drops any
section the token cannot view instead of failing the whole read.

## ApiResourceRegistry

`ApiResourceRegistry::all()` maps each resource key to its API `endpoint`, its Orion
`controller`, a `description` shown to clients, and the `scope` a token needs to read it. The
tools build from it:

| Tool               | Registry calls            | API request              |
|--------------------|---------------------------|--------------------------|
| `list_resources`   | `all()`, `capabilities()` | none                     |
| `search_resource`  | `keys()`, `find()`        | `POST {endpoint}/search` |
| `get_resource`     | `keys()`, `find()`        | `GET {endpoint}/{id}`    |
| `get_current_user` | none                      | `GET me`                 |

`capabilities()` instantiates the Orion controller with `newInstanceWithoutConstructor()` and
reads `filterableBy()`, `sortableBy()`, `searchableBy()`, and `includes()`, so what
`list_resources` advertises can never drift from what the API accepts. Those controller methods
must therefore not depend on constructor state.

`scopes()` is every registry scope plus `mcp:use`. Both metadata controllers publish it as
`scopes_supported`, which is what MCP clients request when they connect. `mcp:use` is not checked
by `/mcp` itself: any valid token reaches the server, and per-resource access is decided by the API
scopes. It exists so `ConnectAiAgentWidget::hasConnectedAiTool()` can detect a linked AI client by
an active token carrying it. It is granted through `ApiPermissionService::scopes()`, which feeds
`Passport::tokensCan()`.

## Security and Tenancy

- The server is read-only. Every tool carries `#[IsReadOnly]`, `#[IsIdempotent]`, and
  `#[IsOpenWorld(false)]`, and `PerscomServerTest` asserts the read-only and open-world hints for
  every tool. Do not add a write tool here; the copilot's propose-then-apply flow in `app/Ai/` is
  the pattern for AI-driven changes.
- The server instructions in `PerscomServer` tell clients that record text is data, never
  instructions. Keep that line when editing them.
- Metadata URLs are built from `request()->getSchemeAndHttpHost()` and named routes, so each
  tenant subdomain advertises only itself as issuer and authorization server.
- `ProtectedResourceMetadataController` names the resource from the tenant, falling back to
  `OrganizationSettings::$name` when tenancy is not initialized.

## Common Changes

**Expose another API resource.** Add an entry to `ApiResourceRegistry::all()` with the Orion
controller and its `view:*` scope. The scope must exist in `config/api.php`, and the API must
register a search route named `api.{endpoint}.search`;
`ListResourcesToolTest::test_every_registered_resource_has_a_search_route_in_the_api` enforces the
route. The new scope automatically appears in both metadata documents.

**Add a tool or resource.** Create it in `Tools/` or `Resources/`, register it in
`PerscomServer::$tools` or `$resources`, mark tools with the three annotations above, and update
the tool name list in `PerscomServerTest::test_every_tool_is_marked_read_only`.

**Tests.** Feature tests live in `tests/Feature/Tenant/Mcp/` and extend `McpTestCase`, which sends
JSON-RPC to `{tenant}/mcp` and offers `callTool()`, `toolJson()`, and `toolError()`. Issue tokens
with the scopes under test via `$this->apiKey([...])`.
