# PersonalBoard

PersonalBoard is a task board application built with Laravel, Vue, Tailwind CSS, and MySQL. The local development environment is containerized with Docker Compose and includes Nginx and phpMyAdmin.

## Technology stack

- Laravel 13
- PHP 8.4 (Docker)
- Vue 3 using the Options API
- Tailwind CSS 4
- Vite 8
- Axios
- Vue Draggable
- MySQL 8.4
- phpMyAdmin
- Nginx

## How the application runs

Docker Compose runs the backend services:

- `app`: Laravel running on PHP-FPM
- `nginx`: serves Laravel on port `8080`
- `mysql`: stores application data on port `3306`
- `phpmyadmin`: provides database administration on port `8081`

Vite runs directly on the host machine on port `5173`. During development, open the Laravel URL at `http://localhost:8080`; Laravel loads the frontend assets from the Vite development server automatically.

## Prerequisites

Install the following before starting:

1. [Git](https://git-scm.com/)
2. [Docker Desktop](https://www.docker.com/products/docker-desktop/)
3. Docker Compose v2, included with current Docker Desktop releases
4. [Node.js](https://nodejs.org/) `20.19` or newer, or `22.12` or newer
5. npm, included with Node.js

Local PHP, Composer, MySQL, and Nginx installations are not required because they run in Docker.

Make sure these ports are available:

| Port | Service |
| --- | --- |
| `8080` | PersonalBoard |
| `8081` | phpMyAdmin |
| `3306` | MySQL |
| `5173` | Vite |

Verify the required tools:

```bash
git --version
docker --version
docker compose version
node --version
npm --version
```

## First-time setup

### 1. Open the project

```bash
cd PersonalBoard
```

### 2. Create the environment file

```bash
cp .env.example .env
```

The Docker entrypoint also creates `.env` automatically when it is missing. Creating it explicitly makes local configuration easier to review and customize.

The default development database configuration is:

```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=personalboard
DB_USERNAME=personalboard
DB_PASSWORD=personalboard
```

Use `mysql` as `DB_HOST` inside Laravel because that is the Docker Compose service name. Applications running directly on the host should connect through `127.0.0.1:3306` instead.

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Build and start Docker services

Make sure Docker Desktop is running, then execute:

```bash
docker compose up -d --build
```

Confirm that all services are running:

```bash
docker compose ps
```

The MySQL service should report a healthy status.

### 5. Run database migrations

```bash
docker compose exec app php artisan migrate
```

To run the database seeder as well:

```bash
docker compose exec app php artisan db:seed
```

### 6. Start Vite

Run this command in a separate terminal and leave it running while developing:

```bash
npm run dev
```

### 7. Open the application

- Application: [http://localhost:8080](http://localhost:8080)
- phpMyAdmin: [http://localhost:8081](http://localhost:8081)
- Vite development server: [http://localhost:5173](http://localhost:5173)

Open the application at port `8080`, not port `5173`. Port `5173` only serves frontend assets and hot module replacement.

## Daily development

Start the backend containers:

```bash
docker compose up -d
```

Start the frontend development server in another terminal:

```bash
npm run dev
```

When finished, stop Vite with `Ctrl+C` and stop the containers:

```bash
docker compose down
```

## phpMyAdmin

Open [http://localhost:8081](http://localhost:8081) and use:

```text
Server: mysql
Username: personalboard
Password: personalboard
Database: personalboard
```

For unrestricted local administration, the root credentials are:

```text
Username: root
Password: root
```

These credentials are intended only for local development. Use secure secrets outside the local environment.

## Common Docker commands

```bash
# Start all services
docker compose up -d

# Build images and start all services
docker compose up -d --build

# Show service status
docker compose ps

# Follow logs from all services
docker compose logs -f

# Follow Laravel/PHP logs
docker compose logs -f app

# Follow Nginx logs
docker compose logs -f nginx

# Restart all services
docker compose restart

# Restart only Laravel
docker compose restart app

# Stop and remove containers
docker compose down

# Rebuild the application image without cache
docker compose build --no-cache app
```

### Delete all local database data

The following command removes the containers and the persistent MySQL volume. This permanently deletes the local PersonalBoard database:

```bash
docker compose down -v
```

## Laravel and Composer commands

Laravel requires PHP 8.3 or newer. Run Artisan and Composer commands inside the `app` container so they use PHP 8.4:

```bash
# Run migrations
docker compose exec app php artisan migrate

# Show migration status
docker compose exec app php artisan migrate:status

# Roll back the latest migration batch
docker compose exec app php artisan migrate:rollback

# Recreate all tables and run seeders (deletes existing data)
docker compose exec app php artisan migrate:fresh --seed

# Clear Laravel caches
docker compose exec app php artisan optimize:clear

# List application routes
docker compose exec app php artisan route:list

# Open Laravel Tinker
docker compose exec app php artisan tinker

# Run tests
docker compose exec app php artisan test --compact

# Format PHP code
docker compose exec app vendor/bin/pint

# Install PHP dependencies
docker compose exec app composer install

# Add a Composer package
docker compose exec app composer require vendor/package
```

## Frontend commands

Run frontend commands from the project directory on the host machine:

```bash
# Install exact dependencies from package-lock.json
npm ci

# Start Vite with hot module replacement
npm run dev

# Create production assets
npm run build

# Add a runtime dependency
npm install package-name

# Add a development dependency
npm install --save-dev package-name
```

## Running without the Vite development server

Compile the production assets:

```bash
npm run build
```

After the build finishes, Laravel serves the generated assets from `public/build`, so `npm run dev` does not need to remain running.

## API routes

All routes in `routes/api.php` automatically use the `/api` prefix.

### Boards

| Method | URL | Purpose |
| --- | --- | --- |
| `GET` | `/api/boards` | List boards |
| `POST` | `/api/boards` | Create a board |
| `POST` | `/api/boards/update` | Update a board |
| `DELETE` | `/api/boards/{board_id}` | Delete a board |

### Tasks

| Method | URL | Purpose |
| --- | --- | --- |
| `GET` | `/api/tasks/{board_id}` | List a board's tasks using cursor pagination |
| `POST` | `/api/tasks` | Create a task |
| `POST` | `/api/tasks/update` | Update a task |
| `POST` | `/api/tasks/reorder` | Reorder tasks |
| `DELETE` | `/api/tasks/{task_id}` | Delete a task |

The Vue application uses the Axios client in `resources/js/api.js`. Its base URL is `/api`:

```js
import api from '../api';

const response = await api.get('/boards');
```

## Project structure

```text
app/                    Laravel application code
bootstrap/              Laravel bootstrap and generated cache
config/                 Laravel configuration
database/migrations/    Database schema migrations
database/seeders/       Database seeders
docker/                 Docker entrypoint and Nginx configuration
public/                 Web root and compiled frontend assets
resources/js/           Vue application and Axios client
resources/js/components Vue components
resources/css/          Tailwind entry stylesheet
routes/api.php          JSON API routes
routes/web.php          Web routes
tests/                  PHPUnit tests
compose.yaml            Docker Compose services
Dockerfile              PHP application image
vite.config.js          Vue, Tailwind, and Vite configuration
```

## Troubleshooting

### Port 5173 is already in use

Find the existing process:

```bash
lsof -nP -iTCP:5173 -sTCP:LISTEN
```

Stop the process using the PID displayed by `lsof`, then restart Vite:

```bash
kill <PID>
npm run dev
```

### The page is blank or frontend changes do not appear

Make sure Vite is running:

```bash
npm run dev
```

Alternatively, compile the frontend assets:

```bash
npm run build
```

Then refresh `http://localhost:8080`.

### Laravel returns an internal server error

Check the Laravel log:

```bash
tail -f storage/logs/laravel.log
```

Check the application container logs and clear generated caches:

```bash
docker compose logs app
docker compose exec app php artisan optimize:clear
```

### Database connection fails

Confirm MySQL is healthy:

```bash
docker compose ps
docker compose logs mysql
```

Verify that `DB_HOST=mysql` is set in `.env`, then restart the application container:

```bash
docker compose restart app
```

### Dependencies are missing

```bash
docker compose exec app composer install
npm install
```

## Resetting the environment

To rebuild containers while keeping database data:

```bash
docker compose down
docker compose up -d --build
docker compose exec app php artisan migrate
```

To completely reset the environment, including the database:

```bash
docker compose down -v
docker compose up -d --build
docker compose exec app php artisan migrate --seed
npm install
npm run dev
```
