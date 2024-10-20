
# User Service

This is the second part of a project implementing a **user service** using Laravel. The service handles user-related features,  profile management. It supports microservice-based architectures.

## Features

- Profile Management
- API Endpoints for user services

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/doaakhalid/user-service.git
   ```
2. Navigate to the project directory:
   ```bash
   cd user-service
   ```
3. Install dependencies:
   ```bash
   composer install
   ```
4. Configure environment variables by copying `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```
5. Generate application key:
   ```bash
   php artisan key:generate
   ```
6. Run migrations:
   ```bash
   php artisan migrate
   ```

## Usage

To start the service, run:
```bash
php artisan serve
```

The API will be accessible at `http://localhost:8000`.

## API Endpoints


- `GET /api/user`: Fetch the authenticated user's profile (requires token).

## Running Tests

This project includes unit tests for core functionalities such as user registration, login, and profile management.

### Steps to Run the Tests:

1. Make sure your test environment is configured properly by creating a `.env.testing` file if needed.

2. Run the tests using the following command:
   ```bash
   php artisan test
   ```
   or
   ```bash
   vendor/bin/phpunit
   ```

These commands will execute all the tests under the `tests` directory to ensure the application's functionalities work as expected.
