# trucquy-store

Monorepo với BE (Laravel 11) và FE (Vue 3) tách biệt, chạy qua Docker.

## Cấu trúc

```
trucquy-store/
├── be/                  # Laravel 11 API Backend
│   ├── Dockerfile
│   └── ...
├── fe/                  # Vue 3 Frontend
│   ├── Dockerfile
│   ├── nginx.conf
│   └── ...
├── docker/
│   └── nginx/
│       └── be.conf      # Nginx config cho BE API
├── docker-compose.yml
├── .env
└── .env.example
```

## Services

| Service      | URL                        | Mô tả                     |
|--------------|----------------------------|---------------------------|
| FE (Vue 3)   | http://localhost:3000      | Frontend                  |
| BE (Laravel) | http://localhost:8000      | Backend API               |
| MySQL        | localhost:3306             | Database                  |
| Redis        | localhost:6379             | Cache / Queue             |
| phpMyAdmin   | http://localhost:8080      | DB GUI (profile: tools)   |

## Khởi động

### 1. Chuẩn bị .env

```bash
cp .env.example .env
# Chỉnh sửa thông tin DB, password,...
```

### 2. Build & chạy

```bash
# Build và start tất cả services
docker compose up -d --build

# Với phpMyAdmin
docker compose --profile tools up -d --build
```

### 3. Migrate database (lần đầu)

```bash
docker compose exec be php artisan migrate
docker compose exec be php artisan key:generate
```

## Development (không dùng Docker)

### Backend

```bash
cd be
cp .env.example .env
composer install
php artisan key:generate
php artisan serve
```

### Frontend

```bash
cd fe
npm install
npm run dev
```

## Tech Stack

### Backend (BE)
- **Laravel 11** (PHP 8.3)
- **MySQL 8**
- **Redis 7**

### Frontend (FE)
- **Vue 3** + TypeScript
- **Vue Router 4**
- **Pinia**
- **Vite**
- **ESLint**

## Ports mặc định

- FE dev: `http://localhost:5173`
- BE dev: `http://localhost:8000`
- FE production (Docker): `http://localhost:3000`
- BE production (Docker): `http://localhost:8000`

