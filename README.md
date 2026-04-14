# socialite-uccello-hub

Laravel Socialite driver for [Uccello Hub](https://hub.uccellolabs.com) OAuth2 authentication.

## Installation

```bash
composer require uccellolabs/socialite-uccello-hub
```

The service provider is auto-discovered — no manual registration needed.

## Configuration

Add to `.env`:

```env
UCCELLO_HUB_URL=https://hub.uccellolabs.com
UCCELLO_HUB_CLIENT_ID=your-client-id
UCCELLO_HUB_CLIENT_SECRET=your-client-secret
UCCELLO_HUB_REDIRECT="${APP_URL}/auth/uccello-hub/callback"
```

Add to `config/services.php`:

```php
'uccello-hub' => [
    'host'          => env('UCCELLO_HUB_URL'),
    'client_id'     => env('UCCELLO_HUB_CLIENT_ID'),
    'client_secret' => env('UCCELLO_HUB_CLIENT_SECRET'),
    'redirect'      => env('UCCELLO_HUB_REDIRECT'),
],
```

## Usage

```php
use Laravel\Socialite\Facades\Socialite;

// Redirect to Uccello Hub
return Socialite::driver('uccello-hub')->redirect();

// Handle the callback
$user = Socialite::driver('uccello-hub')->user();

$user->getId();
$user->getName();
$user->getEmail();
$user->token;
$user->getRaw()['preferred_locale'];
$user->getRaw()['current_team_id'];
```

## User fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Uccello Hub user ID |
| `name` | string | Full name |
| `email` | string | Email address |
| `preferred_locale` | string\|null | Language preference (`fr`, `en`, …) |
| `current_team_id` | integer\|null | Active team/workspace |

## AI skills (Claude Code & Cursor)

This package ships an AI skill that guides Claude and Cursor through the full integration (routes, controller, migration, pitfalls).

**With [Laravel Boost](https://github.com/laravel/boost)** — the skill is auto-discovered and installed when you run:

```bash
php artisan boost:install --skills
```

**Without Laravel Boost** — run the dedicated install command:

```bash
php artisan uccello-hub:install
```

This copies the skill to `.claude/skills/` (Claude Code) and `.cursor/rules/` (Cursor).

**Manual publish:**

```bash
php artisan vendor:publish --tag=claude-skills
```

## License

MIT
