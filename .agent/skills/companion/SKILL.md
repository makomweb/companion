---
name: companion-backend-commands
description: Execute Symfony backend commands, PHP testing, and code quality checks. Use when asked to create entities, controllers, run tests, perform static analysis, or manage the database. Always run these commands inside the backend container via 'make shell'.
---

## Backend Command Execution

All `bin/console`, `vendor/bin/`, and `bin/phpunit` commands must run inside the backend container.

### Starting Backend Container

```bash
make shell
```

## Common Backend Tasks

### Create Entity

```bash
bin/console make:entity User
bin/console make:migration
bin/console doctrine:migrations:migrate
```

### Create Controller

```bash
bin/console make:controller Api/UserController
```

### Create Test

```bash
bin/console make:test --test-class User
```

## Quality Assurance

```bash
make qa          # Full suite on host
make test        # Tests on host
```

Or inside container:
```bash
bin/phpunit
vendor/bin/phpstan analyse --memory-limit=1G
vendor/bin/php-cs-fixer fix
vendor/bin/deptrac analyse
```

## Important Constraints

- **Backend commands must run inside `make shell`** — Host execution fails
- **Services must be running** — Run `make up` before `make shell`
- **Database migrations are required** — After creating entities, run `make db`
- **Code style must pass** — Run `make cs` before committing

---

See: [Development Guide](../docs/DEVELOPMENT.md), [Commands](../docs/COMMANDS.md)

