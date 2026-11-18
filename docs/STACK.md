# Stack Overview

## Technologies

| Component | Technology |
|-----------|-----------|
| Backend | Symfony 8.1 + FrankenPHP |
| Frontend | React 18 + Vite + TypeScript |
| Database | MySQL 8.4 |
| Cache | Valkey (Redis compatible) |
| Testing | PHPUnit, Vitest |
| Quality | PHPStan (level 8), PHP-CS-Fixer, ESLint, Deptrac |

## Architecture

```
Browser (localhost:5173)
  ├→ Vite Dev Server (live reload)
  └→ Fetch API
     └→ http://localhost:8080/api/*
        └→ Symfony
           ├→ MySQL
           └→ Valkey
```

## Services

**Backend** (`src/`)
- Controllers: HTTP endpoints (`src/Controller/Api/`)
- Entities: Database models (`src/Entity/`)
- Repositories: Data queries (`src/Repository/`)
- Commands: CLI tools (`src/Command/`)

**Frontend** (`frontend/src/`)
- App.tsx: Root component
- components/: Reusable UI
- pages/: Page components (optional)
- services/: API calls (optional)

## Environment Variables

**Backend** (`.env` or `.env.local`)
```
DATABASE_URL=mysql://root:secret@db:3306/companion
CACHE_URL=redis://cache:6379
APP_ENV=dev
```

**Frontend** (`frontend/.env`)
```
VITE_API_URL=http://localhost:8080
```

## Volumes (Persistent)

- `db_data`: MySQL database
- `cache_data`: Valkey cache

Both survive container restarts.

---

**See:** [Getting Started](./GETTING_STARTED.md) | [Development Guide](./DEVELOPMENT.md) | [Commands](./COMMANDS.md)

