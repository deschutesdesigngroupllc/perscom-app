<p align="center"><img src="art/header.png" alt="Logo"></p>

<div align="center">

# PERSCOM Personnel Management System

Mission-critical tools built specifically to meet the unique needs of police, fire, EMS, military, and public safety agencies. Optimize your agency's communications, streamline data management, and improve overall efficiency with PERSCOM.io.

[![Test Suite](https://github.com/DeschutesDesignGroupLLC/perscom-app/actions/workflows/test.yml/badge.svg)](https://github.com/DeschutesDesignGroupLLC/perscom-app/actions/workflows/test.yml)
[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2Fb1590353-0af9-46fb-bde5-aba13e3c4fd9&style=plastic)](https://forge.laravel.com/servers/693345/sites/2017011)

[Documentation](https://docs.perscom.io)

</div>

## Introduction

PERSCOM.io is a fully functioning, powerful, and robust personnel management software built for para-military organizations. The goal of PERSCOM.io is to enhance and provide common functionalities needed for organizations to run in a manner that is efficient, intuitive, and powerful.

## Getting Started

Head on over to [https://perscom.io/register](https://perscom.io/register) to start a 7-day free trial.

## Documentation

Visit our documentation [here](https://docs.perscom.io) to get started.

---

## 🏗️ Developer Guide

*This section is intended for developers working on the PERSCOM codebase.*

### Architecture Overview

**Multi-Tenancy**
- **Tenant Isolation**: Each organization gets its own database using `tenant_{id}_{env}` naming convention
- **Subdomain Routing**: Tenants accessed via `{tenant}.domain.com` structure
- **Central Management**: Admin interface for tenant management and global operations
- **Automatic Context Switching**: Middleware handles tenant identification and context switching

**Tech Stack**
- **Backend**: Laravel 11.x with PHP 8.3+
- **Database**: MySQL with multi-tenant architecture
- **Admin Panel**: Filament 3.x for administrative interfaces
- **API**: Laravel Orion for RESTful endpoints with automatic CRUD generation
- **Authentication**: Self-signed JWTs via php-open-source-saver/jwt-auth + Laravel Passport for OAuth/App-signed JWTs
- **Queue System**: Laravel Horizon with Redis for background job processing
- **Frontend**: Vite + React for modern asset compilation and interactive components

**Key Features**
- **Role-Based Access Control**: Comprehensive permissions system using Spatie Laravel Permission
- **Records Management**: Awards, qualifications, training, combat, and service records
- **Communication System**: Internal messaging with multi-channel notifications (Discord, Twilio, Email)
- **Event Management**: Calendar system with recurring schedules and registration
- **Document Management**: File attachments with categorization and tagging
- **API-First Design**: Comprehensive REST API with versioning and rate limiting

### Development Setup

**Prerequisites**
- PHP 8.3+
- Composer
- Node.js 18+ & npm
- MySQL 8.0+
- Redis (for queues and caching)

**Installation**

1. **Clone and install dependencies**
   ```bash
   git clone <repository-url>
   cd perscom-app
   composer install
   npm install
   ```

2. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan jwt:secret
   ```

3. **Configure your `.env` file**
   ```bash
   # Database
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=perscom_central
   DB_USERNAME=root
   DB_PASSWORD=

   # App domains
   APP_URL=http://perscom-app.test
   BASE_URL=.perscom-app.test
   API_URL=http://api.perscom-app.test
   AUTH_URL=http://auth.perscom-app.test

   # Queue system
   QUEUE_CONNECTION=redis
   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379
   ```

4. **Complete setup**
   ```bash
   composer reset  # Full reset: migrations, seeding, assets, IDE helpers
   ```

### Development Workflow

**Daily Development**
```bash
composer dev  # Start Horizon + Pail logs + Vite dev server
```

**Code Quality & Testing**
```bash
composer test              # Run all tests (parallel)
composer test-coverage     # Run tests with coverage
composer test-filter User  # Run specific test pattern
composer cs-fix            # Fix code style with Laravel Pint
composer analyze           # Run PHPStan static analysis
composer rector            # Apply automated refactoring
composer test-suite        # Static analysis + tests
```

**Frontend Development**
```bash
npm run dev          # Start Vite development server
npm run build        # Build production assets
npm run format       # Format Blade/JS/JSON files
```

### Database & Multi-Tenancy

**Tenant Management**
```bash
php artisan tenants:list
php artisan tenants:create {domain}
php artisan tenants:run --tenants=1 migrate
php artisan tenants:run --tenants=1,2,3 queue:work
php artisan tenants:run cache:clear  # All tenants
```

**Migrations**
- **Central migrations**: `/database/migrations/` - for global/central database
- **Tenant migrations**: `/database/migrations/tenant/` - for tenant-specific databases

**Seeders**
```bash
php artisan db:seed                   # Central database
php artisan tenants:seed              # All tenants
composer shield:seeder                # Reseed permissions for tenant 1
```

### API Documentation

**Base URL**: `{api_domain}/{version}` (Current: `v2`)
**Authentication**: JWT Bearer tokens

**Key Resources**
- `users` - Personnel management
- `awards` / `award-records` - Award system
- `qualifications` / `qualification-records` - Qualification tracking
- `ranks` / `rank-records` - Rank progression
- `units` / `positions` / `specialties` - Organizational structure
- `training-records` - Training management
- `events` / `calendars` - Event scheduling
- `documents` / `forms` / `submissions` - Document management
- `messages` - Internal communication

**API Features**
- Rate limiting and permission-based access
- Comprehensive logging and response caching
- Standardized error responses with request tracing

### Testing

**Test Structure**
```
tests/
├── Architecture/    # Architectural tests
├── Contracts/       # Test contracts
├── Feature/         # Feature tests
│   ├── Central/     # Central app tests
│   ├── Console/     # Console tests
│   └── Tenant/      # Tenant-specific tests
├── Traits/          # Testing helpers
└── Unit/            # Unit tests
```

**Writing Tests**
- Extend `ApiResourceTestCase` for API testing
- Use `TenantTestCase` for tenant-specific tests
- Follow existing patterns for consistency

### Maintenance & Operations

**Background Jobs**
- Monitor via Horizon dashboard: `{app_url}/admin/horizon`
- Queues: `default`, `tenant`, `system`, `api`, `backup`, `clean`

**Custom Commands**
```bash
php artisan perscom:reset                 # Reset local environment back to default
php artisan perscom:prune                 # Clean old data
php artisan perscom:optimize              # Optimize databases
php artisan perscom:backup                # Manual backup
php artisan perscom:backup-clean          # Clean old backups
php artisan perscom:calculate-schedules   # Update recurring events
php artisan perscom:recurring-messages    # Schedule upcoming messages
php artisan perscom:event-notifications   # Schedule upcoming event notifications
```

**Development Tools**
- **Laravel Horizon**: Queue monitoring at `{app_url}/admin/horizon`
- **Laravel Telescope**: Debugging at `{app_url}/admin/telescope` (local only)
- **API Specification**: Available at `{app_url}/api/spec.json`
- **Health Monitoring**: System health checks at `{app_url}/up`

### Contributing

1. Follow PSR-12/Laravel coding standards
2. Write tests for new features
3. Use strict type declarations
4. Follow existing architectural patterns
5. Update documentation for API changes

---

**Built with ❤️ using Laravel, Filament, React, Livewire and modern web technologies.**
