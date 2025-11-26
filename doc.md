# OxygenFramework 2.0 - Complete Documentation

**Developer:** Redwan Aouni (aouniradouan@gmail.com)  
**Version:** 2.0.0  
**Copyright:** 2024 - All Rights Reserved

---

## Table of Contents

1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Routing](#routing)
5. [Controllers](#controllers)
6. [Models & Database](#models--database)
7. [Migrations](#migrations)
8. [Authentication](#authentication)
9. [Security](#security)
10. [Forms & Validation](#forms--validation)
11. [Views & Templates](#views--templates)
12. [Storage System](#storage-system)
13. [Template Helpers](#template-helpers)
14. [Services](#services)
15. [API Development](#api-development)
16. [Python Integration](#python-integration)
17. [CLI Commands](#cli-commands)
18. [Deployment](#deployment)

---

## Introduction

**OxygenFramework** is a modern, professional PHP framework developed by **Redwan Aouni**. It combines Laravel-like ease of use with unique features like Python integration for AI/ML.

### Key Features

- 🚀 **Modern PHP** - PHP 7.4 - 8.4 compatible
- 🗄️ **Database Migrations** - Version control your database
- 🔌 **Auto CRUD API** - Generate REST APIs automatically
- 🐍 **Python Integration** - Execute Python/AI from PHP
- 📁 **Storage System** - Local & AWS S3 support
- 🔒 **Security First** - CSRF, XSS, rate limiting
- 🛠️ **Powerful CLI** - 9 commands for code generation
- 📝 **Validation** - Fluent validation API
- 🔐 **Authentication** - Built-in auth system

---

## Installation

### Requirements

- PHP >= 7.4
- Composer
- MySQL/MariaDB >= 5.7
- Apache/Nginx

### Install

```bash
# Navigate to project
cd /path/to/oxygenframework

# Install dependencies
composer install

# Configure environment
cp .env.example .env
nano .env

# Run migrations
php oxygen migrate

# Start server
php oxygen serve
```

Visit: `http://localhost:8000`

---

## Configuration

Configuration files are in `config/`:

```php
use Oxygen\Core\OxygenConfig;

// Get value
$appName = OxygenConfig::get('app.APP_NAME');

// Get with default
$debug = OxygenConfig::get('app.APP_DEBUG', false);

// Set value
OxygenConfig::set('app.timezone', 'UTC');
```

---

## Routing

**File:** `routes/web.php`

```php
use Oxygen\Core\Application;
use Bramus\Router\Router;

$router = Application::getInstance()->make(Router::class);

// Basic routes
$router->get('/', 'HomeController@index');
$router->post('/submit', 'FormController@submit');

// Route parameters
$router->get('/post/([0-9]+)', 'PostController@show');

// Route groups
$router->mount('/admin', function() use ($router) {
    $router->get('/dashboard', 'AdminController@dashboard');
});
```

---

## Controllers

### Create Controller

```bash
php oxygen make:controller PostController
```

### Example Controller

```php
<?php

namespace App\Controllers;

use Controller;
use Oxygen\Core\Request;
use Oxygen\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return $this->view('posts/index.twig', compact('posts'));
    }

    public function show($id)
    {
        $post = Post::find($id);
        return $this->view('posts/show.twig', compact('post'));
    }

    public function store(Request $request)
    {
        $post = Post::create($request->all());
        return $this->json(['post' => $post], 201);
    }
}
```

---

## Models & Database

### Create Model

```bash
php oxygen make:model Post
```

### Example Model

```php
<?php

namespace Oxygen\Models;

use Oxygen\Core\Model;

class Post extends Model
{
    protected $table = 'posts';
    protected $fillable = ['title', 'content', 'user_id'];
}
```

### Using Models

```php
// Get all
$posts = Post::all();

// Find by ID
$post = Post::find(1);

// Create
$post = Post::create([
    'title' => 'My Post',
    'content' => 'Content here'
]);

// Update
Post::update(1, ['title' => 'Updated']);

// Delete
Post::delete(1);

// Where clause
$posts = Post::where('status', '=', 'published');
```

---

## Migrations

### Create Migration

```bash
php oxygen make:migration create_posts_table
```

### Example Migration

```php
<?php

use Oxygen\Core\Database\OxygenMigration;

class CreatePostsTable extends OxygenMigration
{
    public function up()
    {
        $this->createTable('posts', function($table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->dropTable('posts');
    }
}
```

### Run Migrations

```bash
# Run all pending migrations
php oxygen migrate

# Rollback last batch
php oxygen migrate:rollback
```

---

## Authentication

### Register User

```php
use Oxygen\Models\User;
use Oxygen\Core\Security\OxygenSecurity;

$user = User::create([
    'name' => $request->input('name'),
    'email' => $request->input('email'),
    'password' => OxygenSecurity::hashPassword($request->input('password'))
]);
```

### Login

```php
use Oxygen\Core\Auth;
use Oxygen\Core\Application;

$auth = Application::getInstance()->make(Auth::class);

if ($auth->attempt($email, $password)) {
    // Login successful
    $user = $auth->user();
}
```

### Check Authentication

```php
if ($auth->check()) {
    // User is logged in
    $user = $auth->user();
}
```

### In Templates

```twig
{% if auth.check %}
    <p>Welcome, {{ auth.user.name }}!</p>
    <a href="/logout">Logout</a>
{% else %}
    <a href="/login">Login</a>
{% endif %}
```

---

## Security

### CSRF Protection

```twig
<form method="POST" action="/submit">
    {{ csrf_field|raw }}
    <input type="text" name="name">
    <button>Submit</button>
</form>
```

### XSS Protection

```php
use Oxygen\Core\Security\OxygenSecurity;

$safe = OxygenSecurity::escapeHtml($userInput);
```

### Rate Limiting

```php
use Oxygen\Core\Security\OxygenRateLimiter;

if (OxygenRateLimiter::tooManyAttempts('login:' . $email, 5, 60)) {
    return $this->json(['error' => 'Too many attempts'], 429);
}

OxygenRateLimiter::hit('login:' . $email, 60);
```

### Input Sanitization

```php
$clean = OxygenSecurity::sanitizeString($input);
$email = OxygenSecurity::sanitizeEmail($input);
```

---

## Forms & Validation

### Validation

```php
use Oxygen\Core\Validation\OxygenValidator;

$validator = OxygenValidator::make($request->all(), [
    'title' => 'required|string|min:5',
    'email' => 'required|email',
    'age' => 'required|numeric|min:18',
    'password' => 'required|min:8|confirmed'
]);

if ($validator->fails()) {
    return $this->json(['errors' => $validator->errors()], 422);
}

$validated = $validator->validated();
```

---

## Views & Templates

OxygenFramework uses **Twig** templating.

### Create View

**File:** `resources/views/posts/index.twig`

```twig
<!DOCTYPE html>
<html>
<head>
    <title>Posts</title>
</head>
<body>
    <h1>All Posts</h1>
    
    {% for post in posts %}
        <article>
            <h2>{{ post.title }}</h2>
            <p>{{ post.content }}</p>
        </article>
    {% endfor %}
</body>
</html>
```

### Render View

```php
return $this->view('posts/index.twig', [
    'posts' => $posts
]);
```

---

## Storage System

Professional file storage with local and AWS S3 support.

### Configuration

**Local Storage (Default):**

```env
STORAGE_DISK=local
APP_URL=http://localhost:8000
```

**AWS S3:**

```env
STORAGE_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

### Store Files

```php
use Oxygen\Core\Storage\OxygenStorage;

// Upload file
$result = OxygenStorage::put($_FILES['video'], 'videos');

if ($result['success']) {
    $path = $result['path'];  // videos/abc123.mp4
    $url = $result['url'];    // Full URL
}

// Custom filename
$result = OxygenStorage::put($_FILES['file'], 'documents', 'my-file.pdf');
```

### Get File URL

```php
$url = OxygenStorage::url('videos/my-video.mp4');
```

### Check if Exists

```php
if (OxygenStorage::exists('videos/video.mp4')) {
    // File exists
}
```

### Delete File

```php
OxygenStorage::delete('videos/old-video.mp4');
```

### List Files

```php
$videos = OxygenStorage::files('videos');
```

---

## Template Helpers

### storage() - Get Storage URL

```twig
{# Videos #}
<video src="{{ storage('videos/my-video.mp4') }}"></video>

{# Images #}
<img src="{{ storage('images/logo.png') }}">

{# Audio #}
<audio src="{{ storage('audio/song.mp3') }}"></audio>

{# Files #}
<a href="{{ storage('files/document.pdf') }}">Download</a>
```

### asset() - Get Public Asset URL

```twig
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<script src="{{ asset('js/app.js') }}"></script>
<img src="{{ asset('images/icon.png') }}">
```

### url() - Generate URL

```twig
<a href="{{ url('about') }}">About</a>
<a href="{{ url('contact') }}">Contact</a>
```

### Complete Example

```twig
<!DOCTYPE html>
<html>
<head>
    <title>{{ APP_NAME }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    {# Background video from storage #}
    <video autoplay loop muted>
        <source src="{{ storage('videos/background.mp4') }}" type="video/mp4">
    </video>
    
    {# User avatar from storage #}
    <img src="{{ storage('images/avatar.jpg') }}" alt="Avatar">
    
    {# CSRF protected form #}
    <form method="POST" action="{{ url('submit') }}">
        {{ csrf_field|raw }}
        <input type="text" name="title">
        <button>Submit</button>
    </form>
    
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
```

---

## Services

### Email Service

```php
use Oxygen\Services\OxygenEmailService;

$email = new OxygenEmailService();
$email->send('user@example.com', 'Subject', '<h1>Hello!</h1>');
```

### SMS Service

```php
use Oxygen\Services\OxygenSmsService;

$sms = new OxygenSmsService();
$sms->send('+1234567890', 'Your code is: 123456');
```

### Storage Service

```php
use Oxygen\Services\OxygenStorageService;

$storage = new OxygenStorageService();
$result = $storage->upload($_FILES['file'], 'uploads');
```

---

## API Development

### Automatic CRUD API

**File:** `routes/api.php`

```php
use Oxygen\Core\API\OxygenAPI;
use Oxygen\Models\Post;

// Generate complete REST API
OxygenAPI::resource('posts', Post::class);
```

This creates:
- `GET /api/posts` - List all
- `GET /api/posts/1` - Get one
- `POST /api/posts` - Create
- `PUT /api/posts/1` - Update
- `DELETE /api/posts/1` - Delete

---

## Python Integration

### Execute Python

```php
use Oxygen\Core\OxygenPython;

// Run Python code
$result = OxygenPython::run('print("Hello")');

// Execute script
$result = OxygenPython::execute('scripts/process.py', ['arg1']);

// Call function
$result = OxygenPython::call('ai/model.py', 'predict', ['data' => $input]);
```

---

## CLI Commands

```bash
# Code Generation
php oxygen make:controller UserController
php oxygen make:model Post
php oxygen make:middleware CheckAge
php oxygen make:service EmailService
php oxygen make:migration create_users_table

# Database
php oxygen migrate
php oxygen migrate:rollback

# Development
php oxygen serve
php oxygen serve --port=3000

# List all commands
php oxygen list
```

---

## Deployment

### Production Checklist

1. **Set environment:**
```env
APP_DEBUG=false
APP_ENV=production
```

2. **Optimize:**
```bash
composer install --no-dev --optimize-autoloader
```

3. **Run migrations:**
```bash
php oxygen migrate
```

4. **Set permissions:**
```bash
chmod -R 755 /var/www/oxygenframework
chmod -R 775 public/storage
```

---

## Complete Example: Blog

### 1. Create Migration

```bash
php oxygen make:migration create_posts_table
```

### 2. Define Schema

```php
public function up()
{
    $this->createTable('posts', function($table) {
        $table->id();
        $table->string('title');
        $table->text('content');
        $table->string('image')->nullable();
        $table->timestamps();
    });
}
```

### 3. Run Migration

```bash
php oxygen migrate
```

### 4. Create Model

```bash
php oxygen make:model Post
```

### 5. Create Controller

```bash
php oxygen make:controller PostController
```

```php
class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return $this->view('posts/index.twig', compact('posts'));
    }
    
    public function store(Request $request)
    {
        // Upload image
        $image = OxygenStorage::put($_FILES['image'], 'posts');
        
        // Create post
        $post = Post::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'image' => $image['path']
        ]);
        
        return $this->json(['post' => $post], 201);
    }
}
```

### 6. Create View

```twig
<!DOCTYPE html>
<html>
<head>
    <title>Blog - {{ APP_NAME }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <h1>Blog Posts</h1>
    
    {% for post in posts %}
        <article>
            <img src="{{ storage(post.image) }}" alt="{{ post.title }}">
            <h2>{{ post.title }}</h2>
            <p>{{ post.content }}</p>
        </article>
    {% endfor %}
</body>
</html>
```

### 7. Define Routes

```php
$router->get('/posts', 'PostController@index');
$router->post('/posts', 'PostController@store');
```

**Done!** Your blog is ready.

---

## Support

**OxygenFramework 2.0**  
Developed by **Redwan Aouni**  
Email: aouniradouan@gmail.com  
Copyright © 2024 - All Rights Reserved

---

**End of Documentation**
