Here is the **README.md** file with the specific seeder artisan commands included, properly formatted and ready to use:

````markdown
# Laravel Multi-Role Publishing API

## Installation & Setup

### 1. Clone the repository
```bash
git clone https://github.com/AhmedDip/BVS-Backend-Dev-Task.git
cd BVS-Backend-Dev-Task
```

### 2. Install dependencies

```bash
composer install
```

### 3. Copy `.env` file and generate app key

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure `.env`

Update database and other settings in `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 5. Run migrations and seeders

```bash
php artisan migrate
```

To seed roles, permissions, and users, run these commands:

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=UsersTableSeeder
```

Alternatively, to run all seeders registered in `DatabaseSeeder`:

```bash
php artisan db:seed
```

Seeded users include:

* Admin: [admin@bvs.com](mailto:admin@bvs.com) / `12345678`
* Editor: [editor@bvs.com](mailto:editor@bvs.com) / `editor`
* Author: [author@bvs.com](mailto:author@bvs.com) / `author`

### 6. Start development server

```bash
php artisan serve
```

By default, the app runs at [http://localhost:8000](http://localhost:8000)

---

## Authentication & Token Flow

* **Register:** `POST /api/register`
* **Login:** `POST /api/login`
* **Logout:** `POST /api/logout` (requires Bearer token)

After login, use the returned token in the `Authorization` header:

```
Authorization: Bearer YOUR_TOKEN
```

---

## API Endpoints Overview

### User Management (Admin only)

* `GET /api/users` — List all users
* `POST /api/users/{id}/assign-role` — Assign role to a user
* `GET /api/profile` — Get own profile

### Articles

* `GET /api/articles` — List published articles
* `GET /api/articles/mine` — List own articles
* `POST /api/articles` — Create article (authors)
* `PUT /api/articles/{id}` — Update own article
* `DELETE /api/articles/{id}` — Delete article (admin only)
* `PATCH /api/articles/{id}/publish` — Publish article (editor/admin)

---

## Rate Limiting

* 20 requests per minute per user (`throttle:20,1`)
* Exceeding the limit returns HTTP 429 with message `"Too Many Attempts."`

---

## Seeded Users Credentials

| Role   | Email                                   | Password |
| ------ | --------------------------------------- | -------- |
| Admin  | [admin@bvs.com](mailto:admin@bvs.com)   | 12345678 |
| Editor | [editor@bvs.com](mailto:editor@bvs.com) | editor   |
| Author | [author@bvs.com](mailto:author@bvs.com) | author   |

---

## Running Tests

Run feature tests with:

```bash
php artisan test
```

