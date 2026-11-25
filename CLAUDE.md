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
npm run test:e2e                                      # Vite build, then Playwright smoke tests in Chromium
```

The Playwright tests (`tests/e2e/`) render real templates with fixture data through `tests/e2e/router.php`, so they need no database, but the sort and playground pages load jQuery 1.12 and Ace from CDNs, so they need internet access. On a new machine run `npx playwright install chromium` first.

### Frontend
```bash
npm run build  # Vite build to public/build/ (git-ignored)
npm run watch  # Rebuild on change (refresh the browser yourself)
npm run prod   # Always rebuild Skulpt in node_modules/skulpt, then Vite (deploy runs this)
```

`build`, `dev`, `watch` and `test:e2e` first run `bin/ensure-skulpt.cjs`, which builds Skulpt only when its `dist/` files are missing (after a fresh `npm install` or `npm run refreshskulpt`). Skulpt's build runs `git describe`, so it must run inside a git checkout.

There is no lint script: the existing code has about 2,000 ESLint style problems. The check that matters for Vite, no undeclared variables (which throw in ES modules), is enforced by `resources/assets/js/modules-strict.test.js`.

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
- **Vite** — source in `resources/assets/js/` and `resources/assets/sass/`, built to `public/build/`. Each entry point is listed in `vite.entries.js`. Templates load a script with `<?= viteScript('resources/assets/js/app.js'); ?>`, which also links any CSS it imports, and the stylesheet with `vite('resources/assets/sass/app.scss')`. Scripts are ES modules, so they run after the page is parsed, after any classic `<script>` tags. PHPUnit's view tests read `public/build/.vite/manifest.json`, so build before running them. Vite's `strictExecutionOrder` must stay on: TinyMCE's skin imports call the global `tinymce`, which only exists once `tinymce/tinymce` has run.

### Deploying
`src/Framework/Scripts/deploy.sh` (run on the server from `~/pyangelo.com` after `git pull`) installs Composer and npm packages, runs `npm run production`, then `publish.sh` copies the release to `/srv/http/pyangelo.com` while the site keeps serving: `src/`, `views/`, `public/images` and `public/samples` are copied beside the live ones and swapped in with `mv --exchange`, and new build files are added beside the old ones (pages already open still need them) with the manifest switched last. Build files no release has included for a week are deleted. `mix()` in `src/Helpers/functions.php` is kept only for the first Vite deploy; remove it, and on the server `public/js`, `public/css`, `public/fonts` and `public/mix-manifest.json`, after that deploy.
- **Bootstrap 3**, **jQuery**, **TinyMCE** (rich text), **Howler.js** (audio)

### Testing Patterns
- PHP tests use **Mockery** for mocking; `tests/Factory/TestData.php` for test data factories
- `tests/di/ServicesTest.php` validates the entire DI container and all registered services
- JS tests use **Jest**; test files live alongside source in `resources/assets/js/*.test.js`

### Database
MySQL accessed directly via `mysqli`. Plain SQL migration files in `database/migrations/` (numbered, e.g. `0001_create_users.sql`). No ORM.

A fresh database is built from `database/migrations/pyangelo-schema.sql` (replaying the numbered migrations no longer reproduces it), then `database/migrations/pyangelo-schema-changes.sql` (the `db_change` rows for the migrations the dump includes), then lookup rows: `database/test-reference-data.sql` for the test database, and that followed by `database/reference-data.sql` for a dev database.

`bin/migrate` lists migrations newer than the database's highest `db_change` row; `bin/migrate apply` applies them (`--env=.env.test` for the test database, `--prod` to pick `_prod.sql` over `_dev.sql` variants). A new migration is `NNNN_description.sql` with the next number and must end with its own `insert into db_change` row, or the runner stops. When regenerating `pyangelo-schema.sql`, add `pyangelo-schema-changes.sql` rows for the migrations it now includes.
