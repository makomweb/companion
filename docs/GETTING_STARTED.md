# Getting Started

## Setup

**Prerequisites:** Docker & Docker Compose

**Bootstrap** (first time only):
```bash
make boot
```

This builds images, installs dependencies, starts services, and initializes the database.

**Access:**
- Frontend: http://localhost:5173
- Backend: http://localhost:8080/api/health
- Database UI: http://localhost:8085 (user: root, pwd: secret)

## Common First Steps

```bash
# Start services
make up

# Open backend shell
make shell

# View logs
make logs

# Stop services
make down
```

## Project Structure

```
companion/
├── backend/              # Symfony
├── frontend/             # React
├── build/                # Docker files
├── docs/                 # Documentation
├── Makefile
└── docker-compose.yaml
```

## Troubleshooting

**Services won't start?**
```bash
make down
docker compose down -v  # Remove volumes
make boot
```

**Port already in use?**  
Edit `docker-compose.yaml` and change port numbers.

**Database connection errors?**
```bash
docker compose logs db
```

## Next Steps

- Build features: See [Development Guide](./DEVELOPMENT.md)
- Available commands: See [Commands Reference](./COMMANDS.md)
- Tech stack details: See [Stack Overview](./STACK.md)

