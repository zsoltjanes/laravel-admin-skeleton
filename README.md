# Laravel admin skeleton

Kiinduló projekt (skeleton) Laravel alkalmazásokhoz, kész admin felülettel. Új projektnél nem kell újra megépíteni a bejelentkezést, a felhasználókezelést, a beállításokat és az admin layoutot: ezek már működnek, magyar és angol nyelven, világos és sötét módban.

Az admin kinézete a [Paces Tailwind téma](https://themes.coderthemes.com/paces/tailwind/index.html) alapján készült, de nem a téma saját CSS/JS-ét használja: Flux komponensekből és Tailwind utility-kből van újraépítve.

## Mit tartalmaz

- **Hitelesítés (Fortify):** bejelentkezés, regisztráció, elfelejtett jelszó, e-mail megerősítés, jelszó újbóli megerősítése, kétlépcsős azonosítás (TOTP + helyreállító kódok), passkey (WebAuthn) bejelentkezés.
- **Dashboard:** statisztika csempék és ApexCharts grafikonok (terület, oszlop, fánk) a felhasználói adatokból.
- **Felhasználók:** lista kereséssel, szűrőkkel, rendezhető oszlopokkal, lapozással és tömeges törléssel; létrehozás és szerkesztés.
- **Beállítások:** profil, biztonság (jelszó, 2FA, passkeyek), megjelenés (világos/sötét/rendszer), fiók törlése.
- **Többnyelvűség:** `hu` (alapértelmezett) és `en`, nyelvváltóval; minden felületi szöveg `__()`-n megy át, a magyar fordítások a `lang/hu.json`-ban és a `lang/hu/` alatt vannak.
- **Admin építőelemek** (`resources/views/admin/components`): oldalcím, kártya, sidebar menüpontok és lenyíló csoportok, stat csempe, grafikon, lapozó nézet.

## Technológia

| | |
|---|---|
| Backend | PHP 8.4, Laravel 13, Fortify |
| Frontend | Livewire 4, Flux (free), Tailwind CSS 4, Vite, ApexCharts |
| Tesztek, minőség | Pest 5, Larastan (PHPStan), Pint |
| Csomagkezelő | Composer, **pnpm** (npm helyett) |

## Telepítés

Szükséges: PHP 8.4, Composer, Node.js és pnpm.

```bash
git clone <repo-url> my-project
cd my-project
composer run setup
php artisan db:seed
```

A `composer run setup` telepíti a függőségeket, létrehozza a `.env`-et a `.env.example`-ből, kulcsot generál, lefuttatja a migrációkat (alapból SQLite) és buildeli a frontendet.

A `db:seed` létrehozza az alapértelmezett admin felhasználót, helyi (`local`) környezetben pedig 60 demo felhasználót is a listához és a grafikonokhoz.

## Fejlesztés

```bash
composer run dev
```

Ez egyszerre indítja a fejlesztéshez szükséges folyamatokat (`php artisan dev`: PHP szerver, Vite stb.). Az admin a `http://localhost:8000/admin` címen érhető el.

Alapértelmezett belépés (a `.env`-ben átírható):

| Változó | Alapérték | Jelentés |
|---|---|---|
| `ADMIN_PATH` | `admin` | Az admin URL előtagja (a Fortify oldalak is ez alatt vannak) |
| `ADMIN_NAME` | `Admin` | Az admin felhasználó neve |
| `ADMIN_EMAIL` | `admin@admin.hu` | Az admin felhasználó e-mail címe |
| `ADMIN_PASSWORD` | `admin` | Az admin felhasználó jelszava |

Élesítés előtt az admin jelszavát mindenképp állítsd át.

## Parancsok

| Parancs | Mit csinál |
|---|---|
| `composer run dev` | Fejlesztői környezet indítása |
| `composer test` | Pint ellenőrzés, PHPStan és a teljes tesztcsomag |
| `php artisan test --compact` | Csak a tesztek |
| `composer lint` | Kódformázás Pinttel |
| `composer types:check` | Statikus elemzés (Larastan) |
| `pnpm run build` | Frontend build |

## Mappaszerkezet

```
app/Livewire/Admin/          Admin Livewire komponensek (osztály + külön view)
routes/admin.php             Admin route-ok (nevek: admin.<resource>.<action>)
routes/settings.php          Beállítás oldalak
resources/views/admin/       Admin nézetek: layouts/, auth/, settings/, partials/, components/
resources/views/common/      Admin és publikus oldal közös részei (logó, nyelvváltó, téma váltó)
resources/views/flux/        Flux felülírások
resources/css/app.css        Paces design tokenek (színek, árnyékok, méretek, dark mód)
resources/js/charts.js       ApexCharts integráció
lang/                        Magyar fordítások
```

## Új admin oldal hozzáadása

1. Komponens: `php artisan make:livewire admin.posts.index` → `app/Livewire/Admin/Posts/Index.php` + `resources/views/admin/posts/index.blade.php`.
2. Route a `routes/admin.php`-ba, `admin.posts.index` névvel.
3. Menüpont a `resources/views/admin/layouts/app/sidebar.blade.php`-ba `<x-admin-nav-item>`-mel.
4. Az oldal tetejére `<x-admin-page-title>`, a tartalom `<x-admin-card>`-ba. Listaoldalnál a `resources/views/admin/users/index.blade.php` a minta.
5. Minden új szöveg magyar fordítása a `lang/hu.json`-ba.
6. Pest teszt a `tests/Feature/` alá.

A részletes konvenciók (design tokenek, komponensek, elnevezési szabályok) a [CLAUDE.md](CLAUDE.md)-ben vannak.

## Licenc

MIT
