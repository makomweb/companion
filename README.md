<div align="center">

# Companion

<!-- Badges -->
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.5-777BB4.svg?logo=php&logoColor=white)](https://www.php.net/)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%208%2F8-blueviolet)](https://phpstan.org/)
[![Docker](https://img.shields.io/badge/Docker-Multi--stage-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)
![app-ci-workflow](https://github.com/makomweb/companion/actions/workflows/app-ci.yaml/badge.svg)
[![codecov](https://codecov.io/gh/makomweb/companion/graph/badge.svg?token=QA4U2DBXM9)](https://codecov.io/gh/makomweb/companion)

A production-ready full-stack application scaffold combining **Symfony 8.1** backend + **React 18** frontend + **Docker** containerization, and comprehensive quality assurance which can be used as a project template. 🚀

Includes [**Neuron AI**](https://docs.neuron-ai.dev/) 🔗 integration for real-time LLM-powered conversations with tool execution capabilities, enabling intelligent chat features backed by your backend logic.

<img src="./img/screenshot.png" alt="Companion Screenshot" width="600" />

</div>

## Quick Start

```bash
make boot    # Setup everything
make up      # Start services
make down    # Stop services
```

**URLs:**
- Frontend: http://localhost:5173
- Backend: http://localhost:8080/api/health
- Database UI: http://localhost:8085 (user: root, pwd: secret)

## Common Commands

```bash
make shell            # Access backend container
make test             # Run tests
make qa               # Full quality checks
make help             # Show all commands
```

**Documentation:** See [`docs/`](./docs/INDEX.md)
