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

2. **Start the application:**
   ```bash
   # Laravel Herd
   # Start Laravel Herd following documentation
   
   # Docker
   docker compose up
   ```

3. **Run the setup:**
   ```bash
   composer setup
   ```
   
### Development Commands

See [CLAUDE.md](../CLAUDE.md) for a complete list of available development commands including testing, code quality tools, and database management.

## Contributing

Please see [here](../.github/CONTRIBUTING.md) for more details about contributing.
