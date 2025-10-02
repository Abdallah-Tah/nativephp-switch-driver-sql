# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 application with NativePHP/Electron integration, designed for native desktop app development with SQL database driver switching capabilities. The project uses PHP 8.4 and includes a custom NativePHP package (`amohamed/nativephp-ext-cli`) for enhanced database driver support.

## Common Development Commands

### Development Server
- `composer dev` - Runs Laravel server, queue worker, and Vite dev server concurrently
- `composer native:dev` - Runs NativePHP native app with Vite dev server
- `php artisan serve` - Laravel development server only
- `npm run dev` - Vite development server for assets

### Testing
- `composer test` - Runs full test suite (clears config first)
- `./vendor/bin/pest` - Run Pest tests directly
- `./vendor/bin/pest --filter=ExampleTest` - Run specific test

### Code Quality
- `./vendor/bin/pint` - PHP CS Fixer (Laravel Pint) for code formatting
- `./vendor/bin/phpstan analyse` - Static analysis with PHPStan (level 7)
- `./vendor/bin/rector process` - Code refactoring with Rector

### Database
- `php artisan migrate` - Run migrations
- `php artisan db:seed` - Seed database
- Database uses SQLite by default (`database/database.sqlite`)

### Assets
- `npm run build` - Build frontend assets for production

### Custom PHP Binary Building
- `php artisan php-ext:install` - Build custom PHP binary with selected extensions (interactive)
- `php -d memory_limit=512M artisan php-ext:install` - Build with increased memory (recommended)
- `php artisan php-ext:install --php-version=8.4` - Build specific PHP version
- `php artisan php-ext:install --php-version=8.3.13` - Build specific patch version
- Built PHP binary location: `static-php-cli/buildroot/bin/php.exe`
- Logs saved to: `static-php-cli/log/spc.output.log` and `spc.shell.log`

#### Supported PHP Versions
- **8.1.x** - Full support for all extensions including SQL Server
- **8.2.x** - Full support for all extensions including SQL Server
- **8.3.x** - Full support for all extensions including SQL Server
- **8.4.x** - Supported, but SQL Server extensions (sqlsrv, pdo_sqlsrv) are not available
- **Custom versions** - Any specific version can be specified (e.g., 8.3.13, 8.4.1)

## System Requirements

### Required for Development
- **PHP 8.3+** - For Laravel 12 and development server
- **Node.js 18+** - For Vite and frontend tooling
- **Composer** - PHP dependency management

### Required for Custom PHP Binary Building (Windows)
- **Python 3.8+** - Used for reliable tar.gz/tar.xz extraction on Windows
  - Handles archives with symlinks gracefully
  - Avoids Windows tar pipe issues
  - Install from: https://www.python.org/downloads/
- **CMake 3.15+** - Required for building native Windows libraries (zlib, openssl, etc.)
  - ⚠️ **CRITICAL**: Must add CMake to system PATH during installation
  - Download from: https://cmake.org/download/ (Windows x64 Installer)
  - Verify installation: `cmake --version`
- **Visual C++ Build Tools** - Microsoft C++ compiler (MSVC) for Windows builds
  - Automatically detected by php-sdk-binary-tools
- **Git for Windows** - Provides bash, tar, and git commands
  - Used for source code management and Unix-style tools

### PHP Configuration for Building
- **Memory Limit**: Minimum 512MB recommended for building PHP binaries
  - Default 128MB will cause memory exhaustion errors
  - Set via: `php -d memory_limit=512M` or edit `php.ini`
  - Location: Check with `php --ini`

## Architecture

### Core Components
- **Database Driver Switching**: Custom configuration in `config/nativephp-custom-php.php` supports multiple SQL drivers (MySQL, PostgreSQL, SQLite, SQL Server)
- **NativePHP Integration**: Native desktop app functionality via `app/Providers/NativeAppServiceProvider.php`
- **Custom PHP Binary**: Uses `amohamed/nativephp-ext-cli` package for enhanced database driver support

### Key Configuration Files
- `config/database.php` - Database connections for multiple SQL drivers
- `config/nativephp.php` - Native app configuration and packaging settings
- `config/nativephp-custom-php.php` - Custom PHP build configuration with database extensions

### Database Drivers
The application supports switching between:
- SQLite (default)
- MySQL/MariaDB
- PostgreSQL
- SQL Server

Database driver selection is controlled via the `DB_CONNECTION` environment variable.

### Code Quality Standards
- **Strict Types**: All PHP files use `declare(strict_types=1)`
- **Pint Configuration**: Custom Laravel Pint rules in `pint.json` with strict comparison and modern PHP features
- **PHPStan**: Level 7 static analysis covering app/, bootstrap/, routes/, and config/
- **Testing**: Pest framework for feature and unit tests

### Native App Features
- Window management via `Native\Laravel\Facades\Window`
- Custom PHP.ini configuration through `ProvidesPhpIni` interface
- Environment cleanup for production builds
- Auto-updater support (GitHub, S3, DigitalOcean Spaces)

### Development Environment
- Uses Vite for asset building with Tailwind CSS 4.x
- Concurrency support for running multiple development processes
- IDE helper files for Laravel/PHP development

## Custom PHP Binary Build Process

### Overview
The `php artisan php-ext:install` command builds a custom static PHP binary with selected database extensions using:
- **static-php-cli** - PHP static compiler for Windows
- **php-sdk-binary-tools** - Microsoft's PHP SDK for Windows builds
- **Python extraction script** - Windows-compatible tar.gz/tar.xz extraction with symlink handling

### Build Workflow
1. **Setup Phase**: Clones static-php-cli and php-sdk-binary-tools
2. **Download Phase**: Downloads PHP source, libraries, and extension sources
3. **Pre-extraction Phase**: Extracts PHP source and PECL extensions using Python (Windows-safe)
4. **Library Extraction**: Extracts native libraries with symlink handling
5. **Build Phase**: Compiles PHP with MSVC and selected extensions
6. **Verification Phase**: Tests compiled binary functionality

### Windows-Specific Adaptations
- **Symlink Handling**: Python script skips symlinks (not supported on Windows without admin rights)
- **Path Conversion**: Automatic conversion between Windows and Unix-style paths
- **GitHub Fallback**: Auto-clones from GitHub when standard downloads fail (e.g., openssl)
- **Hash File Creation**: Prevents static-php-cli from re-extracting pre-extracted sources
- **Windows-Optimized Repos**: Uses `winlibs` repositories for better Windows compatibility (libxml2, zlib, etc.)

### Extension Categories
- **Default Extensions**: Always included (pdo, sqlite3, pdo_sqlite, mbstring, openssl, curl, etc.)
- **Database Extensions**: MySQL (mysqli, pdo_mysql), PostgreSQL (pgsql, pdo_pgsql), SQL Server (sqlsrv, pdo_sqlsrv)
- **Extension Packs**: Web (dom, xml, gd), Performance (opcache), Processing (iconv, bcmath), etc.

## Troubleshooting

### Build Issues

#### Memory Exhaustion Error
```
Allowed memory size of 134217728 bytes exhausted
```
**Solution**: Increase PHP memory limit
```bash
php -d memory_limit=512M artisan php-ext:install
```
Or permanently edit `php.ini`:
```ini
memory_limit = 512M
```

#### CMake Not Found
```
'cmake' is not recognized as an internal or external command
```
**Solution**:
1. Install CMake from https://cmake.org/download/
2. During installation, select "Add CMake to system PATH"
3. Restart terminal
4. Verify: `cmake --version`

#### Python Unicode Error (SQLSRV Extensions)
```
UnicodeEncodeError: 'charmap' codec can't encode character '\u2705'
```
**Status**: Fixed in latest version
- Python script now uses ASCII-safe output
- Build verifies actual file extraction despite Python console errors
- SQLSRV extensions extract successfully

#### Symlink Errors (libxml2, libwebp)
```
tar: Cannot create symlink to 'tutorA.rng': No such file or directory
```
**Status**: Fixed in latest version
- Python script automatically skips symlinks on Windows
- Creates `.spc-hash` files to prevent re-extraction attempts
- Libraries build successfully without symlinks

#### GitHub Download Failures
```
Standard download failed after 2 attempts
```
**Status**: Auto-handled with GitHub fallback
- System automatically clones from GitHub after 2 failed attempts
- Uses Windows-optimized repos when available (e.g., winlibs/libxml2)
- Includes verification and progress reporting

### Build Verification

After successful build, verify extensions:
```bash
static-php-cli/buildroot/bin/php.exe -m
```

Check specific extension:
```bash
static-php-cli/buildroot/bin/php.exe --ri sqlsrv
```

### Clean Build
If build fails repeatedly, clean and restart:
```bash
rm -rf static-php-cli
php -d memory_limit=512M artisan php-ext:install
```

#### MSVC Compiler Errors (C4146, C4703)
```
error C4146: unary minus operator applied to unsigned type
error C4703: potentially uninitialized local pointer variable
```
**Cause**: PHP 8.3.26 has code patterns that trigger strict MSVC warnings with `/sdl` flag

**Solution**: These errors are compiler-specific and don't indicate actual code problems.

**Workaround**: If you encounter these errors:
1. The build system will automatically retry with the latest stable PHP version
2. Or clean build and try again - sometimes transient compiler issues resolve on retry
3. Consider using PHP 8.3.13 (known stable version for Windows builds)

**Note**: This is a known limitation with the latest PHP versions and MSVC's strict security checks. The PHP core team is aware of these warnings.