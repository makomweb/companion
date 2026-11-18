# Copilot Instructions for Companion

This guide helps Copilot sessions work effectively in this Symfony 8 + React application.

## Quick Start

```bash
make boot    # First time setup
make up      # Start services
make down    # Stop services
make shell   # Open container shell
make logs    # View logs
make help    # Show all commands
```

Backend: http://localhost:8080  
Frontend: http://localhost:5173  
Database UI: http://localhost:8085 (user: root, pwd: secret)

## Documentation & Skills

Guides are available in `docs/`:

- **[Getting Started](../docs/GETTING_STARTED.md)** — Setup, first steps, troubleshooting
- **[Stack Overview](../docs/STACK.md)** — Architecture, technologies, service layout
- **[Development Guide](../docs/DEVELOPMENT.md)** — Backend (Symfony) and frontend (React) development
- **[Commands Reference](../docs/COMMANDS.md)** — All available Make tasks

Agent Skills (`.agent/skills/`):

- **companion-backend-commands** — Execute Symfony commands inside container
- **companion-code-style** — Apply code formatting and style conventions
- **companion-testing-strategy** — Write and run tests (PHPUnit, Vitest)

## Key Conventions

### Code Style

- **Declared strict types** on all PHP files: `declare(strict_types=1);`
- **PHP-CS-Fixer** enforces Symfony style rules
- **ESLint** for TypeScript/React code
- Run `make cs` to auto-fix code style

### Testing

- Unit tests in `tests/Unit/` with PSR-4 namespace `App\Tests\`
- PHPUnit at strict settings (fails on deprecations/warnings)
- Bootstrap at `tests/bootstrap.php`
- Run with `make test`

### Static Analysis

- **PHPStan** at maximum level (8/8)
- **ESLint** for frontend
- Run with `make sa` (backend only) or `make qa` (full suite)

### Architecture

- **Services**: Auto-wired from `src/`, auto-registered
- **Controllers**: HTTP endpoints in `src/Controller/Api/` for API
- **Entities**: Doctrine ORM models in `src/Entity/`
- **Repositories**: Data access in `src/Repository/`
- **Commands**: CLI commands in `src/Command/`
- **Tests**: Unit tests in `tests/Unit/`, Functional in `tests/Functional/`

## Backend (Symfony 8.1)

### File Locations

```
app/
  config/              # Service & package config (auto-loaded)
  public/              # Web root (index.php)
  src/
    Controller/Api/    # HTTP API endpoints
    Entity/            # Doctrine entities
    Repository/        # Data access
    Command/           # CLI commands
    Kernel.php         # App kernel
  tests/
    Unit/              # PHPUnit unit tests
    Functional/        # Integration tests
  migrations/          # Database migrations
  templates/           # Twig templates
```

### Common Tasks

**Create entity:**
```bash
make shell
bin/console make:entity User
bin/console make:migration
bin/console doctrine:migrations:migrate
```

**Create controller:**
```bash
bin/console make:controller Api/UserController
```

**Create service:**
```bash
# Manually create in src/, auto-registered with autowiring
# Constructor injection of dependencies works automatically
```

**Create test:**
```bash
bin/console make:test --test-class User
# Edit tests/Unit/UserTest.php
make test
```

## Frontend (React 18 + Vite)

### File Locations

```
frontend/
  src/
    main.tsx           # Entry point
    App.tsx            # Root component
    components/        # Reusable components
    pages/             # Page components (optional)
    services/          # API calls (optional)
    types/             # TypeScript types (optional)
  public/              # Static assets
  index.html           # HTML template
```

### React Best Practices

- Use **function components** with hooks
- **TypeScript** for all files
- Props with **interfaces**
- **useEffect** for side effects
- Call API via `fetch()` with backend URL from `import.meta.env.VITE_API_URL`

**Example component:**
```tsx
interface User {
  id: number
  name: string
  email: string
}

export function UserCard({ user }: { user: User }) {
  return <div>{user.name}</div>
}
```

## Environment & Configuration

### Backend Variables (app/.env.local)

```
DATABASE_URL=mysql://root:secret@db:3306/companion
CACHE_URL=redis://cache:6379
APP_ENV=dev
APP_SECRET=your-secret
APP_VERSION=0.1.0
```

### Frontend Variables (frontend/.env)

```
VITE_API_URL=http://localhost:8080
```

## Running QA

```bash
make qa              # Full suite (SA + CS + Test + Arch)
make sa              # PHPStan analysis only
make cs              # PHP-CS-Fixer only
make test            # PHPUnit only
make arch            # Deptrac architecture check
```

**Always run before committing.**

## Debugging

**Backend:**
```bash
make logs            # View app logs
make shell           # Access container
dump($var);          # Dump variable
dd($var);            # Dump and exit
```

**Frontend:**
- Browser DevTools (F12)
- React DevTools browser extension
- Console for errors/warnings

**Database:**
- Adminer at http://localhost:8085
- Or via shell: `mysql -h db -u root -psecret`

## Useful Commands

```bash
make shell              # Access app container
make logs               # View logs (Ctrl+C to exit)
make up/down            # Start/stop services
make test               # Run tests
make qa                 # Full QA
make composer-require   # Add PHP package
make db                 # Create database
make cache-clear        # Clear caches
make help               # Show all commands
```

## Important Notes

✅ **This is a scaffold** — No business logic, only structure  
✅ **Ready to extend** — Add entities, components, and features  
✅ **Well configured** — QA tools, Docker, database setup included  
✅ **Documented** — Guides for every aspect  

---

For more help, check the documentation in `docs/` or run `make help` for available commands.