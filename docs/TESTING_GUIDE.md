# Testing Guide - Modern Frontend Integration

Quick guide to test all the new features of OxygenFramework.

---

## 🧪 Manual Testing

### 1. Test CORS

```bash
# Test preflight request
curl -X OPTIONS http://localhost:8000/api/users \
  -H "Origin: http://localhost:3000" \
  -H "Access-Control-Request-Method: POST" \
  -v

# Expected: 204 No Content with CORS headers
```

### 2. Test JWT Authentication

#### Register
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123"}'

# Expected: 201 Created with access_token and refresh_token
```

#### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# Expected: 200 OK with tokens
```

#### Get Current User
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"

# Expected: 200 OK with user data
```

#### Refresh Token
```bash
curl -X POST http://localhost:8000/api/auth/refresh \
  -H "Content-Type: application/json" \
  -d '{"refresh_token":"YOUR_REFRESH_TOKEN"}'

# Expected: 200 OK with new tokens
```

#### Logout
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"

# Expected: 200 OK
```

### 3. Test Rate Limiting

```bash
# Send multiple requests quickly
for i in {1..100}; do
  curl -X GET http://localhost:8000/api/users -I
done

# Expected: First 60 succeed, then 429 Too Many Requests
# Check headers: X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset
```

### 4. Test Pagination

```bash
curl -X GET "http://localhost:8000/api/users?page=1&per_page=10" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"

# Expected: Paginated response with meta and links
```

### 5. Test AI Endpoints (Protected)

```bash
# Sentiment Analysis
curl -X POST http://localhost:8000/api/ai/sentiment \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"text":"I love this framework!"}'

# Keywords
curl -X POST http://localhost:8000/api/ai/keywords \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"text":"OxygenFramework is amazing","limit":5}'

# Summarize
curl -X POST http://localhost:8000/api/ai/summarize \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"text":"Long text here...","sentences":2}'

# Language Detection
curl -X POST http://localhost:8000/api/ai/language \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"text":"Bonjour tout le monde"}'
```

---

## 🌐 Frontend Testing

### React Test App

Create a simple React app to test the integration:

```bash
npm create vite@latest test-app -- --template react
cd test-app
npm install
```

Create `src/App.jsx`:

```javascript
import { useState } from 'react';

const API_URL = 'http://localhost:8000';

function App() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [token, setToken] = useState(localStorage.getItem('access_token'));
  const [user, setUser] = useState(null);
  const [message, setMessage] = useState('');

  const login = async () => {
    try {
      const response = await fetch(`${API_URL}/api/auth/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
      });

      const data = await response.json();

      if (data.success) {
        setToken(data.data.access_token);
        localStorage.setItem('access_token', data.data.access_token);
        setMessage('Login successful!');
      } else {
        setMessage(data.message);
      }
    } catch (error) {
      setMessage('Error: ' + error.message);
    }
  };

  const getUser = async () => {
    try {
      const response = await fetch(`${API_URL}/api/auth/me`, {
        headers: { 'Authorization': `Bearer ${token}` }
      });

      const data = await response.json();

      if (data.success) {
        setUser(data.data);
        setMessage('User fetched!');
      } else {
        setMessage(data.message);
      }
    } catch (error) {
      setMessage('Error: ' + error.message);
    }
  };

  return (
    <div style={{ padding: '20px' }}>
      <h1>OxygenFramework Test</h1>

      {message && <p style={{ color: 'blue' }}>{message}</p>}

      {!token ? (
        <div>
          <h2>Login</h2>
          <input
            type="email"
            placeholder="Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />
          <input
            type="password"
            placeholder="Password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />
          <button onClick={login}>Login</button>
        </div>
      ) : (
        <div>
          <h2>Authenticated</h2>
          <p>Token: {token.substring(0, 20)}...</p>
          <button onClick={getUser}>Get User</button>
          {user && (
            <div>
              <h3>User Data:</h3>
              <pre>{JSON.stringify(user, null, 2)}</pre>
            </div>
          )}
        </div>
      )}
    </div>
  );
}

export default App;
```

Run the app:

```bash
npm run dev
```

Visit `http://localhost:5173` and test:
1. ✅ CORS works (no errors in console)
2. ✅ Login works
3. ✅ Token is saved
4. ✅ Authenticated requests work

---

## 📊 Expected Results

### ✅ CORS Test
- Preflight OPTIONS request returns 204
- CORS headers present in all responses
- No CORS errors in browser console

### ✅ JWT Authentication
- Register creates user and returns tokens
- Login returns valid tokens
- `/api/auth/me` returns user data with valid token
- Refresh token generates new tokens
- Logout blacklists token
- Blacklisted token is rejected

### ✅ Rate Limiting
- First 60 requests succeed (or configured limit)
- 61st request returns 429
- Rate limit headers present
- Authenticated users get higher limits

### ✅ Pagination
- Returns data array
- Includes meta object with pagination info
- Includes links object with navigation
- Respects page and per_page parameters

### ✅ Standardized Responses
- All success responses have `success: true`
- All error responses have `success: false`
- All responses include timestamp
- Error responses include detailed error messages

---

## 🐛 Troubleshooting

### CORS Errors
- Check `.env` has correct `CORS_ALLOWED_ORIGINS`
- Ensure frontend URL matches exactly (including port)
- Check browser console for specific CORS error

### JWT Errors
- Verify `JWT_SECRET` is set in `.env`
- Check token is being sent in `Authorization` header
- Ensure token hasn't expired
- Check token format: `Bearer {token}`

### Rate Limit Issues
- Check `storage/framework/rate-limits/` is writable
- Verify `RATE_LIMIT_ENABLED=true` in `.env`
- Check rate limit headers in response

### 404 Errors
- Ensure server is running (`php oxygen serve`)
- Check endpoint URL is correct
- Verify route is defined in `routes/api.php`

---

## 📝 Test Checklist

- [ ] CORS preflight works
- [ ] CORS headers present
- [ ] Register endpoint works
- [ ] Login endpoint works
- [ ] Token refresh works
- [ ] Logout works
- [ ] Protected endpoints require auth
- [ ] Rate limiting triggers after limit
- [ ] Rate limit headers present
- [ ] Pagination works correctly
- [ ] All responses follow standard format
- [ ] React app can authenticate
- [ ] React app can make authenticated requests
- [ ] No CORS errors in browser

---

**Happy Testing! 🧪**
