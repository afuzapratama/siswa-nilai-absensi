# UI/UX Migration Progress - Bootstrap → Tailwind CSS

> **Dokumentasi ini untuk melacak progress migrasi UI dari Bootstrap 4 (SB Admin 2) ke Tailwind CSS v4**

## 📋 Overview

| Item | Lama | Baru |
|------|------|------|
| CSS Framework | Bootstrap 4 + SB Admin 2 | Tailwind CSS v4.1 |
| Build Tool | - | Vite v7.3 |
| Icon Library | FontAwesome | Lucide Icons |
| JavaScript | jQuery | Vanilla JS (ES6 Modules) |

---

## ✅ Phase 1: Setup & Foundation (COMPLETED)

**Tanggal Mulai:** 17 Desember 2024  
**Status:** ✅ Selesai

### Tasks Completed:
- [x] Install npm dependencies (Tailwind, Vite, PostCSS, Autoprefixer, Lucide)
- [x] Setup Vite configuration (`vite.config.js`)
- [x] Setup Tailwind CSS v4 configuration (`tailwind.config.js`)
- [x] Setup PostCSS configuration (`postcss.config.js`)
- [x] Create custom CSS with component classes (`resources/css/app.css`)
- [x] Create Vite helper for CodeIgniter (`app/Helpers/vite_helper.php`)
- [x] Autoload vite helper (`app/Config/Autoload.php`)
- [x] Create vanilla JS modules:
  - [x] `resources/js/modules/sidebar.js` - Mobile sidebar toggle
  - [x] `resources/js/modules/modal.js` - Modal handler
  - [x] `resources/js/modules/toast.js` - Toast notifications
  - [x] `resources/js/modules/dropdown.js` - Dropdown menus
  - [x] `resources/js/modules/ajax.js` - AJAX/Fetch wrapper with CSRF
- [x] Create main JS entry (`resources/js/app.js`)
- [x] Create new layout template (`app/Views/layout/app.php`)
- [x] Migrate Login page (`app/Views/auth/login_new.php`)
- [x] Migrate Dashboard page (`app/Views/admin/dashboard_new.php`)
- [x] Update AuthController to use new view
- [x] Update Dashboard controller to use new view
- [x] Fix Tailwind v4 CSS compatibility issues
- [x] Fix Filter configuration error
- [x] Database migration & seeder setup
- [x] Create DemoDataSeeder with sample data
- [x] Fix @source directive for Tailwind v4 content scanning
- [x] Create reusable button components

### Files Created/Modified:
```
├── package.json (NEW)
├── vite.config.js (NEW)
├── tailwind.config.js (NEW)
├── postcss.config.js (NEW)
├── resources/
│   ├── css/
│   │   └── app.css (NEW)
│   └── js/
│       ├── app.js (NEW)
│       └── modules/
│           ├── ajax.js (NEW)
│           ├── dropdown.js (NEW)
│           ├── modal.js (NEW)
│           ├── sidebar.js (NEW)
│           └── toast.js (NEW)
├── public/assets/dist/ (BUILD OUTPUT)
├── app/
│   ├── Config/
│   │   ├── Autoload.php (MODIFIED - added vite helper)
│   │   └── Filters.php (MODIFIED - fixed filter config)
│   ├── Controllers/
│   │   ├── Admin/Dashboard.php (MODIFIED)
│   │   └── Auth/AuthController.php (MODIFIED)
│   ├── Helpers/
│   │   └── vite_helper.php (NEW)
│   └── Views/
│       ├── layout/app.php (NEW)
│       ├── auth/login_new.php (NEW)
│       └── admin/dashboard_new.php (NEW)
```

---

## 🚧 Phase 2: Admin Pages Migration (COMPLETED)

**Status:** ✅ Selesai

### Pages Migrated:

#### 2.1 Data Master
- [x] Tahun Ajaran (`app/Views/admin/tahun_ajaran_new.php`) ✅
- [x] Kelas (`app/Views/admin/kelas_new.php`) ✅
- [x] Siswa (`app/Views/admin/siswa_new.php`) ✅
- [x] Mata Pelajaran (`app/Views/admin/mata_pelajaran_new.php`) ✅

#### 2.2 Akademik
- [x] Penilaian List (`app/Views/admin/penilaian_list_new.php`) ✅
- [x] Penilaian Create (`app/Views/admin/penilaian_create_new.php`) ✅
- [x] Penilaian Input (`app/Views/admin/penilaian_form_input_new.php`) ✅
- [x] Absensi Input (`app/Views/admin/absensi_input_new.php`) ✅

#### 2.3 Laporan
- [x] Report Nilai (`app/Views/admin/report_new.php`) ✅
- [x] Rekap Absensi (`app/Views/admin/rekap_absensi_new.php`) ✅

#### 2.4 Settings & Users
- [x] Settings Page (`app/Views/admin/settings_page_new.php`) ✅
- [x] Users Management (`app/Views/admin/users_new.php`) ✅

#### 2.5 Partials
- [x] Siswa Rows (`app/Views/admin/partials/_siswa_rows_new.php`) ✅
- [x] Absensi Table (`app/Views/admin/partials/_absensi_tabel_new.php`) ✅
- [x] Rekap Absensi Table (`app/Views/admin/partials/_rekap_absensi_tabel_new.php`) ✅
- [x] Report Nilai Table (`app/Views/admin/partials/report_table_new.php`) ✅
- [x] Pagination (`app/Views/pagers/tailwind_full.php`) ✅

### Bug Fixes Applied (18 Desember 2025):
- [x] **Layout script section** - Fixed `renderSection('js')` → `renderSection('scripts')`
- [x] **CSRF regenerate** - Changed to `false` in `Security.php` for AJAX compatibility
- [x] **X-Requested-With header** - Added to all fetch requests for AJAX detection
- [x] **Tahun Ajaran status** - Fixed condition from `== 'nonaktif'` to `!== 'aktif'`
- [x] **Input nilai styling** - Changed from `@apply` to inline CSS, hidden spinners
- [x] **Nilai format** - Removed trailing `.00` for whole numbers (100 instead of 100.00)
- [x] **Icon SVG inline** - Replaced Lucide icons with inline SVG in report page
- [x] **Report table** - Removed NIS column from laporan nilai
- [x] **Missing CSS classes** - Added `.table-modern`, `.badge-secondary`, `.form-hint`, `.form-group`, `.modal-title`, `.card-title`
- [x] **Button text visibility** - Removed `hidden sm:inline` from button text

---

## ✅ Phase 3: Polish & Cleanup (COMPLETED)

**Status:** ✅ Selesai  
**Tanggal Selesai:** 18 Desember 2025

### Tasks Completed:
- [x] Move old Bootstrap views to `_backup/` folder (19 files)
- [x] Rename `*_new.php` views to original names
- [x] Update all controllers to use new view names
- [x] Add `_backup/` to `.gitignore`
- [x] Build production assets with Vite
- [x] Documentation update

### Files Moved to Backup:
```
_backup/
├── views/
│   ├── admin/
│   │   ├── absensi_input.php
│   │   ├── dashboard.php
│   │   ├── kelas.php
│   │   ├── mata_pelajaran.php
│   │   ├── penilaian_create.php
│   │   ├── penilaian_form_input.php
│   │   ├── penilaian_list.php
│   │   ├── rekap_absensi.php
│   │   ├── report.php
│   │   ├── settings_page.php
│   │   ├── siswa.php
│   │   ├── tahun_ajaran.php
│   │   ├── users.php
│   │   └── partials/
│   │       ├── _absensi_tabel.php
│   │       ├── _rekap_absensi_tabel.php
│   │       ├── _siswa_rows.php
│   │       └── report_table.php
│   ├── auth/
│   │   └── login.php
│   └── layout/
│       └── template.php
```

### Production Build Output:
```
public/assets/dist/
├── .vite/manifest.json  (0.28 kB)
├── css-Ce5IYtyn.css     (55.87 kB / 9.75 kB gzipped)
└── app-DHKPWppx.js      (7.66 kB / 2.43 kB gzipped)
```

---

## 🛠 Development Commands

```bash
# Start development (run both servers)
npm run dev          # Vite dev server (localhost:5173)
php spark serve      # CI4 server (localhost:8080)

# Build for production
npm run build

# Preview production build
npm run preview
```

---

## 📝 Notes

### Button Components

Tersedia class button reusable untuk konsistensi UI:

```html
<!-- Variants -->
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-success">Success</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-warning">Warning</button>
<button class="btn btn-info">Info</button>
<button class="btn btn-ghost">Ghost</button>

<!-- Outline Variants -->
<button class="btn btn-outline-primary">Outline Primary</button>
<button class="btn btn-outline-danger">Outline Danger</button>
<button class="btn btn-outline-secondary">Outline Secondary</button>

<!-- Sizes -->
<button class="btn btn-primary btn-xs">Extra Small</button>
<button class="btn btn-primary btn-sm">Small</button>
<button class="btn btn-primary">Default</button>
<button class="btn btn-primary btn-lg">Large</button>
<button class="btn btn-primary btn-xl">Extra Large</button>

<!-- Utilities -->
<button class="btn btn-primary btn-block">Full Width</button>
<button class="btn btn-primary btn-icon"><i data-lucide="plus"></i></button>

<!-- Button Group -->
<div class="btn-group">
    <button class="btn btn-primary">One</button>
    <button class="btn btn-primary">Two</button>
    <button class="btn btn-primary">Three</button>
</div>

<!-- Disabled State -->
<button class="btn btn-primary" disabled>Disabled</button>

<!-- Kombinasi -->
<button class="btn btn-success btn-lg btn-block">Submit Form</button>
```

### Tailwind v4 Differences
- Menggunakan `@import 'tailwindcss'` bukan `@tailwind base/components/utilities`
- Custom colors harus di block `@theme { }`
- `@apply` tidak bisa reference class yang didefinisikan di layer yang sama
- Membutuhkan `@tailwindcss/vite` dan `@tailwindcss/postcss` packages

### View Naming Convention
- ~~View lama: `login.php`, `dashboard.php`~~
- ~~View baru: `login_new.php`, `dashboard_new.php`~~
- ✅ **Phase 3 Complete:** All views now use standard names (no `_new` suffix)
- Old Bootstrap views backed up to `_backup/` folder

### Helper Usage
```php
// Di view, load assets dengan:
<?= vite_assets() ?>

// Untuk individual asset:
<?= vite_asset('js/app.js') ?>
```

---

## 📊 Progress Summary

| Phase | Status | Progress |
|-------|--------|----------|
| Phase 1: Setup & Foundation | ✅ Complete | 100% |
| Phase 2: Admin Pages | ✅ Complete | 100% |
| Phase 3: Polish & Cleanup | ✅ Complete | 100% |

**Overall Progress: 100% 🎉**

---

## 🔧 Configuration Changes

### Security.php
```php
// CSRF regenerate disabled for AJAX compatibility
public bool $regenerate = false;
```

### Layout app.php
```php
// Fixed script section rendering
<?= $this->renderSection('scripts') ?>  // Was 'js'
```

### CSS app.css - Added Classes
```css
.table-modern { ... }
.badge-secondary { ... }
.form-hint { ... }
.form-group { ... }
.modal-title { ... }
.card-title { ... }
.btn-info { background: cyan-500 }
```

---

*Last Updated: 18 Desember 2025*  
*Migration Completed: 18 Desember 2025* ✅
