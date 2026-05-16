<p align="center"><img src="https://raw.githubusercontent.com/deschutesdesigngroupllc/perscom-app/refs/heads/master/art/header.png" alt="Logo"></p>

<div align="center">

# PERSCOM Personnel Management System

Mission-critical tools built specifically to meet the unique needs of police, fire, EMS, military, and public safety agencies. Optimize your agency's communications, streamline data management, and improve overall efficiency with PERSCOM.io.

![GitHub Release](https://img.shields.io/github/v/release/deschutesdesigngroupllc/perscom-app?display_name=release)
[![Test Suite](https://github.com/DeschutesDesignGroupLLC/perscom-app/actions/workflows/test.yml/badge.svg)](https://github.com/DeschutesDesignGroupLLC/perscom-app/actions/workflows/test.yml)
![PHPStan](https://img.shields.io/badge/CodeStyle-Laravel-green.svg)
![PHPStan](https://img.shields.io/badge/PHPStan-level%203-yellow.svg)
[![codecov](https://codecov.io/gh/DeschutesDesignGroupLLC/perscom-app/graph/badge.svg?token=uJUiz1Sv6X)](https://codecov.io/gh/DeschutesDesignGroupLLC/perscom-app)
[![Slack](https://img.shields.io/badge/Slack-4A154B?style=flat&logo=slack&logoColor=white)](https://perscom.io/slack)
</div>

## Introduction

PERSCOM.io is a fully functioning, powerful, and robust personnel management software built for para-military organizations. The goal of PERSCOM.io is to enhance and provide common functionalities needed for organizations to run in a manner that is efficient, intuitive, and powerful.

## Getting Started

Head on over to [https://perscom.io/register](https://perscom.io/register) to start a 7-day free trial.

## Self-Hosted Deployment

Want to host PERSCOM yourself? Deploy your own instance to Railway with one click:

[![Deploy on Railway](https://railway.com/button.svg)](https://railway.com/deploy/perscom?referralCode=O-oe8s&utm_medium=integration&utm_source=template&utm_campaign=generic)

For detailed deployment instructions, see the [Railway Deployment Guide](../docs/RAILWAY.md).

## Documentation

Visit our documentation [here](https://docs.perscom.io) to get started.

## Developer Guide

*This section is intended for developers working on the PERSCOM codebase.*

[![Open in GitHub Codespaces](https://github.com/codespaces/badge.svg)](https://github.com/codespaces/new?hide_repo_select=true&ref=master&repo=510520593)

### Prerequisites

- PHP 8.4
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) or [Laravel Herd](https://herd.laravel.com/)
- [Composer](https://getcomposer.org/)

### Installation

The application configuration is set with sensible defaults to get you started. When running `composer setup`, a series of commands will be run that will build and configure the application for local development. Most settings can be configured in your `.env` file. This will be copied from the `.env.example` when running `composer setup` for the first time.

1. **Clone the repository:**
   ```bash
   git clone https://github.com/deschutesdesigngroupllc/perscom-app
   cd perscom-app
   ```

2. **Start the application** using either Laravel Herd or Docker Compose:

   **Option A — Laravel Herd (macOS, recommended):**

   Install [Laravel Herd](https://herd.laravel.com/download) and park the cloned project in a Herd-managed directory. From the project root, run the wizard to provision PHP, services, and domain aliases from the bundled `herd.yml`:

   ```bash
   herd init
   ```

   This installs PHP 8.4, MySQL, and Redis (Herd Pro), and serves the site at `http://perscom.test` with the `app.perscom.test` alias used by tenant subdomain routing.

   Then bootstrap the application:

   ```bash
   composer setup
   ```

   `composer setup` installs dependencies, copies `.env.example` to `.env`, generates application/JWT/Passport keys, builds frontend assets, and runs `php artisan perscom:install` to migrate, seed, and bootstrap the first tenant. When run interactively, the install command will prompt you to choose which type of organization to seed (Military, Fire Service, or Law Enforcement).

   **Option B — Docker Compose (cross-platform):**

   Install [Docker Desktop](https://www.docker.com/products/docker-desktop/) and bring up the stack defined in `compose.yaml` (app, MySQL, Redis, Horizon, scheduler):

   ```bash
   docker compose up -d --build
   ```

   On first start, the app container automatically runs `composer setup` inside the container if it detects a missing `.env`, missing `vendor/`, or unset `APP_KEY`. No additional command is required.

   The app is served at `http://localhost:8080`. Run subsequent `composer`/`artisan`/`npm` commands inside the container with `docker compose exec perscom <command>`.

   > **Note:** `.env.example` defaults to Herd-style URLs (`http://app.perscom.test`, `http://auth.perscom.test`, etc.). After the container generates your `.env`, update `APP_URL`, `AUTH_URL`, `API_URL`, `WIDGET_URL`, and any other URL values to use `http://localhost:8080` (or your chosen `APP_PORT`) so links, redirects, and OAuth callbacks resolve correctly. Service hosts like `DB_HOST` and `REDIS_HOST` are already overridden at runtime by the compose env vars and do not need to be edited.

### Reinstalling or Re-seeding

The `perscom:install` command can be run on its own to (re)install the application:

```bash
# Interactive — you'll be prompted for the organization type
php artisan perscom:install

# Force a reinstall over an existing install (RESETS all data)
php artisan perscom:install --force

# Skip seeding entirely
php artisan perscom:install --no-seed

# Pick the organization template non-interactively
php artisan perscom:install --seeder=military
php artisan perscom:install --seeder=fire
php artisan perscom:install --seeder=law

# Demo environment only — also runs the DemoSeeder
php artisan perscom:install --demo
```

Each organization seeder ships with realistic ranks, qualifications, awards, forms, and other reference data so you have a working dataset to explore immediately.

### Development Commands

See [CLAUDE.md](../CLAUDE.md) for a complete list of available development commands including testing, code quality tools, and database management.

## Premium Plugins

The application supports optional premium Filament plugins that enhance functionality. The application works fully without them.

### Advanced Tables (archilex/filament-filter-sets)

Adds advanced table filtering, preset views, and a favorites bar.

1. **Purchase a license** at [filamentphp.com](https://filamentphp.com/plugins/kenneth-sese-advanced-tables)

2. **Authenticate** with the private Composer repository:
   ```bash
   composer config http-basic.filament-filter-sets.composer.sh your-email@example.com your-license-key
   ```

3. **Install the package:**
   ```bash
   composer require archilex/filament-filter-sets "^4.0.18"
   ```

4. **Rebuild frontend assets:**
   ```bash
   npm run build
   ```

### Data Lens (padmission/data-lens)

Adds custom report building capabilities with scheduling and exports.

1. **Purchase a license** at [filamentphp.com](https://filamentphp.com/plugins/padmission-data-lens)

2. **Authenticate** with the private Composer repository:
   ```bash
   composer config http-basic.data-lens.composer.sh your-email@example.com your-license-key
   ```

3. **Install the package:**
   ```bash
   composer require padmission/data-lens "^2.2.5"
   ```

4. **Rebuild frontend assets:**
   ```bash
   npm run build
   ```

## Contributing

Please see [here](../.github/CONTRIBUTING.md) for more details about contributing.
