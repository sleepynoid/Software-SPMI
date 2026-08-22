# Plan: Migrasi SPMI dari Software-SPMI-old ke Software-SPMI (Laravel 13 + Vue 3 + PrimeVue 4)

## Summary

Migrasikan seluruh model, basis data (migrations, seeders, factories), dan fitur SPMI (Sistem Penjaminan Mutu Internal) dari project lama (`Software-SPMI-old`: Laravel 11 + Vue 3.4 + PrimeVue 4.3 + Ziggy + custom auth) ke project baru (`Software-SPMI`: Laravel 13 + Vue 3.5 + Inertia v3 + reka-ui/shadcn + Fortify + Wayfinder + Pest 5). Project baru saat ini sudah menggunakan Vue 3.5 dengan reka-ui (Radix-like untuk Vue) dan shadcn-style components — frontend stack akan diganti ke PrimeVue 4 untuk konsistensi dengan project lama. Semua config harus dynamic via `env()` dengan sensible defaults, tidak ada hardcoded values.

## Current State Analysis

### Project Lama (Software-SPMI-old)
- **Stack:** Laravel 11, PHP 8.2/8.4, Vue 3.4, Inertia v3, PrimeVue 4.3, Tailwind CSS 4, Ziggy, Pinia, XLSX, Zod, custom auth (AccountController)
- **Domain:** SPMI untuk ITATS berbasis PPEPP (Penetapan, Pelaksanaan, Evaluasi, Pengendalian, Peningkatan)
- **12 Models:** User, Role, UnitKerja, PeriodeAMI, KategoriStandar, StandarDikti, IndikatorMutu, TargetUnit, CapaianPelaksanaan, KertasKerjaAudit, TindakLanjutPtk, RisalahRtm
- **14 Vue Pages:** Dashboard, Auth/Login, Master (Users, UnitKerja, KategoriStandar), Penetapan (Periode, Standar, Indikator, Import, DistribusiTarget), Pelaksanaan (EvaluasiDiri), Evaluasi (JadwalAudit, KKA), Pengendalian (IsiRtl), Peningkatan (Risalah)
- **AppLayout.vue:** Sidebar dengan role-based menu, header dengan user info, PrimeVue Toast
- **Config:** `config/database.php` menggunakan `env()` dengan defaults (tidak hardcoded). Issue user terjadi karena config caching — `config:cache` membuat perubahan `.env` tidak efektif sampai `config:clear` dijalankan

### Project Baru (Software-SPMI) — Current State (Verified)
- **Stack:** Laravel 13.17, PHP 8.3+, Vue 3.5.13, Inertia v3, Tailwind CSS 4, **reka-ui** (Radix-like untuk Vue, NOT React), shadcn-style UI components, Fortify 1.37, Wayfinder 0.1.14, Pest 5, Pint, Larastan, Boost, Chisel
- **Frontend (Vue 3, BUKAN React):**
  - `package.json`: `vue@^3.5.13`, `@inertiajs/vue3@^3.0.0`, `@vitejs/plugin-vue@^6.0.0`, `vue-tsc@^2.2.4`, `eslint-plugin-vue@^9.32.0`, `@vue/eslint-config-typescript@^14.3.0`
  - UI library: `reka-ui@^2.9.8` (Radix-like untuk Vue), `@lucide/vue@^1.17.0`, `vue-sonner@^2.0.0`, `class-variance-authority`, `clsx`, `tailwind-merge`, `tw-animate-css`
  - TIDAK punya: PrimeVue, PrimeVueResolver, XLSX, Pinia, Zod, @primeuix/themes
  - Scripts: `dev`, `build`, `build:ssr`, `format`, `format:check`, `lint`, `lint:check`, `types:check` (vue-tsc)
- **`vite.config.ts`:** Sudah menggunakan `vue()` plugin dari `@vitejs/plugin-vue`. Input: `resources/css/app.css` dan `resources/js/app.ts`. Punya `inertia()`, `tailwindcss()`, `wayfinder({ formVariants: true })`, `laravel()` dengan bunny fonts. TIDAK punya PrimeVueResolver atau unplugin-vue-components
- **`resources/js/app.ts`:** Vue 3 entry point menggunakan `createInertiaApp` dari `@inertiajs/vue3`. Imports `AppLayout`, `AuthLayout`, `SettingsLayout` dari `@/layouts/`. Imports `initializeTheme` dari `@/composables/useAppearance`. Imports `initializeFlashToast` dari `@/lib/flashToast`. Layout resolver: Welcome→null, auth/*→AuthLayout, settings/*→[AppLayout, SettingsLayout], default→AppLayout. Progress color: `#4B5563`. TIDAK punya PrimeVue setup (PrimeVue, Aura theme, ToastService, ConfirmationService, DialogService)
- **`resources/views/app.blade.php`:** Uses `@vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])`. Punya dark mode inline script dan `<style>` block untuk oklch background colors. TIDAK punya `@viteReactRefresh` (sudah Vue)
- **`resources/css/app.css`:** Uses `@import 'tailwindcss'` dan `@import 'tw-animate-css'`. Punya shadcn-style CSS variables (--background, --foreground, --card, --primary, dll.) dengan hsl() values. Punya dark mode variant `@custom-variant dark (&:is(.dark *))`. TIDAK punya `@import "tailwindcss-primeui"` atau ITATS brand colors
- **Existing Vue pages (9 files):** `Dashboard.vue`, `Welcome.vue`, `auth/Login.vue`, `auth/Register.vue`, `auth/ResetPassword.vue`, `auth/ForgotPassword.vue`, `settings/Profile.vue`, `settings/Security.vue`, `settings/Appearance.vue`
- **Existing shadcn UI components (200+ files in `components/ui/`):** sidebar, button, sheet, card, dialog, select, breadcrumb, spinner, alert, badge, input, label, dropdown-menu, tooltip, sonner, navigation-menu, separator, checkbox, skeleton, collapsible, avatar
- **Existing components:** AppSidebar, AppHeader, AppShell, NavMain, NavFooter, NavUser, UserMenuContent, AppLogo, AppLogoIcon, AppContent, AppSidebarHeader, Breadcrumbs, TextLink, Heading, PasswordInput, PlaceholderPattern, AppearanceTabs, DeleteUser, UserInfo, AlertError, InputError
- **Existing layouts:** `AppLayout.vue`, `AuthLayout.vue`, `settings/Layout.vue`, `app/AppHeaderLayout.vue`, `app/AppSidebarLayout.vue`, `auth/AuthSplitLayout.vue`, `auth/AuthSimpleLayout.vue`, `auth/AuthCardLayout.vue`
- **Existing composables:** `useAppearance.ts`, `useInitials.ts`, `useCurrentUrl.ts`
- **Existing lib:** `flashToast.ts`, `utils.ts`
- **Existing types:** `auth.ts`, `ui.ts`, `index.ts`, `navigation.ts`, `vue-shims.d.ts`, `global.d.ts`
- **Wayfinder generated actions in `actions/`:** Fortify controllers, Settings controllers, Inertia controllers
- **`app/Models/User.php`:** Uses PHP 8 attributes: `#[Fillable(['name', 'email', 'password'])]`, `#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]`. Only has name/email/password fields (NO SPMI fields like nama_lengkap, nidn, jenis_user, role_id, unit_kerja_id)
- **`routes/web.php`:** Minimal: `Route::inertia('/', 'Welcome')->name('home')` and `Route::inertia('dashboard', 'Dashboard')->name('dashboard')` with auth/verified middleware. Requires `settings.php`
- **`bootstrap/app.php`:** Encrypts cookies except: `appearance`, `sidebar_state`. Web middleware: HandleAppearance, HandleInertiaRequests, AddLinkHeadersForPreloadedAssets
- **`HandleInertiaRequests.php`:** Shares: `name` (config app.name), `auth.user` (without eager loading), `sidebarOpen`. TIDAK eager load role and unitKerja
- **`config/database.php`:** Sudah menggunakan `env()` properly with defaults (standard Laravel 13 config). Supports sqlite, mysql, mariadb, pgsql, sqlsrv
- **`config/fortify.php`:** Features: registration + resetPasswords. Home: `/dashboard`. Views: true
- **`.env.example`:** APP_NAME=Laravel (needs to be SPMI), APP_LOCALE=en (needs to be id), DB_CONNECTION=sqlite (MySQL lines commented out), SESSION_DOMAIN=null
- **`composer.json`:** Laravel 13.17, PHP 8.3, Fortify 1.37, Wayfinder 0.1.14, Inertia v3, Pest 5, Pint, Larastan, Boost, Chisel. Scripts include `test` which runs `config:clear` first
- **`AppServiceProvider.php`:** CarbonImmutable, DB::prohibitDestructiveCommands in production, Password defaults
- **Migrations:** Only 3 default migrations: users, cache, jobs
- **Seeders:** Default: creates Test User with `name` field
- **Factories:** Default: name, email, email_verified_at, password, remember_token

## Proposed Changes

### Phase 0: Frontend Stack Migration (reka-ui/shadcn → PrimeVue 4)

Ganti UI library dari reka-ui + shadcn-style components ke PrimeVue 4, mengikuti setup project lama. Project SUDAH menggunakan Vue 3 — tidak perlu ganti dari React.

#### 0.1 Update `package.json`
- **Remove (dependencies):** `reka-ui`, `@lucide/vue`, `vue-sonner`, `class-variance-authority`, `clsx`, `tailwind-merge`, `tw-animate-css`
- **Add (dependencies):** `primevue@^4.3`, `@primeuix/themes@^1.0`, `primeicons@^7.0`, `tailwindcss-primeui@^0.5`, `xlsx@^0.18`, `zod@^3.24`, `pinia@^3.0`
- **Add (devDependencies):** `@primevue/auto-import-resolver@^4.5`, `unplugin-vue-components@^28`
- **Keep:** `vue@^3.5.13`, `@inertiajs/vue3@^3.0.0`, `@inertiajs/vite@^3.0.0`, `@vitejs/plugin-vue@^6.0.0`, `@vueuse/core@^12.8.2`, `laravel-vite-plugin@^3.0.0`, `tailwindcss@^4.1.1`, `@tailwindcss/vite@^4.1.11`, `@laravel/vite-plugin-wayfinder@^0.1.3`, `vue-tsc@^2.2.4`, `eslint-plugin-vue@^9.32.0`, `@vue/eslint-config-typescript@^14.3.0`, `typescript`, `vite@^8.0.0`, `concurrently`, `eslint`, `prettier`, `prettier-plugin-tailwindcss`, `typescript-eslint`, `eslint-config-prettier`, `eslint-import-resolver-typescript`, `eslint-plugin-import`, `@stylistic/eslint-plugin`, `@eslint/js`, `@types/node`
- **Keep (optionalDependencies):** `@laravel/multiplex`, `@rollup/*`, `@tailwindcss/oxide-*`, `lightningcss-*`
- **Scripts:** Keep `dev`, `build`, `build:ssr`, `format`, `format:check`, `lint`, `lint:check`, `types:check`

#### 0.2 Update `vite.config.ts`
- **File:** Keep as `vite.config.ts` (TIDAK perlu rename ke `.js` — project sudah Vue)
- **Add:** `Components()` from `unplugin-vue-components/vite` with `PrimeVueResolver()` for auto-import PrimeVue components
- **Keep:** `vue()` plugin (sudah ada), `inertia()`, `tailwindcss()`, `wayfinder({ formVariants: true })`, `laravel()` with bunny fonts
- **Keep input:** `resources/css/app.css` dan `resources/js/app.ts` (TIDAK perlu ganti ke `.js`)
- **Add alias:** `'@primevue'` → PrimeVue module path (jika diperlukan)

#### 0.3 Update `resources/js/app.ts` (Vue entry point)
- **File:** Keep as `app.ts` (TIDAK perlu ganti ke `.js`)
- **Add PrimeVue setup:** `PrimeVue` plugin with `Aura` theme preset, `darkModeSelector: false`
- **Add services:** `ToastService`, `ConfirmationService`, `DialogService`
- **Add CSS import:** `primeicons/primeicons.css`
- **Update progress bar color:** `#00479b` (ITATS blue, dari `#4B5563`)
- **Update layout resolver:** Map SPMI page component names to layouts (auth, settings, default app layout)
- **Keep:** `createInertiaApp` from `@inertiajs/vue3`, `initializeTheme`, `initializeFlashToast`
- **Note:** PrimeVue plugin harus di-register sebelum Inertia app di-resolve. Gunakan `setup` callback dari `createInertiaApp` untuk register PrimeVue, ToastService, ConfirmationService, DialogService ke Vue app instance.

#### 0.4 Update `resources/views/app.blade.php`
- **Keep:** `@vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])` (sudah Vue, TIDAK perlu ganti)
- **Keep:** Dark mode inline script dan `<style>` block (bisa di-review nanti, PrimeVue punya dark mode sendiri tapi tidak masalah jika dipertahankan)
- **Keep:** `@fonts`, `@inertia::head`, `@inertia::app`, meta tags, favicon links
- **Add:** `csrf-token` meta tag (jika belum ada)

#### 0.5 Update `resources/css/app.css`
- **Add:** `@import "tailwindcss-primeui";`
- **Add:** ITATS brand colors in `@theme` block (primary blue #00479b, accent orange #f36f21)
- **Add:** Custom utility classes dari old project (`.th`, `.link`, `textarea` padding, `.edited`)
- **Remove/Replace:** shadcn-style CSS variables (--background, --foreground, --card, --primary, dll. dengan hsl() values) — ganti dengan PrimeVue theme variables atau pertahankan untuk kompatibilitas
- **Remove:** `@import 'tw-animate-css';` (tidak diperlukan dengan PrimeVue)
- **Keep:** `@import 'tailwindcss';`, `@source` directives, `@custom-variant dark`, border compatibility styles

#### 0.6 Delete shadcn-style UI components
- **Delete all:** `resources/js/components/ui/` (semua shadcn-style components: sidebar, button, sheet, card, dialog, select, breadcrumb, spinner, alert, badge, input, label, dropdown-menu, tooltip, sonner, navigation-menu, separator, checkbox, skeleton, collapsible, avatar)
- **Delete:** `resources/js/lib/utils.ts` (shadcn `cn()` utility — TIDAK diperlukan dengan PrimeVue)
- **Keep:** `resources/js/lib/flashToast.ts` (masih berguna untuk Inertia flash messages)
- **Keep:** `resources/js/actions/` dan `resources/js/wayfinder/` (Wayfinder generated, language-agnostic TypeScript)
- **Keep:** `resources/js/types/vue-shims.d.ts`, `resources/js/types/global.d.ts`

#### 0.7 Adapt existing layouts & components
Layouts dan components yang ada saat ini menggunakan shadcn-style UI (reka-ui). Semua perlu di-adaptasi atau di-replace dengan PrimeVue equivalents.

- **Modify:** `resources/js/layouts/AppLayout.vue` — Replace shadcn sidebar, header, dropdown components dengan PrimeVue equivalents (Drawer/Panel untuk sidebar, Menu untuk navigation, Dropdown untuk user menu). Add PrimeVue `<Toast />` component. Source: `Software-SPMI-old/resources/js/components/AppLayout.vue`
  - **Adaptation:** Replace `route()` calls (Ziggy) dengan Wayfinder function imports dari `@/actions/` atau `@/routes/`
  - **Adaptation:** Use `usePage()` dari `@inertiajs/vue3` untuk auth user data
  - **Role-based menu:** Implementasi menuItems array dengan roles filter (sama seperti old project)
- **Modify:** `resources/js/layouts/AuthLayout.vue` — Simplify ke centered card layout untuk login. Source: old project's Auth/Login.vue layout pattern
- **Modify:** `resources/js/layouts/settings/Layout.vue` — Adaptasi untuk PrimeVue components
- **Delete/Replace:** `resources/js/components/AppSidebar.vue`, `AppHeader.vue`, `AppShell.vue`, `NavMain.vue`, `NavFooter.vue`, `NavUser.vue`, `UserMenuContent.vue`, `AppLogo.vue`, `AppLogoIcon.vue`, `AppContent.vue`, `AppSidebarHeader.vue`, `Breadcrumbs.vue`, `TextLink.vue`, `Heading.vue`, `PasswordInput.vue`, `PlaceholderPattern.vue`, `AppearanceTabs.vue`, `DeleteUser.vue`, `UserInfo.vue`, `AlertError.vue`, `InputError.vue` — Replace dengan PrimeVue-based equivalents atau adaptasi
- **Keep/Adapt:** `resources/js/composables/useAppearance.ts` (masih berguna untuk dark mode toggle), `useInitials.ts`, `useCurrentUrl.ts`
- **Create:** `resources/js/types/index.ts` — TypeScript interfaces untuk SPMI models (User, Role, UnitKerja, PeriodeAMI, dll.)

### Phase 1: Dynamic Config & .env Setup

Pastikan semua config dynamic via `env()` dengan sensible defaults. Tidak ada hardcoded values.

#### 1.1 Audit `config/database.php`
- **Current state:** Sudah menggunakan `env()` properly (standard Laravel 13). Supports sqlite, mysql, mariadb, pgsql, sqlsrv
- **Action:** Verify all connections use `env()` for host, port, database, username, password, charset, collation
- **No changes needed** — config is already dynamic

#### 1.2 Update `.env.example`
- **File:** `.env.example`
- **Changes:**
  - Set `APP_NAME=SPMI` (instead of `Laravel`)
  - Set `APP_LOCALE=id` dan `APP_FALLBACK_LOCALE=id` (Indonesian)
  - Set `APP_FAKER_LOCALE=id_ID`
  - Uncomment MySQL config lines dan set sensible defaults:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=spmi
    DB_USERNAME=root
    DB_PASSWORD=
    ```
  - Keep `SESSION_DOMAIN=null` (sudah ada)
  - Keep `VITE_APP_NAME="${APP_NAME}"` (sudah ada)

#### 1.3 Create `.env` (local development)
- **File:** `.env` (copy from `.env.example`)
- **Set:** `APP_NAME=SPMI`, `APP_KEY=` (generate dengan `php artisan key:generate`)
- **Set:** `DB_CONNECTION=mysql` atau `sqlite` depending on local setup
- **Set:** `APP_DEBUG=true`

#### 1.4 Document config caching behavior
- **Add to verification steps:** Selalu run `php artisan config:clear` setelah mengubah `.env`
- **Note:** Jangan pernah run `config:cache` di development. `config:cache` membuat perubahan `.env` tidak efektif sampai `config:clear` dijalankan.
- **Composer scripts:** Project sudah punya `composer test` yang runs `config:clear` first — ini sudah correct.

#### 1.5 Audit all config files for hardcoded values
- **Check:** `config/app.php`, `config/auth.php`, `config/session.php`, `config/cache.php`, `config/queue.php`, `config/mail.php`, `config/filesystems.php`, `config/logging.php`, `config/services.php`, `config/fortify.php`
- **Ensure:** Semua environment-specific values menggunakan `env()` dengan defaults
- **Specifically check:** `config/fortify.php` — `home` path `/dashboard` adalah path, bukan environment value, jadi hardcoded is acceptable

### Phase 2: Database Layer (Migrations)

Buat 11 migration files untuk tabel domain SPMI. Gunakan `php artisan make:migration --no-interaction` untuk setiap file.

#### 2.1 Migration: `roles` table
- **File:** `database/migrations/2026_01_01_000001_create_roles_table.php`
- **Columns:** `id`, `nama_role` (string), timestamps
- **Source:** `Software-SPMI-old/database/migrations/2026_04_24_000001_create_roles_table.php`

#### 2.2 Migration: `unit_kerja` table
- **File:** `database/migrations/2026_01_01_000002_create_unit_kerja_table.php`
- **Columns:** `id`, `nama_unit` (string), `jenis_unit` (enum: Fakultas, Program Studi, Biro, Lembaga), `kepala_unit_id` (unsignedBigInteger, nullable, FK to users), timestamps
- **Source:** `Software-SPMI-old/database/migrations/2026_04_24_000002_create_unit_kerja_table.php`

#### 2.3 Migration: `periode_ami` table
- **File:** `database/migrations/2026_01_01_000003_create_periode_ami_table.php`
- **Columns:** `id`, `tahun_akademik` (string), `tgl_mulai_audit` (date), `tgl_selesai_audit` (date), `status` (enum: Draft, Pelaksanaan EDOM, Audit Lapangan, RTM, Selesai), timestamps
- **Source:** `Software-SPMI-old/database/migrations/2026_04_24_000003_create_periode_ami_table.php`

#### 2.4 Migration: `kategori_standar` table
- **File:** `database/migrations/2026_01_01_000004_create_kategori_standar_table.php`
- **Columns:** `id`, `nama_kategori` (string), `is_default` (boolean, default false), timestamps
- **Source:** `Software-SPMI-old/database/migrations/2026_04_24_000004_create_kategori_standar_table.php`

#### 2.5 Migration: `standar_dikti` table
- **File:** `database/migrations/2026_01_01_000005_create_standar_dikti_table.php`
- **Columns:** `id`, `periode_id` (FK to periode_ami, cascade), `kategori_id` (FK to kategori_standar, cascade), `nama_standar` (string), timestamps
- **Source:** `Software-SPMI-old/database/migrations/2026_04_24_000005_create_standar_dikti_table.php`

#### 2.6 Migration: `indikator_mutu` table
- **File:** `database/migrations/2026_01_01_000006_create_indikator_mutu_table.php`
- **Columns:** `id`, `standar_id` (FK to standar_dikti, cascade), `kode_indikator` (string), `isi_standar` (text), `jenis` (enum: IKU, IKT), timestamps
- **Unique index:** `standar_id` + `kode_indikator`
- **Source:** `Software-SPMI-old/database/migrations/2026_04_24_000006` + unique index from `2026_05_07_055501`

#### 2.7 Migration: `target_unit` table
- **File:** `database/migrations/2026_01_01_000007_create_target_unit_table.php`
- **Columns:** `id`, `indikator_id` (FK to indikator_mutu, cascade), `unit_kerja_id` (FK to unit_kerja, cascade), `nilai_target` (float), `satuan` (string), timestamps
- **Unique index:** `indikator_id` + `unit_kerja_id`
- **Composite index:** `unit_kerja_id` + `indikator_id`
- **Source:** `Software-SPMI-old/database/migrations/2026_04_24_000007` + indexes from `2026_05_07_022126` and `2026_05_07_055501`

#### 2.8 Migration: `capaian_pelaksanaan` table
- **File:** `database/migrations/2026_01_01_000008_create_capaian_pelaksanaan_table.php`
- **Columns:** `id`, `target_unit_id` (FK to target_unit, cascade), `nilai_aktual` (float, nullable), `evaluasi_diri` (text, nullable), `link_dokumen_bukti` (string, nullable), `submitted_by` (FK to users, set null), `submitted_at` (timestamp, nullable), timestamps
- **Source:** `Software-SPMI-old/database/migrations/2026_04_24_000008_create_capaian_pelaksanaan_table.php`

#### 2.9 Migration: `kertas_kerja_audit` table
- **File:** `database/migrations/2026_01_01_000009_create_kertas_kerja_audit_table.php`
- **Columns:** `id`, `capaian_id` (FK to capaian_pelaksanaan, cascade), `auditor_id` (FK to users, cascade), `kategori_temuan` (enum: Sesuai, Melampaui, Observasi (OB), KTS Minor, KTS Mayor), `deskripsi_temuan` (text, nullable), timestamps
- **Composite index:** `capaian_id` + `kategori_temuan`
- **Source:** `Software-SPMI-old/database/migrations/2026_04_24_000009` + index from `2026_05_07_022126`

#### 2.10 Migration: `tindak_lanjut_ptk` table
- **File:** `database/migrations/2026_01_01_000010_create_tindak_lanjut_ptk_table.php`
- **Columns:** `id`, `kka_id` (FK to kertas_kerja_audit, cascade), `akar_masalah` (text, nullable), `rencana_tindak_lanjut` (text, nullable), `jadwal_penyelesaian` (date, nullable), `status_verifikasi` (enum: Open, Menunggu Verifikasi, Closed, default Open), timestamps
- **Source:** `Software-SPMI-old/database/migrations/2026_04_24_000010_create_tindak_lanjut_ptk_table.php`

#### 2.11 Migration: `risalah_rtm` table
- **File:** `database/migrations/2026_01_01_000011_create_risalah_rtm_table.php`
- **Columns:** `id`, `periode_id` (FK to periode_ami, cascade), `unit_kerja_id` (FK to unit_kerja, cascade), `tgl_rtm` (date), `pimpinan_rapat` (string), `isi_risalah` (text), `keputusan_peningkatan` (text), timestamps
- **Source:** `Software-SPMI-old/database/migrations/2026_04_24_000011_create_risalah_rtm_table.php`

#### 2.12 Modify: `users` table migration
- **File:** `database/migrations/0001_01_01_000000_create_users_table.php` (existing)
- **Changes:** Replace `name` dengan `nama_lengkap`, add `nidn` (nullable string), `jenis_user` (enum: Dosen, Tenaga Kependidikan, nullable), `role_id` (foreignId to roles, nullable), `unit_kerja_id` (foreignId to unit_kerja, nullable)
- **Keep:** `email`, `email_verified_at`, `password`, `remember_token`, timestamps
- **Note:** Migration ini runs before domain migrations, jadi FK constraints ke `roles` dan `unit_kerja` perlu di-add di separate migration setelah tables tersebut exist

**Revised approach:** Modify users migration untuk add SPMI fields sebagai nullable columns tanpa FK constraints. Lalu buat separate migration `2026_01_01_000012_add_foreign_keys_to_users_table.php` untuk add FK constraints setelah roles dan unit_kerja tables exist.

### Phase 3: Models

Buat 11 model files (User sudah ada, perlu modification). Gunakan `php artisan make:model --no-interaction` untuk setiap model.

#### 3.1 Modify: `app/Models/User.php`
- **Changes:** Add SPMI fields to fillable (`nama_lengkap`, `nidn`, `jenis_user`, `role_id`, `unit_kerja_id`), add relationships (`role()`, `unitKerja()`), add helper methods (`isAdmin()`, `isAuditor()`, `isAuditee()`, `isPimpinan()`)
- **Convention:** Use PHP 8 attributes (`#[Fillable(...)]`, `#[Hidden(...)]`) sesuai existing convention di new project
- **Source:** `Software-SPMI-old/app/Models/User.php`

#### 3.2 Create: `app/Models/Role.php`
- **Fillable:** `nama_role`
- **Relationships:** `users()` -> hasMany(User)
- **Source:** `Software-SPMI-old/app/Models/Role.php`

#### 3.3 Create: `app/Models/UnitKerja.php`
- **Table:** `unit_kerja`
- **Fillable:** `nama_unit`, `jenis_unit`, `kepala_unit_id`
- **Relationships:** `kepalaUnit()` -> belongsTo(User, 'kepala_unit_id'), `users()` -> hasMany(User), `targetUnits()` -> hasMany(TargetUnit), `risalahRtms()` -> hasMany(RisalahRtm)
- **Source:** `Software-SPMI-old/app/Models/UnitKerja.php`

#### 3.4 Create: `app/Models/PeriodeAMI.php`
- **Table:** `periode_ami`
- **Fillable:** `tahun_akademik`, `tgl_mulai_audit`, `tgl_selesai_audit`, `status`
- **Relationships:** `standarDiktis()` -> hasMany(StandarDikti, 'periode_id'), `risalahRtms()` -> hasMany(RisalahRtm, 'periode_id')
- **Casts:** `tgl_mulai_audit` -> date, `tgl_selesai_audit` -> date
- **Source:** `Software-SPMI-old/app/Models/PeriodeAMI.php`

#### 3.5 Create: `app/Models/KategoriStandar.php`
- **Table:** `kategori_standar`
- **Fillable:** `nama_kategori`, `is_default`
- **Casts:** `is_default` -> boolean
- **Relationships:** `standarDiktis()` -> hasMany(StandarDikti, 'kategori_id')
- **Source:** `Software-SPMI-old/app/Models/KategoriStandar.php`

#### 3.6 Create: `app/Models/StandarDikti.php`
- **Table:** `standar_dikti`
- **Fillable:** `periode_id`, `kategori_id`, `nama_standar`
- **Relationships:** `periode()` -> belongsTo(PeriodeAMI, 'periode_id'), `kategori()` -> belongsTo(KategoriStandar, 'kategori_id'), `indikatorMutus()` -> hasMany(IndikatorMutu, 'standar_id')
- **Source:** `Software-SPMI-old/app/Models/StandarDikti.php`

#### 3.7 Create: `app/Models/IndikatorMutu.php`
- **Table:** `indikator_mutu`
- **Fillable:** `standar_id`, `kode_indikator`, `isi_standar`, `jenis`
- **Relationships:** `standar()` -> belongsTo(StandarDikti, 'standar_id'), `targetUnits()` -> hasMany(TargetUnit, 'indikator_id')
- **Source:** `Software-SPMI-old/app/Models/IndikatorMutu.php`

#### 3.8 Create: `app/Models/TargetUnit.php`
- **Table:** `target_unit`
- **Fillable:** `indikator_id`, `unit_kerja_id`, `nilai_target`, `satuan`
- **Relationships:** `indikatorMutu()` -> belongsTo(IndikatorMutu, 'indikator_id'), `unitKerja()` -> belongsTo(UnitKerja, 'unit_kerja_id'), `capaianPelaksanaan()` -> hasOne(CapaianPelaksanaan, 'target_unit_id')
- **Source:** `Software-SPMI-old/app/Models/TargetUnit.php`

#### 3.9 Create: `app/Models/CapaianPelaksanaan.php`
- **Table:** `capaian_pelaksanaan`
- **Fillable:** `target_unit_id`, `nilai_aktual`, `evaluasi_diri`, `link_dokumen_bukti`, `submitted_by`, `submitted_at`
- **Casts:** `submitted_at` -> datetime
- **Relationships:** `targetUnit()` -> belongsTo(TargetUnit, 'target_unit_id'), `submittedBy()` -> belongsTo(User, 'submitted_by'), `kertasKerjaAudit()` -> hasOne(KertasKerjaAudit, 'capaian_id')
- **Source:** `Software-SPMI-old/app/Models/CapaianPelaksanaan.php`

#### 3.10 Create: `app/Models/KertasKerjaAudit.php`
- **Table:** `kertas_kerja_audit`
- **Fillable:** `capaian_id`, `auditor_id`, `kategori_temuan`, `deskripsi_temuan`
- **Relationships:** `capaianPelaksanaan()` -> belongsTo(CapaianPelaksanaan, 'capaian_id'), `auditor()` -> belongsTo(User, 'auditor_id'), `tindakLanjut()` -> hasOne(TindakLanjutPtk, 'kka_id')
- **Source:** `Software-SPMI-old/app/Models/KertasKerjaAudit.php`

#### 3.11 Create: `app/Models/TindakLanjutPtk.php`
- **Table:** `tindak_lanjut_ptk`
- **Fillable:** `kka_id`, `akar_masalah`, `rencana_tindak_lanjut`, `jadwal_penyelesaian`, `status_verifikasi`
- **Casts:** `jadwal_penyelesaian` -> date
- **Relationships:** `kertasKerjaAudit()` -> belongsTo(KertasKerjaAudit, 'kka_id')
- **Source:** `Software-SPMI-old/app/Models/TindakLanjutPtk.php`

#### 3.12 Create: `app/Models/RisalahRtm.php`
- **Table:** `risalah_rtm`
- **Fillable:** `periode_id`, `unit_kerja_id`, `tgl_rtm`, `pimpinan_rapat`, `isi_risalah`, `keputusan_peningkatan`
- **Casts:** `tgl_rtm` -> date
- **Relationships:** `periode()` -> belongsTo(PeriodeAMI, 'periode_id'), `unitKerja()` -> belongsTo(UnitKerja, 'unit_kerja_id')
- **Source:** `Software-SPMI-old/app/Models/RisalahRtm.php`

### Phase 4: Factories & Seeders

#### 4.1 Factories
Buat factory untuk setiap model baru (11 files). Gunakan `php artisan make:model --factory --no-interaction` atau buat manual.
- `database/factories/RoleFactory.php`
- `database/factories/UnitKerjaFactory.php`
- `database/factories/PeriodeAMIFactory.php`
- `database/factories/KategoriStandarFactory.php`
- `database/factories/StandarDiktiFactory.php`
- `database/factories/IndikatorMutuFactory.php`
- `database/factories/TargetUnitFactory.php`
- `database/factories/CapaianPelaksanaanFactory.php`
- `database/factories/KertasKerjaAuditFactory.php`
- `database/factories/TindakLanjutPtkFactory.php`
- `database/factories/RisalahRtmFactory.php`
- Modify existing `database/factories/UserFactory.php` untuk include SPMI fields

#### 4.2 Seeders
Buat 5 seeder files. Gunakan `php artisan make:seeder --no-interaction`.
- `database/seeders/RoleSeeder.php` - Creates 4 roles: Admin/LPM, Pimpinan, Auditor, Auditee
- `database/seeders/UnitKerjaSeeder.php` - Creates 6 units (2 Fakultas, 2 Prodi, 1 Lembaga, 1 Biro)
- `database/seeders/KategoriStandarSeeder.php` - Creates 3 categories: Pendidikan, Penelitian, Pengabdian Masyarakat
- `database/seeders/UserSeeder.php` - Creates 4 demo users (admin, rektor, auditor, auditee), all password: `password`
- `database/seeders/RealisticDataSeeder.php` - Creates comprehensive test data: 2 periodes, standar/indikator/targets, capaian, KKA findings, tindak lanjut, risalah RTM
- Modify `database/seeders/DatabaseSeeder.php` untuk call all seeders in order

**Source:** All from `Software-SPMI-old/database/seeders/`

### Phase 5: Middleware

#### 5.1 Create: `app/Http/Middleware/CheckRole.php`
- **Alias:** `role`
- **Logic:** Accept variadic role names; redirect to `/login` if not authenticated, to `/dashboard` if unauthorized
- **Source:** `Software-SPMI-old/app/Http/Middleware/CheckRole.php`

#### 5.2 Register middleware alias in `bootstrap/app.php`
- Add `'role' => \App\Http\Middleware\CheckRole::class` to middleware aliases

#### 5.3 Modify: `app/Http/Middleware/HandleInertiaRequests.php`
- Share `auth.user` dengan role dan unitKerja eager-loaded
- Share flash messages (success, error) untuk Inertia
- **Current state:** Shares `auth.user` (without eager loading), `name`, `sidebarOpen`
- **Changes:** Eager load `role` dan `unitKerja` on user, add flash message sharing

### Phase 6: Controllers

Buat controllers organized by PPEPP domain. Gunakan `php artisan make:controller --no-interaction`. Use Form Requests for validation where appropriate (Laravel 13 best practice).

#### 6.1 Dashboard Controller
- **File:** `app/Http/Controllers/DashboardController.php` (invokable)
- **Logic:** Load user dengan role/unitKerja, fetch periodes, compute stats (total_standar, total_indikator, total_temuan) menggunakan JOIN queries
- **Source:** `Software-SPMI-old/app/Http/Controllers/DashboardController.php`

#### 6.2 Master Controllers (Admin/LPM only)
- `app/Http/Controllers/Master/UserController.php` - Resource: index, store, update, destroy
- `app/Http/Controllers/Master/UnitKerjaController.php` - Resource: index, store, update, destroy
- `app/Http/Controllers/Master/KategoriStandarController.php` - Resource: index, store, update, destroy
- **Source:** `Software-SPMI-old/app/Http/Controllers/Master/`

#### 6.3 Penetapan Controllers (Admin/LPM only)
- `app/Http/Controllers/Penetapan/PeriodeController.php` - Resource: index, store, update, destroy
- `app/Http/Controllers/Penetapan/StandarController.php` - Resource: index, store, update, destroy
- `app/Http/Controllers/Penetapan/IndikatorMutuController.php` - Resource: index, store, update, destroy
- `app/Http/Controllers/Penetapan/ImportStandarController.php` - index, store (Excel import)
- `app/Http/Controllers/Penetapan/DistribusiTargetController.php` - index, store (target distribution matrix)
- **Source:** `Software-SPMI-old/app/Http/Controllers/Penetapan/`

#### 6.4 Pelaksanaan Controllers (Auditee only)
- `app/Http/Controllers/Pelaksanaan/EvaluasiDiriController.php` - index, store (self-evaluation/EDOM)
- **Source:** `Software-SPMI-old/app/Http/Controllers/Pelaksanaan/EvaluasiDiriController.php`

#### 6.5 Evaluasi Controllers (Auditor only)
- `app/Http/Controllers/Evaluasi/JadwalAuditController.php` - index (audit schedule)
- `app/Http/Controllers/Evaluasi/KKAController.php` - show, store (audit worksheet)
- **Source:** `Software-SPMI-old/app/Http/Controllers/Evaluasi/`

#### 6.6 Pengendalian Controllers (Auditee only)
- `app/Http/Controllers/Pengendalian/RtlController.php` - index, store (RTL creation)
- **Source:** `Software-SPMI-old/app/Http/Controllers/Pengendalian/RtlController.php`

#### 6.7 Peningkatan Controllers (Pimpinan only)
- `app/Http/Controllers/Peningkatan/RisalahRtmController.php` - index, store, update, destroy
- **Source:** `Software-SPMI-old/app/Http/Controllers/Peningkatan/RisalahRtmController.php`

### Phase 7: Routes

#### 7.1 Modify: `routes/web.php`
Replace the starter kit routes dengan SPMI routes:

```php
// Public
Route::inertia('/', 'Welcome')->name('home');

// Authenticated
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Master Data (Admin/LPM only)
    Route::middleware('role:Admin/LPM')->prefix('master')->name('master.')->group(function () {
        Route::resource('users', Master\UserController::class);
        Route::resource('unit-kerja', Master\UnitKerjaController::class);
        Route::resource('kategori-standar', Master\KategoriStandarController::class);
    });

    // Penetapan (Admin/LPM only)
    Route::middleware('role:Admin/LPM')->prefix('penetapan')->name('penetapan.')->group(function () {
        Route::resource('periode', Penetapan\PeriodeController::class);
        Route::resource('standar', Penetapan\StandarController::class);
        Route::get('/import-standar', [Penetapan\ImportStandarController::class, 'index'])->name('standar.import.index');
        Route::post('/import-standar', [Penetapan\ImportStandarController::class, 'store'])->name('standar.import.store');
        Route::resource('indikator', Penetapan\IndikatorMutuController::class);
        Route::get('/distribusi-target', [Penetapan\DistribusiTargetController::class, 'index'])->name('distribusi-target.index');
        Route::post('/distribusi-target', [Penetapan\DistribusiTargetController::class, 'store'])->name('distribusi-target.store');
    });

    // Pelaksanaan (Auditee only)
    Route::middleware('role:Auditee')->prefix('pelaksanaan')->name('pelaksanaan.')->group(function () {
        Route::get('/evaluasi-diri', [Pelaksanaan\EvaluasiDiriController::class, 'index'])->name('evaluasi-diri.index');
        Route::post('/evaluasi-diri/{targetUnit}', [Pelaksanaan\EvaluasiDiriController::class, 'store'])->name('evaluasi-diri.store');
    });

    // Evaluasi (Auditor only)
    Route::middleware('role:Auditor')->prefix('evaluasi')->name('evaluasi.')->group(function () {
        Route::get('/jadwal-audit', [Evaluasi\JadwalAuditController::class, 'index'])->name('jadwal-audit.index');
        Route::get('/kka/{unit}', [Evaluasi\KKAController::class, 'show'])->name('kka.show');
        Route::post('/kka/{capaian}', [Evaluasi\KKAController::class, 'store'])->name('kka.store');
    });

    // Pengendalian (Auditee only)
    Route::middleware('role:Auditee')->prefix('pengendalian')->name('pengendalian.')->group(function () {
        Route::get('/isi-rtl', [Pengendalian\RtlController::class, 'index'])->name('isi-rtl.index');
        Route::post('/isi-rtl/{kka}', [Pengendalian\RtlController::class, 'store'])->name('isi-rtl.store');
    });

    // Peningkatan (Pimpinan only)
    Route::middleware('role:Pimpinan')->prefix('peningkatan')->name('peningkatan.')->group(function () {
        Route::get('/risalah', [Peningkatan\RisalahRtmController::class, 'index'])->name('risalah.index');
        Route::post('/risalah', [Peningkatan\RisalahRtmController::class, 'store'])->name('risalah.store');
        Route::put('/risalah/{risalah}', [Peningkatan\RisalahRtmController::class, 'update'])->name('risalah.update');
        Route::delete('/risalah/{risalah}', [Peningkatan\RisalahRtmController::class, 'destroy'])->name('risalah.destroy');
    });
});

require __DIR__.'/settings.php';
```

**Note:** Fortify handles login/register/logout routes automatically. The logout route is handled by Fortify's `AuthenticatedSessionController@destroy`. Remove the custom AccountController since Fortify provides auth.

### Phase 8: Auth & Fortify Configuration

#### 8.1 Modify: `config/fortify.php`
- Keep `home` => `/dashboard`
- **Disable registration:** Remove `Features::registration()` — users created only by Admin/LPM via Master/UserController
- Keep `Features::resetPasswords()`

#### 8.2 Modify: `app/Actions/Fortify/CreateNewUser.php`
- Since registration is disabled, this file can remain as-is or be removed. Keep it for potential future use but it won't be called.

#### 8.3 Modify: `app/Models/User.php`
- Add SPMI fields, relationships, dan role helper methods as described in Phase 3.1

### Phase 9: Frontend (Vue Pages)

Buat/adapt Vue pages untuk setiap halaman SPMI. Semua pages menggunakan Inertia v3 + Vue 3 + PrimeVue 4 components. Adaptasi dari old project Vue pages dengan perubahan: ganti `route()` (Ziggy) dengan Wayfinder function imports.

**Note:** Project baru sudah punya 9 Vue pages (Dashboard, Welcome, auth/Login, auth/Register, auth/ResetPassword, auth/ForgotPassword, settings/Profile, settings/Security, settings/Appearance) yang menggunakan shadcn-style components. Pages ini perlu di-adaptasi ke PrimeVue atau di-replace dengan SPMI-specific pages.

#### 9.1 Modify: `resources/js/pages/Dashboard.vue`
- **Current:** Starter kit dashboard dengan shadcn components
- **Target:** SPMI dashboard dengan PrimeVue components
- **Source:** `Software-SPMI-old/resources/js/Pages/Dashboard/Index.vue`
- **Adaptation:** Replace `route()` calls dengan Wayfinder imports dari `@/routes/` atau `@/actions/`
- **Content:** Periode selector, summary cards (total standar, indikator, temuan), user info

#### 9.2 Modify: Auth Login Page
- **File:** `resources/js/pages/auth/Login.vue` (existing, needs adaptation)
- **Source:** `Software-SPMI-old/resources/js/Pages/Auth/Login.vue`
- **Adaptation:** Use Fortify-compatible form action (Wayfinder `login.store`), add ITATS branding, replace shadcn form components dengan PrimeVue (InputText, Password, Button)

#### 9.3 Modify: Auth Register Page
- **File:** `resources/js/pages/auth/Register.vue` (existing)
- **Action:** Delete atau hide (registration disabled via Fortify config). Jika Fortify views=true dan registration disabled, route tidak akan terdaftar.

#### 9.4 Modify: Settings Pages
- `resources/js/pages/settings/Profile.vue` - Adaptasi ke PrimeVue form components
- `resources/js/pages/settings/Security.vue` - Adaptasi ke PrimeVue form components
- `resources/js/pages/settings/Appearance.vue` - Adaptasi ke PrimeVue form components
- **Note:** These pages work with Fortify dan berguna untuk user profile management. Adaptasi dari shadcn ke PrimeVue.

#### 9.5 Modify: Welcome Page
- **File:** `resources/js/pages/Welcome.vue` (existing)
- **Action:** Adaptasi ke PrimeVue atau buat simple landing page dengan ITATS branding

#### 9.6 Create: Master Pages
- `resources/js/pages/master/users/index.vue` - User management table dengan CRUD (PrimeVue DataTable, Dialog)
  - **Source:** `Software-SPMI-old/resources/js/Pages/Master/Users/Index.vue`
- `resources/js/pages/master/unit-kerja/index.vue` - Unit Kerja management table dengan CRUD
  - **Source:** `Software-SPMI-old/resources/js/Pages/Master/UnitKerja/Index.vue`
- `resources/js/pages/master/kategori-standar/index.vue` - Category management table dengan CRUD
  - **Source:** `Software-SPMI-old/resources/js/Pages/Master/KategoriStandar/Index.vue`

#### 9.7 Create: Penetapan Pages
- `resources/js/pages/penetapan/periode/index.vue` - Periode AMI management
  - **Source:** `Software-SPMI-old/resources/js/Pages/Penetapan/Periode/Index.vue`
- `resources/js/pages/penetapan/standar/index.vue` - Standar Dikti management dengan periode filter
  - **Source:** `Software-SPMI-old/resources/js/Pages/Penetapan/Standar/Index.vue`
- `resources/js/pages/penetapan/indikator/index.vue` - Indikator Mutu management dengan standar filter
  - **Source:** `Software-SPMI-old/resources/js/Pages/Penetapan/Indikator/Index.vue`
- `resources/js/pages/penetapan/import.vue` - Excel import page (client-side XLSX parsing)
  - **Source:** `Software-SPMI-old/resources/js/Pages/Penetapan/Import.vue`
- `resources/js/pages/penetapan/distribusi-target/index.vue` - Target distribution matrix
  - **Source:** `Software-SPMI-old/resources/js/Pages/Penetapan/DistribusiTarget/Index.vue`

#### 9.8 Create: Pelaksanaan Pages
- `resources/js/pages/pelaksanaan/evaluasi-diri/index.vue` - Self-evaluation (EDOM) form
  - **Source:** `Software-SPMI-old/resources/js/Pages/Pelaksanaan/EvaluasiDiri/Index.vue`

#### 9.9 Create: Evaluasi Pages
- `resources/js/pages/evaluasi/jadwal-audit/index.vue` - Audit schedule dengan unit list
  - **Source:** `Software-SPMI-old/resources/js/Pages/Evaluasi/JadwalAudit/Index.vue`
- `resources/js/pages/evaluasi/kka/show.vue` - Audit worksheet (KKA) dengan paginated data
  - **Source:** `Software-SPMI-old/resources/js/Pages/Evaluasi/KKA/Show.vue`

#### 9.10 Create: Pengendalian Pages
- `resources/js/pages/pengendalian/isi-rtl/index.vue` - RTL creation form
  - **Source:** `Software-SPMI-old/resources/js/Pages/Pengendalian/IsiRtl/Index.vue`

#### 9.11 Create: Peningkatan Pages
- `resources/js/pages/peningkatan/risalah/index.vue` - RTM risalah management
  - **Source:** `Software-SPMI-old/resources/js/Pages/Peningkatan/Risalah/Index.vue`

#### 9.12 Wayfinder Adaptation Pattern
All Vue pages dari old project menggunakan Ziggy's `route()` function untuk generating URLs. Di new project, these harus di-replace dengan Wayfinder function imports:

**Old (Ziggy):**
```vue
<script setup>
import { router } from '@inertiajs/vue3';
// Uses route() from Ziggy
router.get(route('penetapan.standar.index', { periode: 1 }));
</script>

<template>
  <Link :href="route('dashboard')">Dashboard</Link>
</template>
```

**New (Wayfinder):**
```vue
<script setup>
import { router } from '@inertiajs/vue3';
import { index as standarIndex } from '@/actions/App/Http/Controllers/Penetapan/StandarController';
// Uses Wayfinder function
router.get(standarIndex.url({ periode: 1 }));
</script>

<template>
  <Link :href="dashboard.url()">Dashboard</Link>
</template>
```

**Note:** Setelah routes didefinisikan, run `php artisan wayfinder:generate` untuk regenerate TypeScript route functions. Lalu import dari `@/actions/` (controller-based) atau `@/routes/` (named route-based).

### Phase 10: Excel Import Support

#### 10.1 Install: `xlsx` npm package
- **Command:** `npm install xlsx`
- **Purpose:** Client-side Excel parsing untuk ImportStandar feature (sama seperti old project)
- **Already included in Phase 0.1 package.json update**

#### 10.2 Create: Excel import composable/component
- **File:** `resources/js/composables/useExcelImport.ts` atau inline di `penetapan/import.vue`
- **Logic:** Parse XLSX file client-side menggunakan `xlsx` library, convert to JSON, send to backend via Inertia `router.post()`
- **Source:** `Software-SPMI-old/resources/js/components/upload/XlsxRead.vue` dan `XlsxJson.vue`

### Phase 11: Testing

#### 11.1 Create Pest Tests
Buat Pest feature tests untuk setiap PPEPP domain:
- `tests/Feature/Auth/AuthTest.php` - Login/logout flows
- `tests/Feature/Penetapan/PenetapanTest.php` - Periode, standar, indikator CRUD
- `tests/Feature/Pelaksanaan/PelaksanaanTest.php` - Evaluasi diri submission
- `tests/Feature/Evaluasi/EvaluasiTest.php` - KKA submission
- `tests/Feature/Pengendalian/PengendalianTest.php` - RTL submission
- `tests/Feature/Peningkatan/PeningkatanTest.php` - Risalah RTM CRUD

### Phase 12: Code Formatting & Verification

#### 12.1 Run Pint
- `vendor/bin/pint --dirty --format agent`

#### 12.2 Clear Config Cache (IMPORTANT)
- `php artisan config:clear` — Selalu run ini setelah mengubah `.env` untuk ensure config changes take effect
- `php artisan cache:clear` — Clear application cache

#### 12.3 Run Migrations & Seeders
- `php artisan migrate:fresh --seed`

#### 12.4 Regenerate Wayfinder
- `php artisan wayfinder:generate` — Regenerate TypeScript route functions setelah routes didefinisikan

#### 12.5 Run Tests
- `php artisan test --compact`

#### 12.6 Build Frontend
- `npm run build`

## Assumptions & Decisions

1. **Frontend:** Project baru SUDAH menggunakan Vue 3.5 (BUKAN React). UI library diganti dari reka-ui/shadcn ke PrimeVue 4 untuk konsistensi dengan project lama. Semua shadcn-style UI components di `resources/js/components/ui/` akan di-delete dan diganti dengan PrimeVue auto-imported components.
2. **Auth:** Use Fortify (sudah di new project) instead of custom AccountController. Disable public registration — users created only by Admin/LPM.
3. **Route generation:** Use Wayfinder (sudah di new project) instead of Ziggy. All `route()` calls di Vue pages akan di-replace dengan Wayfinder function imports.
4. **Testing:** Use Pest 5 (sudah di new project) instead of PHPUnit.
5. **Users table:** Modify existing `0001_01_01_000000_create_users_table.php` migration untuk include SPMI fields (`nama_lengkap`, `nidn`, `jenis_user`, `role_id`, `unit_kerja_id`). Add FK constraints di separate migration setelah roles dan unit_kerja tables exist.
6. **Legacy code:** Do NOT copy legacy code (PenetapanImport referencing old models, HTTP Resources, old composables, old mixins, homepage/sheets components).
7. **Database:** Support both SQLite (default for development) dan MySQL (for production). Config is dynamic via `env()`.
8. **Excel import:** Use client-side `xlsx` library for parsing (same approach as old project), send JSON to backend.
9. **Role middleware:** Create `CheckRole` middleware dengan alias `role`, same logic as old project.
10. **Inertia shared data:** Share `auth.user` dengan `role` dan `unitKerja` eager-loaded, plus flash messages.
11. **PHP conventions:** Use PHP 8 attributes (`#[Fillable(...)]`, `#[Hidden(...)]`) untuk User model (matching new project convention). Use traditional `$fillable` array untuk other models (matching old project convention dan simplicity).
12. **Settings pages:** Adaptasi existing settings pages (profile, security, appearance) dari shadcn ke PrimeVue. These work with Fortify dan berguna untuk user profile management.
13. **Config:** All config must use `env()` dengan sensible defaults. No hardcoded values. Document `config:clear` sebagai mandatory step setelah `.env` changes. The old project's config was already using `env()` properly — the user's issue was likely caused by config caching (`config:cache` was run, making `.env` changes ineffective until `config:clear`).
14. **PrimeVue theme:** Use Aura theme preset dengan `darkModeSelector: false` (same as old project). ITATS brand colors (primary blue #00479b, accent orange #f36f21) defined di `app.css` `@theme` block.
15. **Vite config:** Keep as `vite.config.ts` (TIDAK perlu rename ke `.js`). Project sudah menggunakan `@vitejs/plugin-vue`. Add `unplugin-vue-components` dengan `PrimeVueResolver` untuk auto-importing PrimeVue components.
16. **App entry:** Keep as `resources/js/app.ts` (TIDAK perlu ganti ke `.js`). Add PrimeVue setup (PrimeVue, Aura theme, ToastService, ConfirmationService, DialogService) ke Vue app instance via `setup` callback dari `createInertiaApp`.
17. **Blade template:** Keep `resources/views/app.blade.php` as-is (sudah Vue, menggunakan `.vue` pages). TIDAK perlu hapus `@viteReactRefresh` (tidak ada). TIDAK perlu ganti `app.tsx` ke `app.js` (sudah `app.ts`).
18. **Existing composables:** Keep `useAppearance.ts`, `useInitials.ts`, `useCurrentUrl.ts` — masih berguna untuk dark mode toggle, user initials, dan URL tracking.
19. **Flash toast:** Keep `resources/js/lib/flashToast.ts` — masih berguna untuk Inertia flash messages dari server.

## Verification Steps

1. Run `php artisan config:clear` — clear any cached config (IMPORTANT: do this first)
2. Run `php artisan migrate:fresh --seed` — verify all tables created dan seeded without errors
3. Run `php artisan wayfinder:generate` — verify TypeScript route functions generated
4. Run `php artisan test --compact` — verify all tests pass
5. Run `npm run build` — verify frontend builds without errors
6. Run `vendor/bin/pint --dirty --format agent` — verify PHP code formatting
7. Manual verification: Login as each role (admin, rektor, auditor, auditee) dan verify:
   - Dashboard shows correct stats
   - Role-based menu items appear correctly
   - Each PPEPP domain page loads dan functions (CRUD operations)
   - Excel import works
   - Target distribution matrix works
8. Config verification: Change a value di `.env`, run `php artisan config:clear`, verify the change takes effect
