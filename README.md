# Short link service

A RESTful URL shortening service built with Laravel, PHP, and Docker.

The project is fully **Dockerized using Laravel Sail** for consistent, easy, and reproducible local development.

## Prerequisites

- **Docker & Docker Compose**
- **Git**
- **PHP**
- **Composer** (only for initial setup)

> All required services (PHP, MySQL, Redis, etc.) are provided by Sail containers.

## Installation & Setup

Follow the steps below to set up the project:

1. **Clone the repository**

    ```bash
    git clone https://github.com/amirali-hossaini/short-link-service.git
    cd short-link-service
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Copy the environment file**

    ```bash
    cp .env.example .env
    ```

4. **Generate the application key**

    ```bash
    php artisan key:generate
    ```

5. **Start Sail containers** (with build for first-time setup):

    ```bash
    ./vendor/bin/sail up -d --build
    ```

6. **Run database migrations and seed initial data**

    ```bash
    ./vendor/bin/sail artisan migrate --seed
    ```

7. **Optional: Run tests**

    ```bash
    ./vendor/bin/sail artisan test
    ```

## Application URL

Your application will be accessible at:

- [http://localhost:85](http://localhost:85)

## Troubleshooting

### SQLite permission issues

If you encounter errors like:

```text
attempt to write a readonly database
```

or:

```text
storage/logs/laravel.log could not be opened
```

run:

```bash
sudo chmod -R 777 storage bootstrap/cache database
```
