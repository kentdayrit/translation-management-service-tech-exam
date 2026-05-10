# Translation Management API

A Laravel-based REST API for managing translations with search, export, and authentication features.

## Features

- 🔐 **Authentication**: Laravel Sanctum for secure API access
- 🔍 **Search & Filter**: Advanced translation search by key, content, locale, and tags
- 📤 **Export**: Bulk export translations by locale
- 📚 **API Documentation**: Interactive Swagger UI documentation
- 🚀 **Caching**: Redis-backed caching for performance
- ✅ **Validation**: Comprehensive request validation
- 🏗️ **Versioned API**: v1 API endpoints

## Tech Stack

- **Laravel 12** - PHP framework
- **Laravel Sanctum** - API authentication
- **Redis** - Caching and sessions
- **MySQL** - Database
- **L5 Swagger** - API documentation
- **PHP 8.2+** - Language version

## Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 8.0+
- Redis (optional, for caching)
- Docker & Docker Compose (for containerized setup)

## Installation

### Option 1: Docker Setup (Recommended)

1. **Clone the repository**
   ```bash
   git clone https://github.com/kentdayrit/translation-management-service-tech-exam.git
   cd translation-management-service/laravel-api
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   # Edit .env with your database and Redis settings
   ```

3. **Build and Run with Docker**
   ```bash
   # From the project root
   docker-compose up -d
   ```

4. **Run Migrations**
   ```bash
   docker-compose exec api php artisan migrate
   ```

5. **Seed Database (Optional)**
   ```bash
   docker-compose exec api php artisan db:seed
   ```

### Option 2: Local Setup

1. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   # Create MySQL database
   php artisan migrate
   php artisan db:seed
   ```

4. **Start the Server**
   ```bash
   php artisan serve
   ```

## API Documentation

Once the application is running, access the interactive API documentation at:

```
http://localhost/docs
```

The documentation is auto-generated from PHP 8 attributes using L5 Swagger.

## API Endpoints

### Authentication
- `POST /api/v1/login` - User login
- `POST /api/v1/logout` - User logout (requires auth)

### Translations
- `GET /api/v1/translations/search` - Search translations
- `POST /api/v1/translations` - Create/update translation (requires auth)
- `GET /api/v1/translations/export/{locale}` - Export translations by locale

## Design Choices

### Architecture

**Laravel Framework**: Chosen for its robust ecosystem, security features, and developer experience. Laravel 12 provides modern PHP features and long-term support.

**API-First Design**: RESTful API with consistent JSON responses using Laravel Resources and a custom `ApiResponser` trait for standardized success/error responses.

**Versioning**: API endpoints are prefixed with `v1` to allow for future versioning without breaking changes.

### Authentication & Security

**Laravel Sanctum**: Provides simple, secure API token authentication. Tokens are issued on login and can be revoked on logout.

**Request Validation**: Dedicated Form Request classes (`SearchTranslationRequest`, `StoreTranslationRequest`) handle validation with custom error messages.

### Performance & Caching

**Redis Caching**: Implemented at multiple levels:
- Translation search results cached for 5 minutes
- Export data cached for 10 minutes
- Cache invalidation on translation updates

**Pagination**: Search results are paginated with configurable page sizes (max 100 items).

### Data Management

**Eloquent ORM**: Clean, expressive database interactions with relationships and scopes.

**Migration-Based Schema**: Database schema managed through Laravel migrations for version control and environment consistency.

**Factory & Seeders**: Test data generation using Laravel factories and seeders.

### Documentation

**L5 Swagger**: Auto-generates OpenAPI 3.0 specification from PHP 8 attributes. Provides interactive documentation and client SDK generation.

**Attributes over Annotations**: Uses modern PHP 8 attributes (`#[OA\Get(...)]`) instead of docblock comments for cleaner, more maintainable code.

### Error Handling

**Custom Exceptions**: `TranslationException` and `TranslationExportException` for domain-specific errors.

**Structured Responses**: Consistent JSON response format with status, message, and data fields.

**HTTP Status Codes**: Appropriate status codes (200, 201, 400, 401, 404, 500) for different scenarios.

### Testing

**PHPUnit**: Unit and feature tests included for critical functionality.

**Test Database**: Separate SQLite database for testing to avoid affecting development data.

## Development

### Running Tests
```bash
php artisan test
```

### Code Quality
```bash
# Run Pint for code formatting
./vendor/bin/pint

# Run PHPStan for static analysis (if configured)
# php artisan code:analyse
```

### Generating Documentation
```bash
php artisan l5-swagger:generate
```

## Environment Variables

Key environment variables to configure:

```env
APP_NAME=Translation Management API
APP_ENV=local
APP_KEY=base64:your-app-key
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
