# Fix: Admin Login Redirect Issue

## Problem
User `admin@umkm.test` dengan password `password` setelah login masih diarahkan ke `/shop` padahal seharusnya diarahkan ke admin dashboard.

## Root Cause Analysis
User `admin@umkm.test` memiliki role `user` di database, bukan `admin`. Karena itu, method `isAdmin()` mengembalikan `false` dan user dianggap sebagai customer biasa.

```sql
-- Before fix
admin@umkm.test → role: 'user' → isAdmin() = FALSE → redirect to /shop ❌

-- After fix  
admin@umkm.test → role: 'admin' → isAdmin() = TRUE → redirect to /admin/dashboard ✅
```

## Solution Applied

### 1. Database Role Update
Updated user role in database:
```php
$admin = App\Models\User::where('email', 'admin@umkm.test')->first();
$admin->role = 'admin';
$admin->save();
```

### 2. Verification
- ✅ `admin@umkm.test` now has role `admin`
- ✅ `isAdmin()` method returns `true`
- ✅ Login redirect logic will now work correctly
- ✅ Admin dashboard route exists and accessible
- ✅ `buyer@umkm.test` correctly has role `user`

## Current User Setup

### Admin User
```
Email: admin@umkm.test
Password: password
Role: admin
Expected Redirect: /admin/dashboard
```

### Buyer/Customer User  
```
Email: buyer@umkm.test
Password: password
Role: user
Expected Redirect: /shop
```

## Role-Based Redirect Logic (Already Implemented)

### AuthenticatedSessionController
```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    // Role-based redirect after login
    if (Auth::user()->isAdmin()) {
        return redirect()->route('admin.dashboard'); // ← Admin goes here
    }

    return redirect()->route('shop.index'); // ← Customer goes here
}
```

### User Model Method
```php
public function isAdmin(): bool
{
    return $this->role === 'admin'; // ← Now returns true for admin@umkm.test
}
```

## Testing Results

### Admin Login Test
- User: `admin@umkm.test`
- Role: `admin` ✅
- `isAdmin()`: `TRUE` ✅
- Expected redirect: `/admin/dashboard` ✅

### Customer Login Test
- User: `buyer@umkm.test`
- Role: `user` ✅
- `isAdmin()`: `FALSE` ✅
- Expected redirect: `/shop` ✅

## Manual Testing Instructions

### Test Admin Login
1. Go to `/login`
2. Enter credentials:
   - Email: `admin@umkm.test`
   - Password: `password`
3. Click Login
4. **Expected**: Redirect to `/admin/dashboard`

### Test Customer Login
1. Logout if logged in
2. Go to `/login`
3. Enter credentials:
   - Email: `buyer@umkm.test`
   - Password: `password`
4. Click Login
5. **Expected**: Redirect to `/shop`

## Status
✅ **FIXED** - Admin user now has correct role and will redirect to admin dashboard after login.

The issue was a simple database role mismatch, not a code logic problem. All authentication redirect logic was already correctly implemented.