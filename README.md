# Translation Management Service

A complete Docker-based Translation Management API built with Laravel 12, featuring authentication, search, export capabilities, and interactive API documentation.

## 🚀 Quick Start

```bash
git clone https://github.com/kentdayrit/translation-management-service-tech-exam.git
cd translation-management-service
docker-compose up -d
```

That's it! The application will be running at `http://localhost:8000` with API documentation at `http://localhost:8000/docs`.

## 📋 What's Included

- **Laravel API** (`laravel-api/`) - RESTful translation management API
- **MySQL Database** - Persistent data storage
- **Redis** - Caching and session management
- **Nginx** - Web server and reverse proxy
- **Docker Compose** - Complete containerized environment

## 🏗️ Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Nginx         │    │   Laravel API   │    │   MySQL         │
│   (Port 80)     │◄──►│   (Port 8000)   │◄──►│   (Port 3306)   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                              │
                              ▼
                       ┌─────────────────┐
                       │   Redis         │
                       │   (Port 6379)   │
                       └─────────────────┘
```

## 🔧 Services

| Service | Port | Description |
|---------|------|-------------|
| **API** | `http://localhost/api` | Laravel REST API |
| **Docs** | `http://localhost/docs` | Swagger UI Documentation |
| **MySQL** | `localhost:3306` | Database |
| **Redis** | `localhost:6379` | Cache & Sessions |

## 📚 API Features

- 🔐 **Authentication** - Laravel Sanctum token-based auth
- 🔍 **Search & Filter** - Advanced translation queries
- 📤 **Export** - Bulk translation export by locale
- 🚀 **Caching** - Redis-backed performance optimization
- 📖 **Documentation** - Auto-generated OpenAPI/Swagger docs

## 🛠️ Development

### Prerequisites
- Docker & Docker Compose
- Git

### Setup Steps

1. **Clone Repository**
   ```bash
   git clone https://github.com/kentdayrit/translation-management-service-tech-exam.git
   cd translation-management-service
   ```

2. **Environment Configuration**
   ```bash
   cp laravel-api/.env.example laravel-api/.env
   # Edit laravel-api/.env if needed (Docker defaults should work)
   ```

3. **Start Services**
   ```bash
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

### Useful Commands

```bash
# View logs
docker-compose logs -f

# Access Laravel container
docker-compose exec api bash

# Run tests
docker-compose exec api php artisan test

# Generate API docs
docker-compose exec api php artisan l5-swagger:generate

# Stop services
docker-compose down
```

## 📖 API Documentation

Once running, visit `http://localhost:8000/docs` for interactive API documentation including:

- Authentication endpoints (`/api/v1/login`, `/api/v1/logout`)
- Translation management (`/api/v1/translations/*`)
- Request/response examples
- Test interface

## 🔧 Configuration

### Environment Variables

Key settings in `laravel-api/.env`:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=translation_api
DB_USERNAME=root
DB_PASSWORD=root

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# App
APP_NAME="Translation Management API"
APP_URL=http://localhost
```

### Docker Services

- **api**: Laravel application (PHP 8.2, Composer)
- **mysql**: MySQL 8.0 database
- **redis**: Redis 7 for caching
- **nginx**: Nginx web server with custom config

## 🧪 Testing

```bash
# Run all tests
docker-compose exec api php artisan test

# Run specific test file
docker-compose exec api php artisan test tests/Feature/TranslationApiTest.php
```

## 📁 Project Structure

```
translation-management-service/
├── docker-compose.yml          # Docker services configuration
├── Dockerfile                  # Laravel container build
├── docker/
│   └── nginx/
│       └── conf.d/
│           └── default.conf    # Nginx configuration
├── laravel-api/                # Laravel application
│   ├── app/                    # Application code
│   ├── config/                 # Configuration files
│   ├── database/               # Migrations, seeders
│   ├── routes/                 # API routes
│   ├── storage/api-docs/       # Generated API docs
│   ├── tests/                  # Test files
│   ├── composer.json           # PHP dependencies
│   └── README.md               # Laravel-specific docs
└── README.md                   # This file
```

## 🚀 Deployment

For production deployment:

1. Update environment variables for production
2. Configure proper SSL certificates
3. Set up database backups
4. Configure monitoring and logging
5. Use production Docker images

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

MIT License - see [LICENSE](LICENSE) file for details.

---

**Built with ❤️ using Laravel 12, Docker, and modern PHP practices**