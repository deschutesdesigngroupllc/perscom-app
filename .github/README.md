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

### Prerequisites

- PHP 8.4
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) or [Laravel Herd](https://herd.laravel.com/)
- [Composer](https://getcomposer.org/)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/deschutesdesigngroupllc/perscom-app
   cd perscom
   ```

2. **Run the setup:**
   ```bash
   composer setup
   ```

3. **Start the application:**
   ```bash
   docker compose up
   ```

The application will be available at the URL specified in your `.env` file.

### Development Commands

See [CLAUDE.md](../CLAUDE.md) for a complete list of available development commands including testing, code quality tools, and database management.
