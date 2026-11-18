# Development Guide

Assuming your stack is running (`make up`), here's how to build features.

## Backend (Symfony)

### Create Entity

```bash
make shell
bin/console make:entity User
bin/console make:migration
bin/console doctrine:migrations:migrate
```

### Create Controller/API Endpoint

```bash
bin/console make:controller Api/UserController
```

**Convention:** API controllers go in `src/Controller/Api/` with `#[Route('/endpoint')]` (prefix `/api` added automatically).

### Create Service

Create class in `src/Service/` - auto-registered with autowiring:

```php
<?php
declare(strict_types=1);

namespace App\Service;

class UserService
{
    public function getUser(int $id): array { return []; }
}
```

Inject via constructor:
```php
class UserController
{
    public function __construct(private UserService $userService) {}
}
```

## Frontend (React)

### Create Component

Create `frontend/src/components/MyComponent.tsx`:

```tsx
interface MyComponentProps {
  title: string
}

export function MyComponent({ title }: MyComponentProps) {
  return <div>{title}</div>
}
```

### Call Backend API

```tsx
import { useEffect, useState } from 'react'

export function UserList() {
  const [users, setUsers] = useState([])

  useEffect(() => {
    fetch(`${import.meta.env.VITE_API_URL}/api/users`)
      .then(r => r.json())
      .then(d => setUsers(d.users))
  }, [])

  return <div>{/* render users */}</div>
}
```

## Testing

**Create backend test:**
```bash
make shell
bin/console make:test --test-class User
```

**Run tests:**
```bash
make test          # Backend tests
make frontend-test # Frontend tests
make qa            # All checks (PHPStan + CS + Tests + Architecture)
```

## Code Quality

Before committing, run:
```bash
make qa
```

This validates:
1. Static analysis (PHPStan)
2. Code style (PHP-CS-Fixer)
3. Tests (PHPUnit + Vitest)
4. Architecture (Deptrac)

**All must pass.**

## Conventions

**Backend:**
- Strict types: `declare(strict_types=1);` in every PHP file
- Controllers extend `AbstractController`, stay thin
- Repositories for queries, Services for logic
- Use dependency injection

**Frontend:**
- Function components with hooks
- TypeScript interfaces for props
- Use `const` for components

## Debugging

**Backend:**
```bash
make shell
dump($var);  # Print variable
dd($var);    # Print and exit
```

Check logs: `make logs`

**Frontend:**
- Browser DevTools (F12)
- React DevTools browser extension

## See Also

- **Commands:** [Commands Reference](./COMMANDS.md)
- **Stack:** [Stack Overview](./STACK.md)
- **Setup:** [Getting Started](./GETTING_STARTED.md)

