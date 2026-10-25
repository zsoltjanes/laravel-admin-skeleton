# Views

- View folders (`resources/views`):
  - `admin/` – everything of the admin: `layouts/` (`<x-admin::layouts.app>`, `<x-admin::layouts.auth>`), `auth/` (Fortify pages), `settings/`, `partials/`, `components/` (admin-only anonymous components, used without prefix: `<x-auth-header>`).
  - `common/` – shared between the admin and the public site: `components/` (`<x-country-flag>`, `<x-locale-switcher>`, `<x-appearance-toggle>`, `<x-app-logo>`), `partials/head.blade.php`.
  - `flux/` – Flux overrides, must stay at this path.
  - Reference views by dot path (`view('admin.settings.profile')`), namespaced Blade components with `admin::`.
  - Never name an anonymous component like a Flux one (`flag`, `button`, `icon`, ...): Flux's component wins and silently renders instead of ours.
