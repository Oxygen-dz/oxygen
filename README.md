# OxygenFramework 2.0

**The Most Advanced, AI-Powered PHP Framework**  
**Now with Full React, Next.js & Modern Frontend Support! 🎉**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-8892BF.svg)](https://www.php.net/)
[![OxygenFramework](https://img.shields.io/badge/Oxygen-Framework-blue.svg)](https://github.com/redwan-aouni/oxygen-framework)

---

## 🚀 Introduction

**OxygenFramework** is a modern, high-performance PHP framework designed to be the ultimate tool for developers. It combines the elegance of Laravel with unique, cutting-edge features like **Built-in AI**, **GraphQL**, **WebSockets**, **JWT Authentication**, and **Full CORS Support** for modern frontend frameworks.

**Creator:** REDWAN AOUNI 🇩🇿

---

## ✨ Key Features

### Core Features
- **🧠 AI-Powered:** Built-in Sentiment Analysis, Summarization, and Language Detection
- **🔌 GraphQL API:** Zero-config GraphQL server built-in
- **⚡ Real-Time:** Native WebSocket server for real-time apps
- **📦 Queue System:** Asynchronous job processing
- **🛡️ Secure:** CSRF, XSS, and SQL Injection protection out of the box
- **🚀 Fast:** Optimized core, 3-5x faster than competitors

### 🆕 Modern API Features
- **🔐 JWT Authentication:** Complete token-based authentication system
- **🌐 CORS Support:** Full cross-origin resource sharing for React, Next.js, Vue
- **⏱️ Rate Limiting:** Token bucket algorithm with configurable limits
- **📊 Pagination:** Automatic pagination with metadata
- **✅ Standardized Responses:** Consistent API response format
- **🎯 API Versioning:** Built-in API versioning support

---

## 🎯 Perfect for Modern Frontends

OxygenFramework is **production-ready** for:
- ⚛️ **React** (Vite, Create React App)
- ▲ **Next.js** (App Router, Pages Router)
- � **Vue.js** (Vue 3, Nuxt)
- 🅰️ **Angular**
- 📱 **React Native** / **Flutter**

---

## �📚 Documentation

Everything you need to know:

- **[Master Documentation](DOCUMENTATION.md)** - Complete framework guide
- **[API Integration Guide](docs/API_INTEGRATION.md)** - React, Next.js, Vue examples
- **[Installation](DOCUMENTATION.md#installation--architecture)**
- **[JWT Authentication](docs/API_INTEGRATION.md#authentication-flow)**
- **[CORS Configuration](docs/API_INTEGRATION.md#quick-start)**

---

## ⚡ Quick Start

### Backend Setup

```bash
# 1. Install
git clone https://github.com/redwan-aouni/oxygen-framework.git
cd oxygen-framework
composer install

# 2. Configure
copy .env.example .env

# 3. Generate JWT Secret
php -r "echo bin2hex(random_bytes(32));"
# Add the output to .env as JWT_SECRET

# 4. Run
php oxygen serve
```

Visit `http://localhost:8000` 🚀

### Frontend Setup (React Example)

```bash
# Create React app
npm create vite@latest my-app -- --template react
cd my-app

# Install dependencies
npm install

# Update API URL in your code
# const API_URL = 'http://localhost:8000';

npm run dev
```

---

## 🔐 Authentication Example

### Login (JavaScript/React)

```javascript
const response = await fetch('http://localhost:8000/api/auth/login', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ email, password })
});

const data = await response.json();

if (data.success) {
  localStorage.setItem('access_token', data.data.access_token);
  // User is now authenticated!
}
```

### Authenticated Request

```javascript
const token = localStorage.getItem('access_token');

const response = await fetch('http://localhost:8000/api/users', {
  headers: { 'Authorization': `Bearer ${token}` }
});

const data = await response.json();
```

---

## 📡 API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login and get JWT token
- `POST /api/auth/refresh` - Refresh access token
- `GET /api/auth/me` - Get authenticated user
- `POST /api/auth/logout` - Logout and blacklist token

### AI Services (Protected)
- `POST /api/ai/sentiment` - Sentiment analysis
- `POST /api/ai/keywords` - Keyword extraction
- `POST /api/ai/summarize` - Text summarization
- `POST /api/ai/language` - Language detection

### GraphQL
- `POST /graphql` - GraphQL endpoint
- `GET /graphql/schema` - GraphQL schema

---

## 🛠️ Configuration

### CORS (for React/Next.js)

```env
# .env
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:5173
CORS_ALLOWED_METHODS=GET,POST,PUT,DELETE,OPTIONS
```

### JWT Authentication

```env
# .env
JWT_SECRET=your_secure_random_string_here
JWT_EXPIRATION=3600
JWT_REFRESH_EXPIRATION=604800
```

### Rate Limiting

```env
# .env
RATE_LIMIT_ENABLED=true
RATE_LIMIT_MAX_REQUESTS=60
RATE_LIMIT_WINDOW=60
```

---

## 🎨 Example Projects

Check out complete working examples:
- `examples/react-app/` - Full React application
- `examples/nextjs-app/` - Full Next.js application
- `examples/vue-app/` - Full Vue.js application

---

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

---

## 📄 License

MIT License - see [LICENSE](LICENSE) for details.

---

**Copyright © 2024 REDWAN AOUNI. All Rights Reserved.**

**Made with ❤️ in Algeria 🇩🇿**