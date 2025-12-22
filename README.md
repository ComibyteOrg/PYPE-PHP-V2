# Pype PHP V2 — The Professional Framework

<div align="center">
  <img src="https://imgs.search.brave.com/a2QJ4QGpzGpXeDGHk1c-pL3FdZ-v47YnUIxeu4pjCe4/rs:fit:500:0:1:0/g:ce/aHR0cHM6Ly9vbHV3/YWRpbXUtYWRlZGVq/aS53ZWIuYXBwL2lt/YWdlcy9sb2dvLnBu/Zw" alt="Comibyte Welcome Page" width="300">
  <br>
  <p>
    <img src="https://img.shields.io/badge/PHP-8.2%2B-blue?style=for-the-badge&logo=php" alt="PHP Version">
    <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
    <img src="https://img.shields.io/badge/Status-In%20Development-orange?style=for-the-badge" alt="Status">
  </p>
</div>

Pype PHP V2 is a lightweight, expressive, and powerful PHP framework designed for speed and simplicity. It provides a Laravel-like experience with a fluent Query Builder, Twig templating, Social Authentication, and a robust Mailing system.

### View The First Version of Pype PHP [Here](https://github.com/ComibyteOrg/Comibyte-PHP-Framework)

---

## 📑 Table of Contents

- [🚀 Quick Start](#-quick-start)
- [📂 Directory Structure](#-directory-structure)
- [🛤️ Routing Deep Dive](#-routing-deep-dive)
  - [Route Parameters](#route-parameters)
  - [Named Routes](#named-routes)
- [🛠️ Mastering Middleware](#️-mastering-middleware)
- [🔐 Building a Full Auth System](#-building-a-full-auth-system)
  - [Registration & Login](#registration--login)
  - [Email Verification](#email-verification)
  - [Verified Middleware](#verified-middleware)
- [🗄️ Database: The Fluent Query Builder](#-database-the-fluent-query-builder)
  - [Raw SQL Setup](#raw-sql-setup)
  - [Method Reference](#method-reference)
- [🎨 Templating with Twig](#-templating-with-twig)
  - [Inheritance (Layouts)](#inheritance-layouts)
  - [All Twig Functions](#all-twig-functions)
- [📝 Logging System](#-logging-system)
- [🎯 The Core Folder (Your Logic)](#-the-core-folder-your-logic)
- [📧 Mailing System](#-mailing-system)
- [🔑 Social Authentication](#-social-authentication)
- [🌐 API Development](#-api-development)
- [📁 Files & Assets](#-files--assets)
- [🛡️ Security](#-security)
- [⚙️ Global Helper Reference](#-global-helper-reference)

---

## 🚀 Quick Start

### 1. Requirements

- PHP 8.2+
- Composer
- Node JS 22.16.0+
- Git Terminal
- Extensions: `pdo`, `mbstring`, `openssl`, `curl`. `Twig Language 2`

### 2. Installation

```bash
git clone https://github.com/ComibyteOrg/PYPE-PHP-V2.git
cd PYPE-PHP-V2
composer install
cp .env.example .env
```

### 3. Setup Database

```bash
# Automatically create SQLite DB and users table
php migrations/create_users_table.php
```

---

## 🗄️ Database: The Fluent Query Builder

Access your database using the powerful `DB` class.

### 1. Database Schema

Pype requires a `users` table for authentication.

| Column              | Type      | Description                                    |
| :------------------ | :-------- | :--------------------------------------------- |
| `id`                | INT       | Primary Key.                                   |
| `name`              | VARCHAR   | Full name of the user.                         |
| `email`             | VARCHAR   | Unique email address.                          |
| `avatar`            | VARCHAR   | URL to profile image (useful for Social Auth). |
| `provider`          | VARCHAR   | Auth provider (google, github, facebook).      |
| `provider_id`       | VARCHAR   | Unique ID from the provider.                   |
| `email_verified_at` | TIMESTAMP | Date of verification.                          |

### 2. Raw SQL Setup (Manual)

If you aren't using the migration script, use these queries to set up your database:

<details>
<summary>SQLite Query</summary>

```sql
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    avatar TEXT,
    provider TEXT,
    provider_id TEXT,
    email_verified_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

</details>

<details>
<summary>MySQL Query</summary>

```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    avatar VARCHAR(500),
    provider VARCHAR(50),
    provider_id VARCHAR(255),
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

</details>

<details>
<summary>PostgreSQL Query</summary>

```sql
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    avatar VARCHAR(500),
    provider VARCHAR(50),
    provider_id VARCHAR(255),
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

</details>

### 3. Method Reference

| Method                        | Purpose                                    |
| :---------------------------- | :----------------------------------------- |
| `table($name)`                | Bind to a specific table.                  |
| `select($cols)`               | Specify columns to return.                 |
| `where($col, $val, $op)`      | Add a standard WHERE clause.               |
| `orWhere($col, $val)`         | Add an OR WHERE clause.                    |
| `whereIn($col, $arr)`         | Check if a value is in an array.           |
| `whereNull($col)`             | Check if a column is NULL.                 |
| `whereNotNull($col)`          | Check if a column is NOT NULL.             |
| `orderBy($col, $dir)`         | Sort results (`ASC` or `DESC`).            |
| `groupBy($col)`               | Group results by column.                   |
| `limit($n)`                   | Limit the number of records returned.      |
| `offset($n)`                  | Skip a number of records.                  |
| `join($table, $c1, $op, $c2)` | Perform an INNER JOIN.                     |
| `leftJoin(...)`               | Perform a LEFT JOIN.                       |
| `get()`                       | Execute and return all results.            |
| `first()`                     | Execute and return the first result.       |
| `find($id)`                   | Shorthand for `where('id', $id)->first()`. |
| `pluck($col)`                 | Get an array of single column values.      |
| `count()`                     | Get the total number of records.           |
| `sum($col)`                   | Calculate the sum of a column.             |
| `avg($col)`                   | Calculate the average of a column.         |
| `insert($data)`               | Add a new record (returns last ID).        |
| `update($data, $where)`       | Modify existing records.                   |
| `delete($where)`              | Remove records from the database.          |
| `raw($sql, $bindValues)`      | Execute a raw SQL query.                   |
| `debug()`                     | Dumps the SQL and bindings before running. |
| `transaction($fn)`            | Execute a safe DB transaction.             |

---

## 🔐 Building a Full Auth System

Pype makes user management seamless.

### 1. Registration & Login

Use the `DB` class to store user data and the `session()` helper for state.

```php
// Registration
$userId = DB::table('users')->insert([
    'name' => input('name'),
    'email' => input('email'),
    'created_at' => date('Y-m-d H:i:s')
]);

// Login
session('user_id', $user['id']);
flash('success', 'Welcome back!');
redirect('/dashboard');
```

### 2. Email Verification

Pype simplifies the verification flow using `EmailService`.

**The Flow:**

1. Generate a token on register.
2. Send a link with the token.
3. Update `email_verified_at` on click.

```php
// In RegisterController
$token = bin2hex(random_bytes(16));
$verifyUrl = url('/verify', ['token' => $token, 'email' => $email]);

EmailService()->sendEmail($email, "Confirm email", "URL: $verifyUrl");

// In VerifyController
Route::get('/verify', function() {
    $token = input('token');
    $email = input('email');

    // Validate token and update user
    DB::table('users')->where('email', $email)->update([
        'email_verified_at' => date('Y-m-d H:i:s')
    ]);
    flash('success', 'Email confirmed!');
});
```

### 3. Verified User Middleware

Block unverified users from sensitive routes. Create `Core/Middleware/VerifiedMiddleware.php`:

```php
namespace Core\Middleware;

class VerifiedMiddleware {
    public function handle($params, $next) {
        $user = auth();
        if (!$user || !isset($user->email_verified_at)) {
            flash('warning', 'Please verify your email address first.');
            redirect('/login');
        }
        return $next($params);
    }
}
```

_Register it in `index.php`: `Route::registerMiddleware('verified', VerifiedMiddleware::class);`_

### 4. Logout

```php
logout(); // Safely clears sessions and remember_me cookies
redirect('/login');
```

---

## 🔑 Social Authentication (Ultra Easy)

Pype handles Google, GitHub, and Facebook authentication with zero boilerplate.

### 1. The 1-Line Setup

Just add this to your `routes/web.php`:

```php
Route::socialAuth(); // Automatically sets up /auth/{provider} and /auth/{provider}/callback
```

### 2. Configure Credentials

Add your API keys to the `.env` file:

```ini
GOOGLE_CLIENT_ID=your_id
GOOGLE_CLIENT_SECRET=your_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### 3. Use in Templates

Link directly to the provider. No controllers needed!

```html
<a href="/auth/google" class="btn">Login with Google</a>
<a href="/auth/github" class="btn">Login with GitHub</a>
```

The framework handles the redirect, state verification, user creation/lookup, and session management automatically!

---

## 🛠️ Mastering Middleware

Middleware protects your routes. Here are the built-ins:

| Alias        | Class                 | Description                                                   |
| :----------- | :-------------------- | :------------------------------------------------------------ |
| `auth`       | `AuthMiddleware`      | Redirects guests to login.                                    |
| `guest`      | `GuestMiddleware`     | Redirects authenticated users away from login/register pages. |
| `csrf`       | `CsrfMiddleware`      | Verifies CSRF tokens (Active by default on POST).             |
| `cors`       | `CorsMiddleware`      | Handles Cross-Origin Resource Sharing.                        |
| `rate_limit` | `RateLimitMiddleware` | Prevents API abuse (60 requests/min default).                 |
| `log`        | `LogMiddleware`       | Logs every incoming request to `app.log`.                     |

---

## 🎨 Templating with Twig

### Inheritance (Layouts)

**layout.twig**

```twig
<!DOCTYPE html>
<html>
<head><title>{% block title %}My App{% endblock %}</title></head>
<body>
    <main>{% block content %}{% endblock %}</main>
</body>
</html>
```

**home.twig**

```twig
{% extends 'layout.twig' %}
{% block content %}
    <h1>Welcome, {{ user.name }}</h1>
{% endblock %}
```

---

## 📝 Logging System

```php
use App\Logging\Logger;

Logger::info("User logged in", ['id' => 123]);
Logger::error("Database failed", ['error' => $e->getMessage()]);
```

_Logs are stored in `Storage/logs/app.log`._

---

## 🎯 The Core Folder (Your Logic)

Use the **`Core/`** directory for your custom code:

- **Controllers**: `Core\Controllers\`
- **Models**: `Core\Models\`
- **Middleware**: `Core\Middleware\`

---

## 📧 Mailing System

```php
$mailer = EmailService();
$mailer->sendEmail('user@example.com', 'Hello', '<h1>Body</h1>');
```

---

## 🛡️ Security

- **CSRF**: Use `{{ csrf_field() | raw }}` in every form.
- **XSS**: All Twig outputs are escaped. Use `sanitize($input)` manually.
- **Method Spoofing**: Use `<input type="hidden" name="_method" value="DELETE">`.

---

## ⚙️ Global Helper Reference

| Function              | Description                       |
| :-------------------- | :-------------------------------- |
| `view($v, $data)`     | Renders a template.               |
| `auth()`              | Access the logged-in User object. |
| `check()`             | Is the user logged in? (Boolean). |
| `logout()`            | Safely logs out.                  |
| `url($path)`          | Resolves URLs.                    |
| `asset($path)`        | Links to `/assets` folder.        |
| `input($key)`         | Safe `POST`/`GET` retrieval.      |
| `upload($file, $dir)` | Advanced file upload tool.        |
| `dd($var)`            | Dump and Die debugging.           |

---

## 📜 License

Developed and maintained by **comibyte**.

# P Y P E - P H P - V 2
