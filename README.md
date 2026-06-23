# Mini Issue Tracker

A small-team issue tracker built with **Laravel 13**. Manage projects, issues, tags, comments, and assigned members — with the interactive parts (tags, comments, members, search/filtering) handled over AJAX, no full page reloads.

## Features

- **Projects** — full CRUD; each project lists its issues; owner-only edit/delete.
- **Issues** — full CRUD with combined **search + status + priority + tag** filtering in a single debounced AJAX request (results + pagination swap in place).
- **Tags** — create unique tags; attach/detach to issues via AJAX.
- **Comments** — paginated AJAX load ("load more"); add via AJAX with inline validation;
  new comments prepend without reload.
- **Members** (bonus) — assign/remove users on an issue via AJAX.
- **Authorization** (bonus) — `ProjectPolicy` restricts edit/delete to the project owner.
- **Search** (bonus) — text search on title/description, debounced.

## Tech stack

Laravel 13 · Laravel Breeze (Blade auth) · Blade + Tailwind CSS · vanilla JS modules
(`fetch`) + Alpine.js · Pest 4 tests · Pint, ESLint, Prettier.

## Requirements

- PHP 8.5
- Composer
- Node.js + npm

## Setup

```bash
git clone git@github.com:gazi04/mini-issue-tracker.git
cd mini-issue-tracker

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed   # schema + demo data
npm run build                # or: npm run dev
```

Then serve it:

```bash
php artisan serve            # http://127.0.0.1:8000
# or run app + vite together:
composer run dev
```

### Seeded login

| Email | Password |
|-------|----------|
| `test@example.com` | `password` |
| `alice@example.com` | `password` |
| `bob@example.com` | `password` |

## Architecture notes

- **Validation** via Form Request classes; **enums** (`IssueStatus`, `IssuePriority`) cast on the model and reused for selects/validation.
- **No N+1** — list/detail queries eager-load relations (`with`, `withCount`).
- **AJAX filtering** — `IssueController@index` returns the full page normally, or just the `issues/_results` Blade partial when the request is AJAX, so the same markup serves both (no duplicated client-side templates).
- **Frontend** is organized under `resources/js/lib` (shared `http`/`dom` helpers) and
  `resources/js/features` (one module per page area).

## Testing & quality

```bash
php artisan test --compact        # Pest (feature + unit)
vendor/bin/pint                   # PHP formatting
npm run lint                      # ESLint (resources/js)
npm run format                    # Prettier
```
