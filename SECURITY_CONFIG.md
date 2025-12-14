# Security Configuration Guide

This document outlines the security configurations required for production deployment.

## Session Security

The application uses secure session configuration. The following environment variables must be set in production:

### Required Production Environment Variables

```env
# Session Security
SESSION_SECURE_COOKIE=true          # Force HTTPS-only cookies in production
SESSION_HTTP_ONLY=true              # Prevent JavaScript access to cookies (default: true)
SESSION_SAME_SITE=lax               # CSRF protection: "lax" (default) or "strict" for maximum security
SESSION_LIFETIME=120                # Session lifetime in minutes (default: 120)
SESSION_DRIVER=database             # Use database driver for production (recommended)
```

### Explanation

- **SESSION_SECURE_COOKIE**: When set to `true`, cookies are only sent over HTTPS connections. **MUST be true in production**.
- **SESSION_HTTP_ONLY**: When `true`, prevents JavaScript from accessing session cookies, protecting against XSS attacks. **Keep as true**.
- **SESSION_SAME_SITE**: 
  - `lax` (default): Allows cookies to be sent with top-level navigations and same-site requests. Good balance of security and functionality.
  - `strict`: Maximum security, cookies only sent with same-site requests. May break some third-party integrations.
  - `none`: Only use if you need cross-site cookies (requires HTTPS and secure flag).

## Rate Limiting

API endpoints are protected with rate limiting:

- **Login**: 5 attempts per minute
- **Register**: 5 attempts per minute

These limits help prevent brute-force attacks. Adjust in `routes/api.php` if needed.

## Tenant Isolation

All tenant-scoped models automatically filter by the current school/tenant using a global scope. This ensures:

- Users can only see data from their own school
- Super admins can see all data (when in Admin panel)
- API requests are scoped to the authenticated user's school

## Authorization

Policies are in place for:
- Student access control
- Staff access control  
- Fee Payment access control

All Filament resources respect these policies automatically.

## Testing

Run the test suite to verify security:

```bash
php artisan test
```

Key security tests:
- `tests/Feature/TenantIsolationTest.php` - Verifies tenant isolation
- `tests/Feature/RateLimitingTest.php` - Verifies rate limiting
- `tests/Feature/AuthenticationTest.php` - Verifies authentication flows

