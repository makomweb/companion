---
name: companion-testing-strategy
description: Write, run, and validate tests for backend (PHPUnit) and frontend (Vitest). Run 'make qa' before committing.
---

## Testing Strategy

Backend: PHPUnit (Symfony + Doctrine)  
Frontend: Vitest (React)

## Backend Testing (PHPUnit)

**Test database:**
- Uses SQLite in-memory (`sqlite:///:memory:`)
- Schema auto-created before each test
- Much faster than MySQL

**Test locations:**
- Unit tests: `tests/Unit/`
- Functional tests: `tests/Functional/`

**Create test:**
```bash
make shell
bin/console make:test --test-class User
```

**Example functional test:**
```php
<?php
declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api;

use App\Tests\Functional\DBAwareWebTestCase;

class LoginControllerTest extends DBAwareWebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $this->createTestUser(email: 'user@test.com', password: 'pass123');

        $client->request('POST', '/api/login', [], [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'user@test.com', 'password' => 'pass123'])
        );

        $this->assertResponseStatusCodeSame(200);
    }
}
```

**Run tests:**
```bash
make test          # All tests
make frontend-test # Frontend tests
make qa            # Full suite (SA + CS + Tests + Arch)
```

## Frontend Testing (Vitest)

**Test locations:**
- Tests co-located with components: `src/App.test.tsx`

**Example test:**
```tsx
import { describe, it, expect } from 'vitest'
import { render, screen } from '@testing-library/react'
import { UserCard } from './UserCard'

describe('UserCard', () => {
  it('renders user name', () => {
    const user = { id: 1, name: 'John', email: 'john@example.com' }
    render(<UserCard user={user} />)
    expect(screen.getByText('John')).toBeDefined()
  })
})
```

**Run:**
```bash
make frontend-test
# Or manually:
cd frontend && npm test
```

## QA Before Committing

Always run:
```bash
make qa
```

This validates:
1. Static analysis (PHPStan)
2. Code style (PHP-CS-Fixer)
3. Tests (PHPUnit + Vitest)
4. Architecture (Deptrac)

---

See: [Development Guide](../docs/DEVELOPMENT.md)

