# SLMP Tech Exam - JSONPlaceholder Laravel API

## Project Overview

Implement a Laravel-based backend that imports JSONPlaceholder data into a normalized database and exposes it via authenticated REST API.

## Requirements implemented

- `fetch:jsonplaceholder` artisan command fetches users, posts, comments, albums, photos, todos
- Eloquent models + relations + migrations
- Auth via API token middleware (`api.token`)
- REST endpoints under `/api/*`
- Docker support with `docker-compose`

## Local setup

1. Clone repo
   - `git clone <remote-url> .`
2. Copy env
   - `cp .env.example .env`
3. (Optional) Set DB credentials for local MySQL if not using Docker

## Running with Docker

1. `docker compose up --build`
2. Wait until server runs on `http://localhost:8000`
3. In another terminal: `docker compose exec app php artisan fetch:jsonplaceholder`

## Running manually (without Docker)

1. Install dependencies: `composer install`
2. Generate key: `php artisan key:generate`
3. Migrate + seed: `php artisan migrate --seed`
4. Fetch external data: `php artisan fetch:jsonplaceholder`
5. Serve: `php artisan serve`

## Auth

1. Login endpoint:
   - POST `http://localhost:8000/api/login`
   - Body: `{ "email": "admin@example.com", "password": "secret" }`
   - Response: `{ "token": "..." }`
2. Protected endpoints require header:
   - `Authorization: Bearer <token>`

## API Endpoints

- GET `/api/users`
- GET `/api/posts`
- GET `/api/comments`
- GET `/api/albums`
- GET `/api/photos`
- GET `/api/todos`
- GET `/api/all`

## Postman testing

1. Call `POST /api/login` to get token
2. Use Bearer token in headers and call any GET endpoint.

## Notes

- The command uses `updateOrCreate` to prevent duplicates.
- Database is normalized with foreign keys and external IDs.
- Unit tests can be added under `tests/Feature`.
