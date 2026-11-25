.PHONY: help boot build up down shell logs open hint composer-install maintain composer-require npm-install npm-require db create-database create-schema cache-clear qa sa cs test frontend-test arch restart reset

help:
	@echo ""
	@echo "╔════════════════════════════════════════════════════════════════════════════╗"
	@echo "║                     🚀 COMPANION - Make Targets 🚀                         ║"
	@echo "╚════════════════════════════════════════════════════════════════════════════╝"
	@echo ""
	@echo "📦 SETUP & MANAGEMENT"
	@echo "  boot                  ⚙️  Build images, install deps, start stack, init DB"
	@echo "  up                    ⬆️  Start the stack"
	@echo "  down                  ⬇️  Stop the stack"
	@echo ""
	@echo "🔧 DEVELOPMENT"
	@echo "  shell                 🐚 Open bash on app container"
	@echo "  build                 🏗️  Build Docker images"
	@echo "  logs                  📋 View app logs (tail -f)"
	@echo "  open                  🌐 Open the app in a browser"
	@echo "  hint                  💡 Print local app URLs"
	@echo ""
	@echo "📚 COMPOSER"
	@echo "  composer-install      📦 Run composer install"
	@echo "  composer-require      ➕ Add a package (use PKG=vendor/package)"
	@echo "  maintain              🔄 Check outdated deps and update (composer -W + npm)"
	@echo ""
	@echo "📚 NPM"
	@echo "  npm-install           📦 Run npm install"
	@echo "  npm-require           ➕ Add a package (use PKG=package-name)"
	@echo ""
	@echo "🗄️  DATABASE"
	@echo "  db                    🔧 Create database and schema"
	@echo "  create-database       📊 Create database if not exists"
	@echo "  create-schema         📐 Create tables from entity definitions"
	@echo "  seed		           🏄‍♀️ Seed database"
	@echo ""
	@echo "⚡ CACHE"
	@echo "  cache-clear           🧹 Clear Symfony app cache"
	@echo ""
	@echo "✅ QUALITY ASSURANCE"
	@echo "  qa                    🎯 Run full QA suite (SA + CS + Test + Frontend + Arch)"
	@echo "  sa                    🔍 Static code analysis (PHPStan level 8)"
	@echo "  cs                    🎨 Fix code style (Symfony rules)"
	@echo "  test                  ✓  Run PHPUnit tests"
	@echo "  frontend-test         ✓  Run frontend tests (Vitest)"
	@echo "  arch                  📐 Run architecture tests (Deptrac)"
	@echo ""


boot: build composer-install up create-database create-schema seed

build:
	@echo "Building Docker images (development)..."
	docker compose build

prod:
	@echo "Building Docker images (production)..."
	docker build -f build/Dockerfile.backend.prod -t companion-backend:prod .

up:
	@echo "Starting the stack..."
	docker compose up -d
	@echo "Stack started!"
	@$(MAKE) hint

hint:
	@echo ""
	@echo "✨ Application URLs:"
	@echo "  🌐 Backend   → http://localhost:8080"
	@echo "  ⚛️  Frontend  → http://localhost:5173"
	@echo "  📊 Dashboard → http://localhost:8001 (links to all services)"
	@echo "  🗄️  Database  → http://localhost:8085 (Adminer, user: root, pwd: secret)"
	@echo "  💾 Valkey    → localhost:6379"
	@echo ""
	@echo "💡 Tip: Use 'make logs' to view logs"
	@echo ""

shell:
	@echo "Open shell on backend container..."
	docker compose exec -it backend bash

down:
	@echo "Stopping the stack..."
	docker compose down

composer-install:
	@echo "Running composer install..."
	docker compose run --rm composer install

maintain:
	@echo "🔄 Checking for outdated dependencies..."
	@echo ""
	@echo "📦 Composer outdated packages:"
	docker compose run --rm composer show --outdated
	@echo ""
	@echo "🔄 Updating Composer packages..."
	docker compose run --rm composer update -W
	@echo ""
	@echo "📦 npm outdated packages:"
	docker compose run --rm npm outdated || true
	@echo ""
	@echo "🔄 Updating npm packages..."
	docker compose run --rm npm update
	@echo ""
	@echo "✅ All dependencies updated!"

composer-require:
	@test -n "$(PKG)" || (echo "Error: specify package with PKG=vendor/package"; exit 1)
	@echo "Adding package $(PKG)..."
	docker compose run --rm composer require $(PKG)

npm-install:
	@echo "Running npm install..."
	docker compose run --rm npm install

npm-require:
	@test -n "$(PKG)" || (echo "Error: specify package with PKG=package-name"; exit 1)
	@echo "Adding package $(PKG)..."
	docker compose run --rm npm install $(PKG)

db: create-database create-schema seed

create-database:
	@echo "Create database"
	docker compose exec -it backend bin/console doctrine:database:create --if-not-exists
	@echo "✓ Database created"

create-schema:
	@echo "Create database schema"
	docker compose exec -it backend bin/console doctrine:schema:update --force
	@echo "✓ Schema created"

seed:
	@echo "Seed database"
	docker compose exec -it backend bin/console app:create-user demo@example.com secret
	docker compose exec -it backend bin/console app:create-user admin@example.com secret --admin
	@echo "✓ Database initialized"

logs:
	docker compose logs -f app

cc: cache-clear
cache-clear:
	@echo "Clearing Symfony application cache..."
	docker compose exec -it backend bin/console cache:clear
	docker compose exec -it backend bin/console cache:clear --env=test

sa:
	@echo "Run static code analysis..."
	docker compose exec -it backend vendor/bin/phpstan analyse --memory-limit=1G

cs:
	@echo "Fix code style..."
	docker compose exec -it backend vendor/bin/php-cs-fixer fix

test:
	@echo "Run tests..."
	docker compose exec -it backend bin/phpunit --coverage-clover=coverage/clover.xml --coverage-filter=src

frontend-test:
	@echo "Run frontend tests..."
	docker compose run --rm frontend npm test

arch:
	@echo "Run architecture tests..."
	docker compose exec -it backend vendor/bin/deptrac analyse --report-uncovered


qa: sa cs test frontend-test arch

open:
	@if command -v xdg-open > /dev/null 2>&1; then \
		xdg-open http://localhost:8001 2>/dev/null & \
	elif command -v open > /dev/null 2>&1; then \
		open http://localhost:8001; \
	elif command -v wslview > /dev/null 2>&1; then \
		wslview http://localhost:8001; \
	elif command -v cmd.exe > /dev/null 2>&1; then \
		cmd.exe /c start http://localhost:8001; \
	else \
		echo "❌ Could not detect browser launcher."; \
		echo "📍 Please open http://localhost:8001 manually"; \
	fi

reset: restart
restart:
	@echo "Restarting containers..."
	docker compose restart backend
	docker compose restart frontend