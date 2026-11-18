# Commands Reference

All available Make commands for local development.

## Lifecycle

| Command | Purpose |
|---------|---------|
| `make boot` | Full setup: build, install, start, init DB |
| `make build` | Build Docker images |
| `make up` | Start services |
| `make down` | Stop services |
| `make restart` | Restart app & cache |

## Development

| Command | Purpose |
|---------|---------|
| `make shell` | Access backend container bash |
| `make logs` | View real-time logs (Ctrl+C to exit) |
| `make help` | Show all commands |

## Database

| Command | Purpose |
|---------|---------|
| `make db` | Create database & schema |
| `make create-database` | Create database only |
| `make create-schema` | Create tables from entities |

## Quality Assurance

| Command | Purpose |
|---------|---------|
| `make test` | Run PHPUnit tests |
| `make frontend-test` | Run Vitest (React) tests |
| `make sa` | Static analysis (PHPStan) |
| `make cs` | Fix code style |
| `make arch` | Architecture check (Deptrac) |
| `make qa` | Full suite (SA + CS + Tests + Arch) |

**Run `make qa` before committing.**

## Inside Container (`make shell`)

```bash
# Symfony
bin/console list                 # All Symfony commands
bin/console make:entity User     # Create entity
bin/console make:controller Ctrl # Create controller
bin/console make:test --test-class X # Create test

# Database
bin/console doctrine:migrations:migrate

# Testing
bin/phpunit                      # Run all tests
bin/phpunit tests/Unit/X.php     # Specific file

# Quality
vendor/bin/phpstan analyse --memory-limit=1G
vendor/bin/php-cs-fixer fix
```

## See Also

- **Stack:** [Stack Overview](./STACK.md)
- **Development:** [Development Guide](./DEVELOPMENT.md)
- **Getting Started:** [Getting Started](./GETTING_STARTED.md)

