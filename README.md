# Task Management API

A Laravel-based REST API for task management with image upload capabilities and token-based authentication.

## Requirements

- PHP >= 8.2
- Composer
- MySQL
- XAMPP (or similar local development environment)

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
 ```

2. Install dependencies:
```bash
composer install
 ```

3. Copy .env.example to .env :
```bash
cp .env.example .env
 ```

4. Configure your .env file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=
 ```

5. Generate application key:
```bash
php artisan key:generate
 ```

6. Run migrations:
```bash
php artisan migrate
 ```

7. Create storage link for public file access:
```bash
php artisan storage:link
 ```

## API Endpoints
### Authentication
- POST /api/register - Register new user
- POST /api/login - Login user
### Tasks
- GET /api/tasks - Get all tasks
- POST /api/tasks - Create new task
- GET /api/tasks/{id} - Get specific task
- PUT /api/tasks/{id} - Update task
- DELETE /api/tasks/{id} - Delete task
## Authentication
The API uses Laravel Sanctum for token-based authentication. Include the token in your requests:

```http
Authorization: Bearer your_token_here
Accept: application/json
 ```
## Task Creation Example
```json
    {
        "title": "Task Title",
        "description": "Task Description",
        "status": "pending",
        "due_date": "2024-04-01",
        "images": [file1, file2]  // Optional
    }
 ```