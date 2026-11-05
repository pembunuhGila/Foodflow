# Panduan Lengkap Setup Autentikasi 4 Role & Lupa Password (Laravel Breeze)

Dokumen ini menjelaskan alur teknis dan langkah-langkah implementasi sistem autentikasi multi-role dan fitur lupa password pada aplikasi **FoodFlow** menggunakan **Laravel Breeze (Blade + Tailwind)**.

---

## 1. Ringkasan Arsitektur

Sesuai dokumen [PRD.md](PRD.md), sistem FoodFlow memiliki 4 peran (role) pengguna:
1. **Admin / Staff Operasional (`admin`)** $\rightarrow$ Dashboard: `/admin/dashboard`
2. **Staff Gudang / Inventory (`gudang`)** $\rightarrow$ Dashboard: `/gudang/dashboard`
3. **Staff Produksi / Ahli Gizi (`produksi`)** $\rightarrow$ Dashboard: `/produksi/dashboard`
4. **Owner / Manager (`owner`)** $\rightarrow$ Dashboard: `/owner/dashboard`

Setiap akun memiliki status (`aktif` atau `tidak_aktif`). Jika akun `tidak_aktif`, login akan otomatis ditolak.

---

## 2. Instalasi Laravel Breeze

Laravel Breeze menyediakan scaffolding auth standar (Login, Register, Forgot Password, Reset Password, Verify Email, Update Profile).

Perintah instalasi yang dijalankan:
```bash
# 1. Unduh package Breeze
composer require laravel/breeze --dev

# 2. Pasang scaffolding Breeze dengan stack Blade (tanpa pest, pakai PHPUnit standar)
php artisan breeze:install blade --no-interaction

# 3. Build aset frontend Tailwind & Vite
npm run build
```

---

## 3. Skema Database & Migrasi

### A. Tabel Default Laravel
Saat Laravel diinisiasi, tabel bawaan mencakup:
- `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, timestamps)
- `password_reset_tokens` (`email`, `token`, `created_at`)
- `sessions`, `cache`, `jobs`

### B. Menambahkan Kolom `role` dan `status`
Dibuatkan file migrasi baru:
```bash
php artisan make:migration add_role_and_status_to_users_table --table=users
```

Isi migrasi di `database/migrations/2026_10_13_002312_add_role_and_status_to_users_table.php`:
```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('admin')->after('password');
        $table->string('status')->default('aktif')->after('role');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['role', 'status']);
    });
}
```

Jalankan migrasi ke database:
```bash
php artisan migrate
```

---

## 4. Pembuatan Enum & Model `User`

Untuk menjaga konsistensi nilai role dan status, dibuat PHP Enum bertipe kuat (*backed enum*):

### A. `app/Enums/UserRole.php`
```php
namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Gudang = 'gudang';
    case Produksi = 'produksi';
    case Owner = 'owner';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin / Staff Operasional',
            self::Gudang => 'Staff Gudang / Inventory',
            self::Produksi => 'Staff Produksi / Ahli Gizi',
            self::Owner => 'Owner / Manager',
        };
    }
}
```

### B. `app/Enums/UserStatus.php`
```php
namespace App\Enums;

enum UserStatus: string
{
    case Aktif = 'aktif';
    case TidakAktif = 'tidak_aktif';
}
```

### C. `app/Models/User.php`
Model `User` dikonfigurasi dengan:
- **`casts`**: Mengonversi `role` dan `status` ke Enum terkait.
- **`$attributes`**: Nilai default saat instance baru dibuat di memori (`role => UserRole::Admin`, `status => UserStatus::Aktif`).
- **Helper methods**:
  - `isAdmin(): bool`
  - `isGudang(): bool`
  - `isProduksi(): bool`
  - `isOwner(): bool`
  - `isActive(): bool`
  - `getDashboardRoute(): string` (mengembalikan nama route dashboard yang sesuai dengan role).

---

## 5. Kustomisasi Logika Autentikasi

### A. Validasi Status Akun Aktif (`app/Http/Requests/Auth/LoginRequest.php`)
Pada metode `authenticate()`, setelah kredensial email & password valid, sistem memeriksa apakah akun aktif:
```php
/** @var \App\Models\User $user */
$user = Auth::user();

if (! $user->isActive()) {
    Auth::logout();
    RateLimiter::hit($this->throttleKey());

    throw ValidationException::withMessages([
        'email' => 'Akun Anda berstatus tidak aktif. Silakan hubungi administrator.',
    ]);
}
```

### B. Redirect Setelah Login (`AuthenticatedSessionController.php`)
User diarahkan ke dashboard perannya masing-masing:
```php
public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    /** @var \App\Models\User $user */
    $user = $request->user();

    return redirect()->intended(route($user->getDashboardRoute(), absolute: false));
}
```

### C. Redirect Saat Logout (`AuthenticatedSessionController.php`)
Setelah logout berhasil, user langsung dialihkan kembali ke halaman login:
```php
public function destroy(Request $request): RedirectResponse
{
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}
```

---

## 6. Proteksi Server-Side dengan Middleware Role

Untuk memenuhi aturan keamanan bahwa pengguna tidak boleh mengakses menu/halaman role lain (KNF-01 & BR-11):

### A. Buat Middleware
```bash
php artisan make:middleware EnsureUserHasRole
```
File: `app/Http/Middleware/EnsureUserHasRole.php`:
```php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    $user = $request->user();

    if (! $user) {
        return redirect()->route('login');
    }

    if (! $user->isActive()) {
        auth()->logout();
        return redirect()->route('login')->withErrors([
            'email' => 'Akun Anda tidak aktif.',
        ]);
    }

    $userRoleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

    if (! in_array($userRoleValue, $roles, true)) {
        abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses halaman ini.');
    }

    return $next($request);
}
```

### B. Daftarkan Alias di `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
    ]);
})
```

---

## 7. Rute & Dashboard 4 Role (`routes/web.php`)

```php
// Rute Root: Langsung redirect ke dashboard (jika auth) atau login (jika guest)
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Redirect generic /dashboard
Route::get('/dashboard', function (Request $request) {
    /** @var User $user */
    $user = $request->user();
    return redirect()->route($user->getDashboardRoute());
})->middleware(['auth'])->name('dashboard');

// 1. Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.admin'))->name('dashboard');
});

// 2. Gudang
Route::middleware(['auth', 'role:gudang'])->prefix('gudang')->name('gudang.')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.gudang'))->name('dashboard');
});

// 3. Produksi
Route::middleware(['auth', 'role:produksi'])->prefix('produksi')->name('produksi.')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.produksi'))->name('dashboard');
});

// 4. Owner
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard.owner'))->name('dashboard');
});
```

Halaman Blade masing-masing berada di:
- `resources/views/dashboard/admin.blade.php`
- `resources/views/dashboard/gudang.blade.php`
- `resources/views/dashboard/produksi.blade.php`
- `resources/views/dashboard/owner.blade.php`

---

## 8. Seeder Akun Uji Coba

Di `database/seeders/DatabaseSeeder.php` disiapkan 4 akun default:

| Role | Email | Password | Dashboard |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@foodflow.test` | `password` | `/admin/dashboard` |
| **Gudang** | `gudang@foodflow.test` | `password` | `/gudang/dashboard` |
| **Produksi** | `produksi@foodflow.test` | `password` | `/produksi/dashboard` |
| **Owner** | `owner@foodflow.test` | `password` | `/owner/dashboard` |

Perintah menjalankan seeder:
```bash
php artisan db:seed
```

---

## 9. Fitur Lupa Password

Fitur lupa password bawaan Laravel Breeze sudah aktif:
- Halaman minta link reset: `GET /forgot-password`
- Submit permintaan reset: `POST /forgot-password` (mengirimkan notifikasi token ke email user)
- Halaman form input password baru: `GET /reset-password/{token}`
- Simpan password baru: `POST /reset-password`

> **Catatan Konfigurasi Mail**:
> Pastikan pengaturan `MAIL_*` pada file `.env` sudah diarahkan ke layanan email yang valid (misal: Mailpit di Laragon, Mailtrap, atau SMTP) untuk menerima email reset password saat pengujian lokal.

---

## 10. Cheat Sheet Perintah Berguna

```bash
# Menjalankan migrasi baru
php artisan migrate

# Me-reset database dari nol dan menjalankan seeder
php artisan migrate:fresh --seed

# Menjalankan seeder saja
php artisan db:seed

# Menjalankan seluruh pengujian otomatis
php artisan test

# Memformat kode sesuai standar PSR / Laravel Pint
vendor/bin/pint --format agent

# Menjalankan server lokal
php artisan serve
```

---

## 11. Implementasi Branch `feat/user-management` (UC-21 & UC-22)

Branch ini mengimplementasikan pengelolaan akun pengguna dan hak akses di panel Admin sesuai dokumen [UseCaseSPEC.md](UseCaseSPEC.md):

### A. Fitur yang Dikerjakan
1. **UC-21: Register Akun (Oleh Admin)**
   - Rute: `GET /admin/users/create` dan `POST /admin/users`
   - Form pembuatan akun bagi pengguna baru dengan memilih salah satu dari 4 peran (`admin`, `gudang`, `produksi`, `owner`).
   - Validasi ketat format nama, email unik (tidak boleh duplikat), kata sandi minimal 8 karakter dengan konfirmasi, dan validasi enum peran.

2. **UC-22: Mengelola Data Pengguna & Hak Akses**
   - Rute: `GET /admin/users`, `GET /admin/users/{user}/edit`, `PUT /admin/users/{user}`
   - Tabel ringkasan pengguna dilengkapi pencarian (nama/email), filter berdasarkan role, dan filter status akun.
   - Paginasi data 10 pengguna per halaman.
   - Admin dapat memperbarui nama, email, dan mengubah peran pengguna.

3. **Fitur Toggle Status Akun (Aktif $\leftrightarrow$ Tidak Aktif)**
   - Rute: `PATCH /admin/users/{user}/toggle-status`
   - Sesuai aturan bisnis PRD (BR-02 & UC-22 Alur Alternatif 7a), akun yang dinonaktifkan tidak dihapus permanen dari basis data, melainkan diubah statusnya menjadi `tidak_aktif`.
   - Pengguna dengan status `tidak_aktif` otomatis ditolak saat mencoba login di sistem.
   - **Keamanan**: Dilengkapi proteksi agar Admin tidak dapat menonaktifkan akunnya sendiri (mencegah *lockout*).

4. **Fitur Reset Password Langsung oleh Admin**
   - Rute: `PUT /admin/users/{user}/reset-password`
   - Sesuai UC-22 Alur Alternatif 5a & UC-02, Admin dapat menentukan dan menyimpan kata sandi baru untuk pengguna melalui modal interaktif (**Alpine.js**) tanpa perlu mengakses email pengguna.

### B. Daftar Berkas yang Dibuat & Diperbarui
- **Controller**: `app/Http/Controllers/Admin/UserController.php`
- **Form Requests**:
  - `app/Http/Requests/Admin/StoreUserRequest.php` (Validasi pembuatan akun)
  - `app/Http/Requests/Admin/UpdateUserRequest.php` (Validasi pengubahan akun & email unik terabaikan pada user bersangkutan)
  - `app/Http/Requests/Admin/ResetUserPasswordRequest.php` (Validasi konfirmasi password baru)
- **Tampilan Blade**:
  - `resources/views/admin/users/index.blade.php` (Tabel daftar pengguna, search/filter, dan modal reset password Alpine.js)
  - `resources/views/admin/users/create.blade.php` (Form pendaftaran akun baru)
  - `resources/views/admin/users/edit.blade.php` (Form edit pengguna dan peran)
- **Rute**: `routes/web.php` (Grup middleware `['auth', 'role:admin']`)
- **Navigasi**: `resources/views/layouts/navigation.blade.php` & `resources/views/dashboard/admin.blade.php`
- **Pengujian**: `tests/Feature/Admin/UserManagementTest.php` (12 skenario pengujian lolos 100%)

---

## 12. Implementasi Branch `feat/password-reset-admin-approval` (UC-02)

Branch ini mengimplementasikan alur pemulihan kata sandi (Lupa Password) sesuai use case **UC-02** dan dokumen [PRD.md](PRD.md).

### A. Perubahan Alur dari Breeze Standar
- **Breeze Standar**: Mengirimkan tautan reset password via email otomatis (SMTP) yang memungkinkan pengguna mengganti password sendiri.
- **Alur FoodFlow Sesuai UC-02 & PRD**:
  1. Pengguna membuka `/forgot-password` dan memasukkan alamat email terdaftar.
  2. Sistem memverifikasi bahwa email terdaftar di basis data. Jika tidak ditemukan, sistem memberikan pesan kesalahan (Alur Alternatif 4a).
  3. Sistem mencatat permohonan ke dalam tabel `password_reset_requests` dengan status **`diajukan`** dan menampilkan notifikasi bahwa permohonan diteruskan ke Admin (Langkah 5).
  4. Admin melihat daftar antrean permohonan pemulihan kata sandi pada menu **Permintaan Reset PW** (`/admin/password-resets`).
  5. Admin memproses permohonan dengan membuatkan kata sandi baru (minimal 8 karakter) dan catatan (Langkah 7 & 8).
  6. Sistem memperbarui kata sandi pengguna di tabel `users` (dihash) dan mengubah status permohonan menjadi **`selesai`**.
  7. Admin juga dapat menolak permohonan yang tidak valid (Alur Alternatif 7a) sehingga status berubah menjadi **`gagal`**.
  8. Pengguna kembali ke halaman `/login` dan dapat langsung login menggunakan kata sandi baru yang telah disiapkan Admin (Langkah 10 & 11).

### B. Daftar Berkas yang Dibuat & Diperbarui
- **Tabel & Migrasi**: `database/migrations/2026_10_13_183831_create_password_reset_requests_table.php`
  - Kolom: `id`, `user_id`, `email`, `status` (`diajukan`, `diproses`, `selesai`, `gagal`), `processed_by`, `admin_notes`, `processed_at`, `timestamps`.
- **Enum**: `app/Enums/PasswordResetStatus.php` (`Diajukan`, `Diproses`, `Selesai`, `Gagal`).
- **Model**: `app/Models/PasswordResetRequest.php` (Relasi ke `user` pemohon dan `processor` Admin).
- **Controller Pengguna**: `app/Http/Controllers/Auth/PasswordResetLinkController.php` (Menyimpan request pemulihan alih-alih mengirim email token otomatis).
- **Tampilan Publik**: `resources/views/auth/forgot-password.blade.php` (Formulir pengajuan pemulihan ke Admin).
- **Controller Admin**: `app/Http/Controllers/Admin/PasswordResetRequestController.php`
  - `index`: Menampilkan tabel antrean permohonan, filter status, pencarian, dan badge jumlah permohonan menunggu.
  - `approve`: Admin membuat kata sandi baru dan menyelesaikan permohonan.
  - `reject`: Admin menolak permohonan beserta alasannya.
- **Request Validasi**: `app/Http/Requests/Admin/ProcessPasswordResetRequest.php`.
- **Tampilan Admin**: `resources/views/admin/password-resets/index.blade.php` (Tabel antrean permohonan dan modal interaktif Alpine.js).
- **Rute**: `routes/web.php` (`admin.password-resets.*`).
- **Pengujian Fitur**: `tests/Feature/Auth/PasswordResetTest.php` (7 skenario pengujian UC-02 lolos 100%).

---

## 13. Finalisasi Modul 1: Autentikasi & Akun Pengguna (`feat/finalize-module-1-auth`)

Branch ini menyelesaikan seluruh detail akhir pada Modul 1 sesuai spesifikasi Use Case dan PRD:

### A. Fitur yang Diselesaikan
1. **Penonaktifan Registrasi Publik (UC-21 & PRD FR-04)**:
   - Sesuai aturan bisnis aplikasi internal catering FoodFlow, orang luar tidak diperkenankan mendaftar akun secara mandiri.
   - Akses rute `/register` secara otomatis dialihkan (*redirect*) ke `/login`.
   - Seluruh registrasi akun pengguna dikonsolidasikan melalui panel Admin (`/admin/users/create`).

2. **Konfirmasi Logout (UC-03 Langkah 3 & 4)**:
   - Saat pengguna menekan menu **Log Out** (baik di desktop navbar maupun mobile responsive menu), sistem memunculkan dialog konfirmasi: *"Apakah Anda yakin ingin keluar dari sistem?"*.
   - Jika pengguna memilih batal, proses dihentikan dan sesi tetap aktif (Alur Alternatif 3a).
   - Jika dikonfirmasi, sistem mengakhiri sesi dan mengarahkan pengguna kembali ke halaman login.

### B. Matriks Ketercapaian Use Case Modul 1
| ID Use Case | Nama Use Case | Status | Keterangan |
| :--- | :--- | :--- | :--- |
| **UC-01** | Login | ✅ **Selesai** | Validasi email/password, proteksi akun aktif/tidak aktif, redirect otomatis ke dashboard role masing-masing. |
| **UC-02** | Lupa Password | ✅ **Selesai** | Pengajuan permohonan via email $\rightarrow$ antrean permohonan di Admin $\rightarrow$ Admin buatkan password baru (status `selesai`) atau tolak (status `gagal`). |
| **UC-03** | Logout | ✅ **Selesai** | Dialog konfirmasi keluar, pembatalan logout, pengakhiran sesi aman, dan redirect ke form login. |
| **UC-21** | Register Akun | ✅ **Selesai** | Form pendaftaran akun internal oleh Admin dengan pemilihan 4 role & status awal. Registrasi publik dinonaktifkan. |
| **UC-22** | Mengelola Data Pengguna & Hak Akses | ✅ **Selesai** | Tabel pengguna, filter/pencarian, edit profil & peran, toggle status aktif/tidak aktif, reset password langsung oleh Admin, serta proteksi 403 Forbidden untuk non-admin. |



