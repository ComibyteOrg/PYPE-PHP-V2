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

### [View Version 1 of Pype PHP Here](https://github.com/ComibyteOrg/Comibyte-PHP-Framework)

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

## 📂 Directory Structure

| Folder       | Level                | Purpose                                                                        |
| :----------- | :------------------- | :----------------------------------------------------------------------------- |
| `App/`       | **Framework Engine** | Contains the core logic of Pype. **Avoid editing files here.**                 |
| `Core/`      | **User Workspace**   | **This is where you build your app.** Controllers, Models, Middleware go here. |
| `Resources/` | View Layer           | Twig and PHP template files.                                                   |
| `assets/`    | Public Assets        | CSS, JS, Images, and fonts.                                                    |
| `Storage/`   | Persistence          | Logs, uploads, and temporary files.                                            |
| `routes/`    | Definitions          | `web.php` for all application endpoints.                                       |

> [!IMPORTANT] > **Architecture Rule**: Always place your Controllers in `Core/Controllers`, Models in `Core/Models`, and Middleware in `Core/Middleware`. The `App/` folder is reserved for the framework is internal mechanics.

---

### Route Parameters

Capture dynamic data from the URL:

```php
Route::get("/user/{id}/post/{post_id}", function ($id, $postId) {
    return "User: $id, Post: $postId";
});
```

### Named Routes

Generate URLs by name instead of hardcoding paths. The `url()` helper automatically resolves names defined via `->name()`.

```php
// routes/web.php
Route::get("/dashboard/profile", [ProfileController::class, 'show'])->name('profile');

// Usage in logic:
$url = url('profile'); // http://localhost:8000/dashboard/profile

// Usage in Twig:
<a href="{{ url('profile') }}">Profile</a>
```

---

## 🛠️ Mastering Middleware

Middleware protects your routes. Here is how to build and use them.

### Full Demo

**1. Create (`Core/Middleware/PremiumMiddleware.php`)**

```php
namespace Core\Middleware;

class PremiumMiddleware {
    public function handle($params, $next) {
        if (!auth()->is_premium) {
            flash('danger', 'Feature restricted.');
            return redirect(url('subscribe'));
        }
        return $next($params);
    }
}
```

**2. Register (`index.php`)**

```php
Route::registerMiddleware('premium', \Core\Middleware\PremiumMiddleware::class);
```

**3. Apply to Single Route**

```php
Route::get('/vip', [VipController::class, 'index'])->middleware('premium');
```

**4. Middleware Groups**
You can wrap multiple routes or apply multiple middlewares in a group:

```php
Route::group(['middleware' => ['auth', 'premium']], function() {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/settings', [SettingsController::class, 'show']);
});
```

### Built-in Middleware

| Alias        | Class                 | Description                                                   |
| :----------- | :-------------------- | :------------------------------------------------------------ |
| `auth`       | `AuthMiddleware`      | Redirects guests to login.                                    |
| `guest`      | `GuestMiddleware`     | Redirects authenticated users away from login/register pages. |
| `csrf`       | `CsrfMiddleware`      | Verifies CSRF tokens (Active by default on POST).             |
| `cors`       | `CorsMiddleware`      | Handles Cross-Origin Resource Sharing.                        |
| `rate_limit` | `RateLimitMiddleware` | Prevents API abuse (60 requests/min default).                 |
| `log`        | `LogMiddleware`       | Logs every incoming request to `app.log`.                     |

---

## 🔐 Building a Full Auth System

### 1. Registration & Login

```php
// Register
$userId = DB::table('users')->insert(['name' => 'Comibyte', 'email' => 'a@b.com']);

// Login
session('user_id', $userId);
flash('success', 'Logged in!');
redirect(url('dashboard'));
```

### 2. Email Verification

1. **Generate Token**: `$token = bin2hex(random_bytes(16));`
2. **Send URL**: `url('verify', ['token' => $token, 'email' => $email])`
3. **Handle Route**: Update `email_verified_at` when tokens match.

```php
// In RegisterController
$token = bin2hex(random_bytes(16));
$verifyUrl = url('verify', ['token' => $token, 'email' => $email]);

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
            return redirect(url('login'));
        }
        return $next($params);
    }
}
```

_Register it in `index.php`: `Route::registerMiddleware('verified', VerifiedMiddleware::class);`_

### 4. Logout

```php
logout(); // Safely clears sessions and remember_me cookies
redirect(url('login'));
```

---

## 🗄️ Database: The Fluent Query Builder

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

### Method Reference

| Method                        | Description                                | Example                                                  |
| :---------------------------- | :----------------------------------------- | :------------------------------------------------------- |
| `table($name)`                | Bind to a specific table.                  | `DB::table('users')`                                     |
| `select($cols)`               | Specify columns to return.                 | `->select(['id', 'name', 'email'])`                      |
| `where($col, $val, $op)`      | Add a standard WHERE clause.               | `->where('age', 18, '>')`                                |
| `orWhere($col, $val)`         | Add an OR WHERE clause.                    | `->orWhere('status', 'admin')`                           |
| `whereIn($col, $arr)`         | Check if a value is in an array.           | `->whereIn('id', [1, 2, 3])`                             |
| `whereNull($col)`             | Check if a column is NULL.                 | `->whereNull('deleted_at')`                              |
| `whereNotNull($col)`          | Check if a column is NOT NULL.             | `->whereNotNull('email_verified_at')`                    |
| `orderBy($col, $dir)`         | Sort results (ASC or DESC).                | `->orderBy('created_at', 'DESC')`                        |
| `groupBy($col)`               | Group results by column.                   | `->groupBy('category_id')`                               |
| `limit($n)`                   | Limit the number of records returned.      | `->limit(10)`                                            |
| `offset($n)`                  | Skip a number of records.                  | `->offset(20)`                                           |
| `join($table, $c1, $op, $c2)` | Perform an INNER JOIN.                     | `->join('posts', 'users.id', '=', 'posts.user_id')`      |
| `leftJoin(...)`               | Perform a LEFT JOIN.                       | `->leftJoin('profiles', 'users.id', '=', 'profiles.id')` |
| `get()`                       | Execute and return all results.            | `->get()`                                                |
| `first()`                     | Execute and return the first result.       | `->first()`                                              |
| `find($id)`                   | Shorthand for ID lookup.                   | `->find(5)`                                              |
| `pluck($col)`                 | Get an array of single column values.      | `->pluck('email')`                                       |
| `count()`                     | Get the total number of records.           | `->count()`                                              |
| `sum($col)`                   | Calculate the sum of a column.             | `->sum('price')`                                         |
| `avg($col)`                   | Calculate the average of a column.         | `->avg('rating')`                                        |
| `insert($data)`               | Add a new record (returns last ID).        | `->insert(['name' => 'John', 'email' => 'j@me.com'])`    |
| `update($data, $where)`       | Modify existing records.                   | `->update(['status' => 'banned'], ['id' => 1])`          |
| `delete($where)`              | Remove records from the database.          | `->delete(['id' => 1])`                                  |
| `raw($sql, $bindValues)`      | Execute a raw SQL query.                   | `->raw("SELECT * FROM users WHERE id = ?", [1])`         |
| `debug()`                     | Dumps the SQL and bindings before running. | `->debug()->get()`                                       |
| `transaction($fn)`            | Execute a safe DB transaction.             | `DB::transaction(function() { ... })`                    |

---

## 🎨 Templating with Twig

### Passing Data to Twig

Passing data from a controller to a view is simple. Use the second parameter of the `view()` helper.

```php
// Core/Controllers/ProfileController.php
public function show() {
    $user = auth();
    return view('profile', [
        'user' => $user,
        'title' => 'User Profile',
        'is_admin' => ($user->role === 'admin')
    ]);
}
```

**Accessing Data in Twig:**
Variables in the array are exposed as global-level properties in Twig.

```twig
<h1>{{ title }}</h1>
<p>Name: {{ user.name }}</p>

{% if is_admin %}
    <span class="badge">Administrator</span>
{% endif %}
```

### Inheritance (Layouts)

**layout.twig**

```twig
<!DOCTYPE html>
<html>
<head><title>{% block title %}My App{% endblock %}</title></head>
<body>
    <header><h1>Welcome to Pype</h1></header>
    <main>{% block content %}{% endblock %}</main>
    <footer>(c) 2025 comibyte</footer>
</body>
</html>
```

**home.twig**

```twig
{% extends 'layout.twig' %}
{% block title %}Dashboard{% endblock %}
{% block content %}
    <h2>Hello, {{ user.name }}</h2>
    <p>You are now logged in.</p>
{% endblock %}
```

### All Twig Functions

Twig can access all global helpers like `{{ auth().name }}`, `{{ url('home') }}`, and `{{ asset('img.png') }}`. Use `{{ getFlash('key') }}` to display alerts.

---

## 📝 Logging System

Pype includes a built-in Logger for tracking app behavior.

```php
use App\Logging\Logger;
Logger::info("User sign-in.", ['ip' => $_SERVER['REMOTE_ADDR']]);
Logger::error("API Timeout", ['service' => 'payment_gateway']);
```

_Logs are stored in `Storage/logs/app.log`._

---

## 🎯 The Core Folder (Your Logic)

The **`Core/`** folder is your primary workspace.

- **Controllers**: `Core/Controllers/`
- **Models**: `Core/Models/`
- **Middleware**: `Core/Middleware/`
- **Helpers**: `Core/Helpers/`

---

## 📧 Mailing System

Send emails via SMTP or the `log` driver (for local testing).

```php
$mailer = EmailService();
$mailer->sendEmail('to@me.com', 'Subject', '<h1>Body</h1>');

// Using templates
$mailer->sendTemplate('to@me.com', 'Welcome', 'welcome.php', ['name' => 'Comibyte']);
```

---

## 🔑 Social Authentication

1. **Routes**: `Route::socialAuth();`
2. **Env**: Fill `GOOGLE_CLIENT_ID`, etc.
3. **Links**: `<a href="/auth/google">Login</a>`

---

## 🌐 API Development

Pype simplifies building RESTful APIs.

### JSON Responses

```php
ApiResponse::success($data, "Fetched successfully");
ApiResponse::error("Unauthorized", 401);
```

### Resources

Wrap your models to transform data: `return UserResource::make($user);`.

---

## 📁 Files & Assets

The `upload()` helper manages secure file handling and returns the **unique filename** on success.

```php
$file = $_FILES['avatar'];

// upload(FILES_ATTR, TARGET_DIR, ALLOWED_EXTS)
$filename = upload($file, 'Storage/uploads/avatars', ['jpg', 'png']);

if ($filename) {
    // Save only the filename or full path to the DB
    DB::table('users')->where('id', auth()->id)->update([
        'avatar' => 'Storage/uploads/avatars/' . $filename
    ]);
}
```

---

## 🛡️ Security

- **CSRF**: Use `{{ csrf_field() | raw }}` in forms.
- **XSS**: All Twig outputs are escaped.
- **Spoofing**: Use `_method` hidden input for `DELETE`/`PUT`.

---

## ⚙️ Global Helper Reference

| Helper                | Description                                           | Usage Example                        |
| :-------------------- | :---------------------------------------------------- | :----------------------------------- |
| `auth()`              | Gets the currently authenticated user object.         | `auth()->name`                       |
| `check()`             | Boolean check if a user is logged in.                 | `if(check()) { ... }`                |
| `logout()`            | Clears sessions and remember_me cookies.              | `logout();`                          |
| `url($path, $params)` | Resolves a named route or absolute path.              | `url('profile', ['id' => 1])`        |
| `asset($path)`        | Generates a URL for the `/assets` folder.             | `asset('css/app.css')`               |
| `input($key)`         | Safely retrieves GET or POST data.                    | `input('username')`                  |
| `session($key, $val)` | Gets or sets a session variable.                      | `session('theme', 'dark')`           |
| `flash($key, $msg)`   | Sets a temporary session message.                     | `flash('success', 'Saved!')`         |
| `getFlash($key)`      | Retrieves and clears a flash message.                 | `{{ getFlash('success') }}`          |
| `view($view, $data)`  | Renders a Twig or PHP template.                       | `view('home', ['user' => $user])`    |
| `sanitize($str)`      | Extreme XSS protection for strings.                   | `sanitize(input('bio'))`             |
| `redirect($url)`      | Performs an HTTP redirect.                            | `redirect('/dashboard')`             |
| `dd($var)`            | "Dump and Die" for debugging.                         | `dd($array);`                        |
| `env($key, $default)` | Retrieves an environment variable.                    | `env('DB_TYPE', 'sqlite')`           |
| `slugify($str)`       | Converts a string into a URL-friendly slug.           | `slugify("Hello World")`             |
| `readingTime($c)`     | Estimates reading time in minutes.                    | `readingTime($content)`              |
| `excerpt($html, $l)`  | Plain-text summary of HTML content.                   | `excerpt($content, 100)`             |
| `old($key)`           | Retrieves data from the previous request for forms.   | `<input value="{{ old('email') }}">` |
| `base_path($p)`       | Returns the absolute path from the root.              | `base_path('config.php')`            |
| `storage_path($p)`    | Returns the absolute path within `/Storage`.          | `storage_path('logs/app.log')`       |
| `db_path()`           | Locates the SQLite database file.                     | `db_path()`                          |
| `upload($f, $d, $e)`  | Securely uploads a file with validation.              | `upload($_FILES['img'], 'uploads')`  |
| `EmailService()`      | Accesses the SMTP/Log mailing utility.                | `EmailService()->sendEmail(...)`     |
| `csrf_field()`        | Generates a hidden HTML field with a CSRF token.      | `{{ csrf_field() \| raw }}`          |
| `csrf_token()`        | Returns the raw CSRF token string.                    | `csrf_token()`                       |
| `csrf_enforce()`      | Manually validates CSRF for the current request.      | `csrf_enforce();`                    |
| `array_get($a, $k)`   | Deeply nested array access via dot notation.          | `array_get($user, 'meta.ip')`        |
| `set_alert($t, $m)`   | Sets a styled session alert.                          | `set_alert('danger', 'Error!')`      |
| `writetxt($f, $v)`    | Appends data to a text/CSV file.                      | `writetxt('stats.txt', ['visit'])`   |
| `deletetxt($f, $c)`   | Deletes lines matching a condition from a file.       | `deletetxt('logs.txt', '127.0.0.1')` |
| `returnJson($d, $s)`  | Outputs a JSON response and exits.                    | `returnJson(['id' => 1])`            |
| `method()`            | Returns the current HTTP request method (e.g., POST). | `if(method() === 'POST') { ... }`    |
| `app_path()`          | Returns the absolute path to the `/App` folder.       | `app_path('Helper/Helper.php')`      |
| `getCSRFName()`       | Returns the name of the CSRF token in the request.    | `getCSRFName()`                      |
| `csrf_verify($t)`     | Manually verifies a specific token.                   | `csrf_verify($token)`                |

---

## 📜 License

Developed and maintained by **comibyte**.

## P Y P E - P H P - V 2
