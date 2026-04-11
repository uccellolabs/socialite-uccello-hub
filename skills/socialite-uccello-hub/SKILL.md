---
name: socialite-uccello-hub
description: "Use this skill when adding 'Se connecter avec Uccello Hub' authentication to a Laravel project. Activate when: wiring up OAuth login via Uccello Hub (Passport), configuring the Socialite driver, handling redirect/callback flows, or storing the authenticated Uccello Hub user locally."
license: MIT
metadata:
  author: uccellolabs
---

# Uccello Hub — Socialite Authentication

Uccello Hub acts as an OAuth2 authorization server (Laravel Passport). This skill installs and wires up the `uccellolabs/socialite-uccello-hub` package to authenticate users via Uccello Hub in any Laravel application.

## 1. Install the package

The package is a local path dependency until published on Packagist. Add it via a path repository in `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../package/socialite-uccello-hub"
        }
    ],
    "require": {
        "uccellolabs/socialite-uccello-hub": "*"
    }
}
```

Then run:

```bash
composer require uccellolabs/socialite-uccello-hub
```

The service provider is auto-discovered — no manual registration needed.

## 2. Environment variables

Add to `.env`:

```env
UCCELLO_HUB_URL=https://hub.uccellolabs.com
UCCELLO_HUB_CLIENT_ID=your-client-id
UCCELLO_HUB_CLIENT_SECRET=your-client-secret
UCCELLO_HUB_REDIRECT="${APP_URL}/auth/uccello-hub/callback"
```

## 3. Configure `config/services.php`

```php
'uccello-hub' => [
    'host'          => env('UCCELLO_HUB_URL'),
    'client_id'     => env('UCCELLO_HUB_CLIENT_ID'),
    'client_secret' => env('UCCELLO_HUB_CLIENT_SECRET'),
    'redirect'      => env('UCCELLO_HUB_REDIRECT'),
],
```

## 4. Register an OAuth client on Uccello Hub

In the Uccello Hub admin (or via Passport artisan commands on the Hub), create an OAuth client:
- **Grant type:** Authorization Code
- **Redirect URI:** must match `UCCELLO_HUB_REDIRECT` exactly

Copy the generated `client_id` and `client_secret` into `.env`.

## 5. Routes

```php
// routes/web.php
Route::get('/auth/uccello-hub', [AuthController::class, 'redirect'])->name('auth.uccello-hub');
Route::get('/auth/uccello-hub/callback', [AuthController::class, 'callback']);
```

## 6. Controller

```php
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirect(): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return Socialite::driver('uccello-hub')->redirect();
    }

    public function callback(): \Illuminate\Http\RedirectResponse
    {
        $socialiteUser = Socialite::driver('uccello-hub')->user();

        $user = User::updateOrCreate(
            ['uccello_hub_id' => $socialiteUser->getId()],
            [
                'name'              => $socialiteUser->getName(),
                'email'             => $socialiteUser->getEmail(),
                'uccello_hub_token' => $socialiteUser->token,
            ],
        );

        Auth::login($user, remember: true);

        return redirect()->intended('/dashboard');
    }
}
```

## 7. User model migration

Add the Uccello Hub fields to the users table:

```bash
php artisan make:migration add_uccello_hub_fields_to_users_table --table=users
```

```php
$table->string('uccello_hub_id')->nullable()->unique();
$table->string('uccello_hub_token')->nullable();
```

Make these fields `$fillable` in the `User` model.

## Available user fields

The `/api/user` endpoint on Uccello Hub returns:

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Uccello Hub user ID |
| `name` | string | Full name |
| `email` | string | Email address |
| `preferred_locale` | string\|null | Language preference (`fr`, `en`, …) |
| `current_team_id` | integer\|null | Active team/workspace |

Access raw fields via `$socialiteUser->getRaw()['preferred_locale']`.

## Common Pitfalls

- The config key **must** be `uccello-hub` (hyphenated) — this is the driver name.
- `UCCELLO_HUB_REDIRECT` must match the redirect URI registered in the Uccello Hub OAuth client **exactly** (protocol, trailing slash, etc.).
- If the app is an SPA/API with no session, call `->stateless()` before `redirect()` and `user()`.
- Always handle the case where the user denies the authorization — Socialite throws an exception in `user()`.
