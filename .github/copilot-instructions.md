# Copilot Instructions - Sistem Penilaian Siswa (CodeIgniter 4)

## Project Overview
Student grading and attendance management system built with **CodeIgniter 4** and **MySQL**. Indonesian language UI with admin panel.

**UI/UX Migration Status:** ✅ **COMPLETED** - Migrated from Bootstrap 4 (SB Admin 2) to **Tailwind CSS v4** with vanilla JavaScript.

## Tech Stack
- **Backend:** CodeIgniter 4, PHP 8.x
- **Database:** MySQL 8.0 (Docker container: `mysql-local`, host: `127.0.0.1`)
- **Frontend:** Tailwind CSS v4.1.18, Vite v7.3.0, Vanilla JavaScript ES6
- **Icons:** Lucide Icons v0.511.0
- **PDF Export:** dompdf/dompdf

## Architecture

### Core Domain Entities (Database Tables)
- **tahun_ajaran** → Academic year with active/inactive status (only ONE can be `status = 'aktif'`)
- **kelas** → Classes linked to tahun_ajaran
- **siswa** → Students linked to kelas
- **mapel** → Subjects
- **mapel_kelas** → Pivot table linking subjects to classes
- **penilaian_header** → Assessment form headers (title, class, subject, academic year)
- **penilaian_kolom** → Dynamic columns per assessment (N1, N2, etc.)
- **penilaian_detail** → Individual student grades per column
- **absensi** → Attendance records with status (H/I/S/A)
- **settings** → Key-value application settings (attendance weighting)

### Key Data Flow Pattern
1. **Tahun Ajaran Aktif** is the central filter - almost all queries filter by active academic year
2. **Kelas → Mapel** relationship uses pivot table `mapel_kelas` (not direct FK)
3. **Penilaian** uses 3-table structure: Header → Kolom → Detail for dynamic columns

### Directory Structure
```
app/Controllers/Admin/     # Admin panel controllers
app/Controllers/Api/       # JSON API endpoints
app/Controllers/Auth/      # Authentication
app/Models/               # Models with validation rules
app/Views/admin/          # Admin views (Tailwind CSS)
app/Views/admin/partials/ # AJAX partial views
app/Views/layout/         # Layout templates (app.php = Tailwind)
app/Filters/              # AuthFilter, LoginThrottle
resources/css/            # Tailwind source CSS (app.css)
resources/js/             # Vanilla JS modules
public/assets/dist/       # Compiled assets (Vite output)
_backup/                  # Old Bootstrap views (gitignored)
```

## Development Commands

```bash
# Start development server
php spark serve --port=8080

# Build frontend assets (Tailwind + JS)
npm run build

# Watch mode for development
npm run dev

# Run migrations
php spark migrate

# Run tests
composer test
```

## Frontend Architecture (Tailwind CSS v4)

### Build Configuration
- **Vite config:** `resources/vite.config.js`
- **Tailwind config:** `resources/tailwind.config.js`
- **PostCSS config:** `resources/postcss.config.js`
- **Source files:** `resources/css/app.css`, `resources/js/app.js`
- **Output:** `public/assets/dist/`

### CSS Component Classes (defined in app.css)
```css
/* Buttons */
.btn              /* Base button */
.btn-primary      /* Blue primary */
.btn-secondary    /* Gray */
.btn-success      /* Green */
.btn-danger       /* Red */
.btn-warning      /* Yellow */
.btn-sm           /* Small size */

/* Cards */
.card             /* Card container */
.card-header      /* Card header */
.card-title       /* Card title text */
.card-body        /* Card content */

/* Forms */
.form-group       /* Form group wrapper */
.form-label       /* Label styling */
.form-input       /* Input/select styling */
.form-hint        /* Help text */

/* Alerts */
.alert            /* Base alert */
.alert-success    /* Green */
.alert-danger     /* Red */
.alert-warning    /* Yellow */
.alert-info       /* Blue */

/* Modal */
.modal            /* Hidden by default (display: none) */
.modal.active     /* Shown (display: flex) */
.modal-backdrop   /* Dark overlay (z-index: -1 relative to modal) */
.modal-container  /* Centers content (z-index: 1 relative to modal) */
.modal-content    /* White card */
.modal-header     /* Header with title */
.modal-body       /* Content area */
.modal-footer     /* Action buttons */
```

### JavaScript Modules (resources/js/modules/)
- `sidebar.js` - Sidebar toggle, mobile responsive
- `modal.js` - Modal open/close with `.active` class
- `toast.js` - Toast notifications
- `dropdown.js` - Dropdown menus
- `ajax.js` - AJAX utilities with CSRF handling

### View Naming Convention
- All views use Tailwind CSS (extends `layout/app`)
- Old Bootstrap views backed up to `_backup/` folder (gitignored)

### Tailwind View Pattern
```php
<?= $this->extend('layout/app') ?>

<?= $this->section('css') ?>
<!-- Page-specific styles -->
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page content using Tailwind classes -->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="module">
    const csrfName = document.querySelector('meta[name="csrf-name"]').content;
    let csrfHash = document.querySelector('meta[name="csrf-token"]').content;
    const baseUrl = document.querySelector('meta[name="base-url"]').content;
    
    // Update CSRF after AJAX response
    if (data.csrf_hash) csrfHash = data.csrf_hash;
</script>
<?= $this->endSection() ?>
```

### Modal Pattern
```html
<div class="modal" id="myModal">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content max-w-md">
            <div class="modal-header">
                <h3 class="modal-title">Title</h3>
                <button type="button" class="btn-close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">Content</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
```

```javascript
// Open modal
document.getElementById('myModal').classList.add('active');
document.body.style.overflow = 'hidden';

// Close modal
document.getElementById('myModal').classList.remove('active');
document.body.style.overflow = '';
```

## Coding Conventions

### Model Pattern
- Primary keys follow `id_<table>` naming (e.g., `id_siswa`, `id_kelas`)
- Use `$allowedFields` array for mass assignment protection
- Define `$validationRules` with Indonesian messages
- Custom query methods return `array` via `->getResultArray()`

### Controller AJAX Pattern
```php
public function ajaxMethod() {
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403);
    }
    
    // Process request...
    
    return $this->response->setJSON([
        'status' => 'success',
        'data' => $result,
        'csrf_hash' => csrf_hash()  // Always include for token regeneration
    ]);
}
```

### Route Pattern
Routes defined in `app/Config/Routes.php`:
- Auth routes: `/login`, `/logout`
- Admin group with `['filter' => 'auth']`: `/admin/*`
- AJAX endpoints use `POST`

## Important Patterns

### Getting Active Academic Year
```php
$ta_aktif = $this->taModel->getTahunAjaranAktif();
if (!$ta_aktif) {
    session()->setFlashdata('error', 'Tidak ada Tahun Ajaran yang aktif.');
    return redirect()->to('admin/tahun-ajaran');
}
```

### MapelKelas Pivot Usage
```php
// CORRECT - use pivot model
$mapel = $this->mapelKelasModel->getMapelByKelas($id_kelas);

// WRONG - no direct FK relationship
$mapel = $this->mapelModel->where('id_kelas', $id_kelas)->findAll();
```

## Migration Progress (Tailwind CSS)

### ✅ Completed Pages
| Page | View File | Controller |
|------|-----------|------------|
| Layout | `layout/app.php` | - |
| Login | `auth/login_new.php` | AuthController |
| Dashboard | `admin/dashboard_new.php` | Dashboard |
| Tahun Ajaran | `admin/tahun_ajaran_new.php` | TahunAjaran |
| Kelas | `admin/kelas_new.php` | Kelas |
| Siswa | `admin/siswa_new.php` | Siswa |
| Mata Pelajaran | `admin/mata_pelajaran_new.php` | MataPelajaran |
| Penilaian List | `admin/penilaian_list_new.php` | Penilaian |
| Penilaian Create | `admin/penilaian_create_new.php` | Penilaian |
| Penilaian Input | `admin/penilaian_form_input_new.php` | Penilaian |
| Absensi Input | `admin/absensi_input_new.php` | AbsensiController |
| Rekap Absensi | `admin/rekap_absensi_new.php` | Report |
| Report Nilai | `admin/report_new.php` | Report |
| Settings | `admin/settings_page_new.php` | Settings |
| Users | `admin/users_new.php` | Users |

### ✅ Completed Partials
| Partial | File |
|---------|------|
| Siswa Rows | `partials/_siswa_rows_new.php` |
| Absensi Table | `partials/_absensi_tabel_new.php` |
| Rekap Absensi Table | `partials/_rekap_absensi_tabel_new.php` |
| Report Nilai Table | `partials/report_table_new.php` |
| Pagination | `pagers/tailwind_full.php` |

### 🎉 Migration Complete!
All admin pages have been migrated from Bootstrap 4 (SB Admin 2) to Tailwind CSS v4.

**Next Phase:** Cleanup - Remove old Bootstrap files and optimize assets.

## Authentication
- Session-based auth via `AuthFilter`
- Session keys: `isLoggedIn`, `user_id`, `username`, `nama_lengkap`
- Login throttle: 5 failed attempts = 15 min lockout
- Default credentials: `admin` / `Sayang@123`

## Environment
Configure in `.env`:
```
CI_ENVIRONMENT = development
app.baseURL = http://localhost:8080/
database.default.hostname = 127.0.0.1
database.default.database = nilai_siswa
database.default.username = root
database.default.password = root
```
