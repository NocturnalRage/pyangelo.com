# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Testing
```bash
vendor/bin/phpunit                                    # All PHP tests
vendor/bin/phpunit tests/src/PyAngelo/Controllers/   # Single directory
vendor/bin/phpunit tests/di/ServicesTest.php          # Single file
npm run testjs                                        # All JavaScript tests
npm run testjs -- Autocompleter.test.js               # Single JS test file
npm test                                              # Both PHP and JS tests
```

### Frontend
```bash
npm run dev    # Development build
npm run watch  # Watch mode
npm run prod   # Production build (minified)
```

## Architecture

**Custom PHP framework** (not Laravel/Symfony). Entry point: `public/index.php`.

### Request Lifecycle
1. `public/index.php` bootstraps DI container (`config/services.php`), matches route via AltoRouter (`config/routes.php`)
2. Route format: `['GET', '/path', 'ControllerClassName', 'actionMethod']`
3. Controller is resolved from the DI container and the action method is called
4. Action returns a `Framework\Response`, which renders a PHP view template

### Key Layers
- **`src/Framework/`** — Core framework: `Di` (service container), `Request`, `Response`, `Mail/`, `Billing/` (Stripe), `CloudFront/`, `Turnstile/`
- **`src/PyAngelo/Controllers/`** — 100+ controllers organized by feature (Sketch, Tutorials, Blog, Admin, Membership, etc.)
- **`src/PyAngelo/Repositories/`** — MySQL data access layer; one repository class per domain (Blog, Sketch, Tutorial, Person, etc.), injected via DI
- **`src/PyAngelo/FormServices/`** — Business logic layer between controllers and repositories (registration, password reset, etc.)
- **`views/`** — PHP templates (`.html.php`, `.json.php`); shared layouts in `views/layout/`
- **`config/services.php`** — All DI bindings; the authoritative list of available services
- **`config/routes.php`** — All URL routes

### DI Container Pattern
Services are registered in `config/services.php` and injected via constructors. Controllers receive `$request`, `$response`, `$auth`, and domain-specific repositories/form services. When adding a new controller, register it in `config/services.php`.

### Authentication
`src/PyAngelo/Auth/Auth.php` — session-based auth, CSRF tokens, remember-me. Access via `$this->auth` in controllers (e.g., `$this->auth->loggedIn()`, `$this->auth->person`).

### Email
Dual-mode: `Framework\Mail\LoggerMail` writes to log file (dev), `Framework\Mail\AwsSesMail` sends via AWS SES (prod). Controlled by `MAIL_METHOD` env var.

### Frontend
- **Skulpt** — custom Python-in-browser interpreter (fork at `github:pingskills/skulpt#pyangelo`) powers the sketch editor
- **Laravel Mix / Webpack** — source in `resources/assets/js/` and `resources/assets/sass/`, compiled to `public/js/` and `public/css/`
- **Bootstrap 3**, **jQuery**, **TinyMCE** (rich text), **Howler.js** (audio)

### Testing Patterns
- PHP tests use **Mockery** for mocking; `tests/Factory/TestData.php` for test data factories
- `tests/di/ServicesTest.php` validates the entire DI container and all registered services
- JS tests use **Jest**; test files live alongside source in `resources/assets/js/*.test.js`

### Database
MySQL accessed directly via `mysqli`. Plain SQL migration files in `database/migrations/` (numbered, e.g. `0001_create_users.sql`). No ORM.

A fresh database is built from `database/migrations/pyangelo-schema.sql` (the current schema; replaying the numbered migrations no longer reproduces it) plus lookup rows: `database/test-reference-data.sql` for the test database, and that followed by `database/reference-data.sql` for a dev database.
