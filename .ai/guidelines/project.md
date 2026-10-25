# Laravel admin skeleton – project rules

Laravel 13, Livewire 4 + Flux (free), Fortify, Tailwind 4, Pest 5 (tests are written in Pest, never PHPUnit classes).

- Livewire components are class-based with a separate view. Do not create single-file (⚡) components. Admin components: `php artisan make:livewire admin.posts.index` → `app/Livewire/Admin/Posts/Index.php` + `resources/views/admin/posts/index.blade.php`.
- Use **pnpm**, never npm: `pnpm install`, `pnpm add <pkg>`, `pnpm run build`, `pnpm run dev`.
- The admin lives under `/admin` (`config('fortify.prefix')`, `ADMIN_PATH`); the Fortify routes (login, forgot password, etc.) share the same prefix.
- Admin routes go in `routes/admin.php` (names `admin.<resource>.<action>`).
- Every UI text goes through `__()`, with the Hungarian translation added to `lang/hu.json` (supported locales: `hu`, `en`).
- Never use a translation key that equals a `lang/hu/*.php` file name (e.g. `Actions`) without a `lang/hu.json` entry: on a case-insensitive filesystem `__()` returns that file's array.
- Project guidelines live in `.ai/guidelines/*.md`; `CLAUDE.md` is generated from them by `php artisan boost:update`. Edit the guideline files, never the generated `CLAUDE.md`.
