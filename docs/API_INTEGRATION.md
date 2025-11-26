# Modern Frontend Integration Guide

Complete guide for integrating React, Next.js, Vue.js, and other modern frontend frameworks with OxygenFramework.

---

## 🚀 Quick Start

### 1. Backend Setup

```bash
# Install dependencies
composer install

# Copy environment file
copy .env.example .env

# Generate JWT secret
php -r "echo bin2hex(random_bytes(32));"

# Update .env with the generated secret
# JWT_SECRET=<your_generated_secret>

# Start the server
php oxygen serve
```

### 2. Frontend Setup

Configure your frontend to point to the API:

```javascript
const API_URL = 'http://localhost:8000';
```

---

## 🔐 Authentication Flow

### Complete Authentication Example (React)

```javascript
// src/services/api.js
const API_URL = 'http://localhost:8000';

class ApiService {
  constructor() {
    this.baseUrl = API_URL;
  }

  // Get access token from storage
  getAccessToken() {
    return localStorage.getItem('access_token');
  }

  // Get refresh token from storage
  getRefreshToken() {
    return localStorage.getItem('refresh_token');
  }

  // Save tokens to storage
  saveTokens(accessToken, refreshToken) {
    localStorage.setItem('access_token', accessToken);
    localStorage.setItem('refresh_token', refreshToken);
  }

  // Clear tokens from storage
  clearTokens() {
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
  }

  // Make authenticated request
  async request(endpoint, options = {}) {
    const token = this.getAccessToken();
    
    const config = {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        ...options.headers,
      },
    };

    // Add authorization header if token exists
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }

    try {
      const response = await fetch(`${this.baseUrl}${endpoint}`, config);
      const data = await response.json();

      // Handle token expiration
      if (response.status === 401 && data.message?.includes('expired')) {
        const refreshed = await this.refreshToken();
        if (refreshed) {
          // Retry the original request
          return this.request(endpoint, options);
        }
      }

      return { response, data };
    } catch (error) {
      console.error('API request failed:', error);
      throw error;
    }
  }

  // Register new user
  async register(name, email, password) {
    const { response, data } = await this.request('/api/auth/register', {
      method: 'POST',
      body: JSON.stringify({ name, email, password }),
    });

    if (data.success) {
      this.saveTokens(data.data.access_token, data.data.refresh_token);
    }

    return data;
  }

  // Login
  async login(email, password) {
    const { response, data } = await this.request('/api/auth/login', {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    });

    if (data.success) {
      this.saveTokens(data.data.access_token, data.data.refresh_token);
    }

    return data;
  }

  // Refresh access token
  async refreshToken() {
    const refreshToken = this.getRefreshToken();
    
    if (!refreshToken) {
      return false;
    }

    const { response, data } = await this.request('/api/auth/refresh', {
      method: 'POST',
      body: JSON.stringify({ refresh_token: refreshToken }),
    });

    if (data.success) {
      this.saveTokens(data.data.access_token, data.data.refresh_token);
      return true;
    }

    this.clearTokens();
    return false;
  }

  // Logout
  async logout() {
    await this.request('/api/auth/logout', {
      method: 'POST',
    });

    this.clearTokens();
  }

  // Get current user
  async getCurrentUser() {
    const { response, data } = await this.request('/api/auth/me');
    return data;
  }

  // Fetch paginated data
  async fetchPaginated(endpoint, page = 1, perPage = 15) {
    const { response, data } = await this.request(
      `${endpoint}?page=${page}&per_page=${perPage}`
    );
    return data;
  }
}

export default new ApiService();
```

### React Authentication Hook

```javascript
// src/hooks/useAuth.js
import { useState, useEffect, createContext, useContext } from 'react';
import api from '../services/api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Check if user is logged in on mount
    const checkAuth = async () => {
      const token = api.getAccessToken();
      
      if (token) {
        try {
          const response = await api.getCurrentUser();
          if (response.success) {
            setUser(response.data);
          }
        } catch (error) {
          console.error('Auth check failed:', error);
        }
      }
      
      setLoading(false);
    };

    checkAuth();
  }, []);

  const login = async (email, password) => {
    const response = await api.login(email, password);
    if (response.success) {
      setUser(response.data.user);
    }
    return response;
  };

  const register = async (name, email, password) => {
    const response = await api.register(name, email, password);
    if (response.success) {
      setUser(response.data.user);
    }
    return response;
  };

  const logout = async () => {
    await api.logout();
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, loading, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within AuthProvider');
  }
  return context;
}
```

### Usage in Components

```javascript
// src/components/LoginForm.jsx
import { useState } from 'react';
import { useAuth } from '../hooks/useAuth';

export function LoginForm() {
  const { login } = useAuth();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const response = await login(email, password);
      
      if (!response.success) {
        setError(response.message || 'Login failed');
      }
    } catch (err) {
      setError('An error occurred. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <h2>Login</h2>
      
      {error && <div className="error">{error}</div>}
      
      <input
        type="email"
        placeholder="Email"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        required
      />
      
      <input
        type="password"
        placeholder="Password"
        value={password}
        onChange={(e) => setPassword(e.target.value)}
        required
      />
      
      <button type="submit" disabled={loading}>
        {loading ? 'Logging in...' : 'Login'}
      </button>
    </form>
  );
}
```

---

## 📊 Data Fetching with Pagination

### React Hook for Paginated Data

```javascript
// src/hooks/usePagination.js
import { useState, useEffect } from 'react';
import api from '../services/api';

export function usePagination(endpoint, initialPerPage = 15) {
  const [data, setData] = useState([]);
  const [meta, setMeta] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(initialPerPage);

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      setError(null);

      try {
        const response = await api.fetchPaginated(endpoint, page, perPage);
        
        if (response.success) {
          setData(response.data);
          setMeta(response.meta);
        } else {
          setError(response.message);
        }
      } catch (err) {
        setError('Failed to fetch data');
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, [endpoint, page, perPage]);

  const nextPage = () => {
    if (meta && page < meta.last_page) {
      setPage(page + 1);
    }
  };

  const prevPage = () => {
    if (page > 1) {
      setPage(page - 1);
    }
  };

  const goToPage = (pageNumber) => {
    setPage(pageNumber);
  };

  return {
    data,
    meta,
    loading,
    error,
    page,
    perPage,
    setPerPage,
    nextPage,
    prevPage,
    goToPage,
  };
}
```

### Usage Example

```javascript
// src/components/UsersList.jsx
import { usePagination } from '../hooks/usePagination';

export function UsersList() {
  const { data, meta, loading, error, page, nextPage, prevPage } = 
    usePagination('/api/users');

  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div>
      <h2>Users</h2>
      
      <ul>
        {data.map(user => (
          <li key={user.id}>
            {user.name} - {user.email}
          </li>
        ))}
      </ul>

      {meta && (
        <div className="pagination">
          <button onClick={prevPage} disabled={page === 1}>
            Previous
          </button>
          
          <span>
            Page {meta.current_page} of {meta.last_page}
          </span>
          
          <button onClick={nextPage} disabled={page === meta.last_page}>
            Next
          </button>
          
          <span>
            Showing {meta.from} to {meta.to} of {meta.total} results
          </span>
        </div>
      )}
    </div>
  );
}
```

---

## 🎯 Next.js Integration

### API Route Proxy (Recommended)

```javascript
// app/api/[...path]/route.js
import { NextResponse } from 'next/server';

const API_URL = 'http://localhost:8000';

export async function GET(request, { params }) {
  const path = params.path.join('/');
  const searchParams = request.nextUrl.searchParams.toString();
  const url = `${API_URL}/api/${path}${searchParams ? `?${searchParams}` : ''}`;

  const token = request.headers.get('authorization');

  const response = await fetch(url, {
    headers: token ? { 'Authorization': token } : {},
  });

  const data = await response.json();
  return NextResponse.json(data);
}

export async function POST(request, { params }) {
  const path = params.path.join('/');
  const body = await request.json();
  const token = request.headers.get('authorization');

  const response = await fetch(`${API_URL}/api/${path}`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      ...(token ? { 'Authorization': token } : {}),
    },
    body: JSON.stringify(body),
  });

  const data = await response.json();
  return NextResponse.json(data);
}
```

### Server Component

```javascript
// app/users/page.jsx
async function getUsers() {
  const response = await fetch('http://localhost:8000/api/users', {
    cache: 'no-store', // or 'force-cache' for static
  });
  
  return response.json();
}

export default async function UsersPage() {
  const data = await getUsers();

  return (
    <div>
      <h1>Users</h1>
      <ul>
        {data.data.map(user => (
          <li key={user.id}>{user.name}</li>
        ))}
      </ul>
    </div>
  );
}
```

### Client Component with Auth

```javascript
// app/dashboard/page.jsx
'use client';

import { useEffect, useState } from 'react';

export default function Dashboard() {
  const [user, setUser] = useState(null);

  useEffect(() => {
    const fetchUser = async () => {
      const token = localStorage.getItem('access_token');
      
      const response = await fetch('/api/auth/me', {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      
      const data = await response.json();
      
      if (data.success) {
        setUser(data.data);
      }
    };

    fetchUser();
  }, []);

  if (!user) return <div>Loading...</div>;

  return (
    <div>
      <h1>Welcome, {user.name}!</h1>
      <p>Email: {user.email}</p>
    </div>
  );
}
```

---

## 🎨 Vue.js Integration

### Composable for API

```javascript
// composables/useApi.js
import { ref } from 'vue';

const API_URL = 'http://localhost:8000';

export function useApi() {
  const loading = ref(false);
  const error = ref(null);

  const request = async (endpoint, options = {}) => {
    loading.value = true;
    error.value = null;

    const token = localStorage.getItem('access_token');

    try {
      const response = await fetch(`${API_URL}${endpoint}`, {
        ...options,
        headers: {
          'Content-Type': 'application/json',
          ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
          ...options.headers,
        },
      });

      const data = await response.json();
      loading.value = false;

      return data;
    } catch (err) {
      error.value = err.message;
      loading.value = false;
      throw err;
    }
  };

  return { request, loading, error };
}
```

### Authentication Composable

```javascript
// composables/useAuth.js
import { ref, computed } from 'vue';
import { useApi } from './useApi';

const user = ref(null);
const token = ref(localStorage.getItem('access_token'));

export function useAuth() {
  const { request } = useApi();
  const isAuthenticated = computed(() => !!token.value);

  const login = async (email, password) => {
    const data = await request('/api/auth/login', {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    });

    if (data.success) {
      token.value = data.data.access_token;
      user.value = data.data.user;
      localStorage.setItem('access_token', data.data.access_token);
      localStorage.setItem('refresh_token', data.data.refresh_token);
    }

    return data;
  };

  const logout = async () => {
    await request('/api/auth/logout', { method: 'POST' });
    token.value = null;
    user.value = null;
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
  };

  const fetchUser = async () => {
    const data = await request('/api/auth/me');
    if (data.success) {
      user.value = data.data;
    }
  };

  return {
    user,
    token,
    isAuthenticated,
    login,
    logout,
    fetchUser,
  };
}
```

---

## 🛡️ Error Handling

### Global Error Handler (React)

```javascript
// src/utils/errorHandler.js
export function handleApiError(error, response) {
  // Handle validation errors
  if (response?.status === 422 && response?.data?.errors) {
    return {
      type: 'validation',
      errors: response.data.errors,
      message: response.data.message,
    };
  }

  // Handle authentication errors
  if (response?.status === 401) {
    // Redirect to login or refresh token
    return {
      type: 'auth',
      message: 'Please log in to continue',
    };
  }

  // Handle rate limiting
  if (response?.status === 429) {
    return {
      type: 'rate_limit',
      message: 'Too many requests. Please try again later.',
    };
  }

  // Handle server errors
  if (response?.status >= 500) {
    return {
      type: 'server',
      message: 'Server error. Please try again later.',
    };
  }

  // Generic error
  return {
    type: 'generic',
    message: error?.message || 'An error occurred',
  };
}
```

---

## 🔄 Rate Limit Handling

```javascript
// src/utils/rateLimitHandler.js
export class RateLimitHandler {
  constructor() {
    this.retryQueue = [];
  }

  async handleRateLimitedRequest(request, retryAfter = 60) {
    return new Promise((resolve, reject) => {
      setTimeout(async () => {
        try {
          const result = await request();
          resolve(result);
        } catch (error) {
          reject(error);
        }
      }, retryAfter * 1000);
    });
  }

  extractRateLimitInfo(headers) {
    return {
      limit: parseInt(headers.get('X-RateLimit-Limit') || '0'),
      remaining: parseInt(headers.get('X-RateLimit-Remaining') || '0'),
      reset: parseInt(headers.get('X-RateLimit-Reset') || '0'),
    };
  }
}
```

---

## 📱 Complete Example App

Check the `examples/` directory for complete working examples:
- `examples/react-app/` - Full React application
- `examples/nextjs-app/` - Full Next.js application
- `examples/vue-app/` - Full Vue.js application

---

## 🚀 Production Deployment

### Backend (.env production settings)

```env
APP_ENV=production
APP_DEBUG=false

# Use your production domain
CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://app.yourdomain.com

# Strong JWT secret
JWT_SECRET=<very_long_random_string_64+_characters>

# Disable trace in production
API_INCLUDE_TRACE=false

# Enable rate limiting
RATE_LIMIT_ENABLED=true
```

### Frontend Configuration

```javascript
// config.js
const config = {
  development: {
    apiUrl: 'http://localhost:8000',
  },
  production: {
    apiUrl: 'https://api.yourdomain.com',
  },
};

export default config[process.env.NODE_ENV || 'development'];
```

---

**Happy coding! 🎉**
