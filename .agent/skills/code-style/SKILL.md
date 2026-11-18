---
name: companion-code-style
description: Apply code style conventions and auto-fix formatting issues. Run 'make cs' before committing.
---

## Code Style Standards

### PHP Backend

**Rules:**
- Declare strict types: `declare(strict_types=1);` at the top of every PHP file
- Follow Symfony coding standards (PSR-12 with Symfony enhancements)
- Use PHP-CS-Fixer for automatic formatting

**Apply fixes:**
```bash
make cs          # On host
# Or inside container:
vendor/bin/php-cs-fixer fix
```

### TypeScript/React Frontend

**Rules:**
- Use ESLint for linting
- Follow React best practices
- Use TypeScript strict mode

**Apply fixes:**
```bash
cd frontend && npm run lint:fix
```

## Before Committing

Always run:
```bash
make qa
```

This validates:
1. Static analysis (PHPStan)
2. Code style (PHP-CS-Fixer)
3. Tests
4. Architecture (Deptrac)

---

See: [Development Guide](../docs/DEVELOPMENT.md)

