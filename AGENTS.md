# V.O.I.C.E. — Agent Guide

## Quick start
```sh
cp .env.example .env
composer install
npm install --ignore-scripts  # frontend is mostly CDN + Blade
php artisan key:generate
php artisan migrate
npm run build
```

## Commands
| Command | What it does |
|---------|-------------|
| `composer dev` | Runs serve + queue + Pail (logs) + Vite concurrently |
| `composer test` | `config:clear` then `php artisan test` |
| `npm run build` | Vite production build |
| `./vendor/bin/pint` | Laravel Pint (PHP CS fixer) |
| `php artisan complaints:reset` | Reset daily complaint count (runs daily at 00:00 via scheduler) |
| `php artisan queue:listen --tries=1 --timeout=0` | Process queue jobs (database driver) |

## Architecture

### Roles & access
- **student** — default role; 6 complaints/day limit, profanity strike system (3 strikes = 24h ban)
- **admin** — moderate complaints, manage polls, view users (`AdminMiddleware` allows admin + superadmin)
- **superadmin** — assign complaints to admins, manage admin accounts, view superadmin dashboard

Authentication: custom `LoginController` (no Breeze/Jetstream). Claim-account flow for existing student records.

### Key directories
```
app/
  Console/Commands/ResetDailyComplaints.php   # Scheduled task
  Events/                    # ComplaintSubmitted, MessageSent, PollVoteCast (broadcast to Reverb)
  Http/Controllers/
    Admin/                   # ComplaintModeration, Poll, Report, User
    Auth/                    # LoginController, ClaimAccountController
    SuperAdmin/              # Dashboard, Complaint (assignment), AdminManagement
    ComplaintController.php  # User-facing complaint CRUD + polls + ratings
    ComplaintMessageController.php
    VoteController.php
    ProfileController.php
  Middleware/
    AdminMiddleware.php      # role in ['admin', 'superadmin']
    SuperAdminMiddleware.php # role === 'superadmin'
    CheckBlockedUser.php     # global web middleware
  Models/                    # Complaint, ComplaintMessage, Poll, PollOption, PollVote, ProfanityWord, User
  Services/
    ProfanityService.php     # OpenAI → Perspective API → local list fallback
    ComplaintNumberService.php
  Notifications/             # NewComplaintMessage, NewPollNotification
```

### Routing
- `routes/web.php` — all app routes (no SPA, no api routes beyond `/api/user`)
  - `/fix-db` — runtime schema repair (adds missing columns/tables)
  - `/migrate` — `migrate:fresh` + seed + column fallbacks (for cloud deployments)
  - `/import-users` — bulk import from XML spreadsheets
- `routes/channels.php` — `complaint.{id}`, `poll.{id}`, `App.Models.User.{id}`

### Views
Blade only (no Vue/React). Tailwind CSS v4 + Vite, with CDN scripts in layout:
- Tailwind CSS CDN (`cdn.tailwindcss.com`)
- Chart.js CDN
- Font Awesome CDN
- Pusher JS + Laravel Echo CDN (loaded via `<script>` tags, not npm)
- `resources/views/layouts/app.blade.php` — live update polling via `LiveUpdate` JS object

### Broadcasting
- Driver: Reverb (Pusher-compatible protocol)
- Frontend: `pusher-js` + `laravel-echo` via CDN
- Backend: `pusher/pusher-php-server` for broadcasting events
- Queue: `database` driver (needs `jobs` table)

### Database
- Production: TiDB Cloud (MySQL-compatible, port 4000, SSL required)
- Testing: SQLite `:memory:` (configured in `phpunit.xml`)
- Local Docker: MySQL 8.0 (defined in `docker-compose.yaml`)
- Schema gotchas: `/fix-db` and `/migrate` routes handle column additions outside migrations
- Migrations: 21 files covering users, complaints, polls, profanity, messages, sessions, cache

### External services (API keys required)
- `OPENAI_API_KEY` — primary profanity/moderation check (`omni-moderation-latest`)
- `PERSPECTIVE_API_KEY` — fallback profanity check (supports `en`, `tl`)
- Local word list `profanity_words` table as last-resort

### Storage
- Files stored in `storage/app/public`, symlinked to `public/storage`
- Supported uploads: images (jpeg,png,jpg,gif, max 5MB each), audio (max 10MB each)
- `audio_paths` is JSON array, `image_path` is single string, `extra_images` is JSON array

## Testing
```sh
# phpunit.xml uses SQLite :memory:, overrides all env vars
php artisan config:clear && php artisan test
# Focused:
php artisan test --filter=ExampleTest
php artisan test tests/Unit/ExampleTest.php
```

No integration test infrastructure (no external API mocking setup yet).

## Deployment
- **Hugging Face Spaces**: Docker (port 7860), single container via `Dockerfile`
- **Render**: `render.yaml` (PostgreSQL), `render-build.sh` build script
- On production: `config:cache`, `route:cache`, `view:cache` run in entrypoint
