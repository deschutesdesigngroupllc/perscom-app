    # CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Testing
- `composer test` - Run PHPUnit tests without coverage (parallel execution)
- `composer test-coverage` - Run tests with coverage (parallel execution)
- `composer test-filter <pattern>` - Run specific tests by filter pattern
- `composer test-suite` - Run both static analysis and tests

### Code Quality
- `composer cs-fix` - Fix code style using Laravel Pint
- `composer analyze` - Run PHPStan static analysis with 2GB memory limit
- `composer rector` - Apply automated refactoring with Rector

### Development Workflow
- `composer dev` - Start concurrent development services (Horizon, Pail logs, Vite)
- `composer reset` - Full reset: install dependencies, build assets, fresh migration with seeding
- `composer ide` - Generate IDE helper files for better autocomplete

### Frontend
- `npm run dev` - Start Vite development server
- `npm run build` - Build production assets
- `npm run format` - Format Blade, JS, and JSON files with Prettier

### Database & Tenancy
- `php artisan tenants:run --tenants=1 <command>` - Run artisan commands on specific tenant
- `composer shield:seeder` - Reseed permissions and roles for tenant 1

## Architecture Overview

This is a **multi-tenant Laravel application** using the Stancl/Tenancy package for tenant isolation. The application serves as a military/organization management system with role-based permissions.

### Key Architectural Patterns

**Multi-Tenancy**
- Each tenant gets its own database with `tenant_<id>_<env>` naming
- Tenancy is initialized by subdomain: `{tenant}.domain.com`
- Central domain hosts the main landing pages and tenant management
- Tenant-specific middleware handles context switching automatically

**Domain-Driven Structure**
- Models are organized by domain (Users, Awards, Records, etc.)
- Extensive use of Eloquent relationships and scopes
- Custom field data system for flexible entity attributes
- Audit logging via Spatie ActivityLog

**API-First Design**
- RESTful API using Laravel Orion for automatic CRUD endpoints
- JWT authentication via php-open-source-saver/jwt-auth
- API versioning and comprehensive logging
- Rate limiting and permission-based access control

**Queue-Based Processing**
- Laravel Horizon for queue management
- Background jobs for tenant setup/teardown, notifications, and maintenance
- System queue for critical tenant operations

### Key Components

**User Management**
- Role-based permissions using Spatie Laravel Permission
- User approval workflows and online status tracking
- Social authentication integration

**Records System**
- Assignment, Award, Combat, Service, and Training records
- Qualification and competency tracking
- Document management with categories and tags

**Communication**
- Internal messaging system with notifications
- Discord and Twilio integrations for external notifications
- Mass email capabilities

**Scheduling & Events**
- Calendar system with event management
- Recurring schedules with RRule support
- Event registration and attendance tracking

## Important Notes

- Always use `php artisan tenants:run` when working with tenant-specific data
- The application uses strict typing (`declare(strict_types=1)`)
- Code style follows Laravel Pint rules with custom configurations
- All database changes require both central and tenant migrations
- API requests require proper JWT tokens and permissions
- Test database uses MySQL with `testing` database name
