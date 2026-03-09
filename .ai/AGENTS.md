# Padi PHP Project Guidelines

A personal library for implementing design patterns and utilities. All development happens in Docker containers for consistency.

## Code Style

**PSR-12 with Custom Extensions** (enforced by Mago):
- Single quote for code string, like technical name, id, etc and double quotes for strings, like error message, user output, etc

**Immutability & Type Safety**:
- Comprehensive type hints on parameters and returns

See [mago.toml](mago.toml) for exact linting rules and analysis thresholds.

## Architecture

**Design Patterns Library**: Flat structure demonstrating clean code principles
- `src/` — Main classes and utilities (PSR-4 namespace: `Padi\`)
- `tests/` — PHPUnit tests mirroring source structure
- Value Objects: Immutable, validated (Email, etc.)

## Commands

**Prerequisites**: Docker + Make

All commands execute inside Docker containers. See [Makefile](Makefile) for full options.

### Build project

```bash
make composer-install # Build Docker image & install dependencies (run first!)
```

### Run tests

```bash
make test                      # Run all PHPUnit tests
make test ARGS="path/to/test"  # Run specific test file
```

### Format code, lint & analysis issue

```bash
make fix
```

## Conventions

**Testing**:
- PHPUnit 13.x for unit tests
- Test files in `tests/` named `{ClassName}Test.php`
- Extend `PHPUnit\Framework\TestCase`
- Test exception behavior explicitly

**Method Naming**:
- CamelCase for classes/methods
- UPPER_SNAKE_CASE for constants

**File Organization**:
- One class per file (PHP standard)

**Development Notes**:
- First run: Always execute `make composer-install` to build the Docker image
- Permissions: UID/GID are passed to containers to prevent file permission issues
- `.image-built` marks successful image build — delete to rebuild
- vendor/ and .image-built are git-ignored
