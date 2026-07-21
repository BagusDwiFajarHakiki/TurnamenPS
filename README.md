<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Infinity Boxzone — Sistem Manajemen Turnamen PlayStation

Sistem manajemen turnamen PlayStation **offline/venue-based** untuk turnamen eFootball. Mengelola siklus penuh mulai dari pendaftaran pemain, pembayaran, pembuatan bracket, penjadwalan pertandingan, input skor, hingga leaderboard.

## Fitur Utama

- **Manajemen Turnamen** — Buat dan kelola turnamen (draft, registration, ongoing, completed, cancelled)
- **Pendaftaran Pemain Online** — Pemain mendaftar sendiri via halaman publik, buat akun dengan username & password
- **Verifikasi Pembayaran** — Upload bukti transfer (QRIS/bank), admin verifikasi di panel
- **Auto Bracket Generation** — Single elimination dengan BYE otomatis untuk jumlah peserta non-pangkat-2, termasuk bracket tempat ketiga
- **Penjadwalan & Assign PS Unit** — Sistem FIFO untuk mapping pertandingan ke unit PS fisik
- **Input Skor Real-time** — Admin input hasil pertandingan, otomatis advance pemenang di bracket
- **Leaderboard & Statistik** — Top skorer, win streak, klub populer, ranking pemain
- **Dashboard Pemain** — Status pertandingan, bracket view, disput, pembelian slot
- **Sistem Disput** — Protes hasil pertandingan dengan alur koreksi berjenjang
- **Multi-Bahasa** — Dukungan Indonesia (`id`) dan Inggris (`en`)
- **Dark/Light Mode** — Toggle tema tersimpan di localStorage

## Tech Stack

| Komponen | Versi |
|---|---|
| PHP | >= 8.3 |
| Laravel | 13.x |
| Filament | 4.0 (admin panel) |
| Livewire | Full-page components (frontend publik) |
| Vite | 8.0 |
| Tailwind CSS | 4.0 |
| Database | SQLite (default) |
| spatie/laravel-activitylog | Audit trail |
| filament-shield | Role & permission admin |

---

## Tutorial Clone & Setup

### Prasyarat

- **PHP** >= 8.3 (dengan extensions: openssl, pdo, mbstring, tokenizer, xml, ctype, json, bcmath, gd, fileinfo)
- **Composer** >= 2.x
- **Node.js** >= 18.x & **npm**
- **Git**

> Jangan punya PHP/Composer/Node? Gunakan [Laragon](https://laragon.org/) (Windows) atau [Laradock](https://laradock.io/) (Linux/Mac) untuk install otomatis.

### 1. Clone Repository

```bash
git clone https://github.com/username/TurnamenPS.git
cd TurnamenPS
```

### 2. Install Dependensi & Setup Otomatis

Jalankan satu command untuk setup lengkap (install PHP deps, copy `.env`, generate key, migrate, install & build frontend):

```bash
composer setup
```

> Script ini menjalankan: `composer install` → copy `.env` → `php artisan key:generate` → `php artisan migrate` → `npm install` → `npm run build`

### 3. Seed Database (Data Default)

```bash
php artisan db:seed
```

Ini akan mengisi:
- **115+ klub sepak bola** dari 10 liga (Premier League, La Liga, Bundesliga, Serie A, Ligue 1, dll) + 15 tim nasional
- **4 unit PS** (PS-01 s/d PS-04, campuran PS3/PS4/PS5)
- **Admin default**: `admin@boxzone.com` / `password123`
- **Role & permission** Filament Shield (super_admin)

### 4. Jalankan Server Development

```bash
composer dev
```

Command ini menjalankan **secara bersamaan**:
- `php artisan serve` — Web server (http://localhost:8000)
- `php artisan queue:work` — Queue worker untuk job
- `php artisan pail` — Real-time log viewer
- `npm run dev` — Vite dev server (HMR)

### 5. Aplikasi

| Halaman | URL | Keterangan |
|---|---|---|
| Beranda | [http://localhost:8000](http://localhost:8000) | Turnamen aktif, bracket live, top pemain |
| Daftar Turnamen | `http://localhost:8000/tournament/{slug}` | Detail bracket & statistik turnamen |
| Registrasi Pemain | `http://localhost:8000/register-player` | Buat akun pemain |
| Login Pemain | `http://localhost:8000/login` | Dashboard pemain |
| **Admin Panel** | [http://localhost:8000/admin](http://localhost:8000/admin) | Panel manajemen (`admin@boxzone.com` / `password123`) |

---

## Setup Manual (Tanpa `composer setup`)

Jika ingin setup langkah demi langkah:

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Buat file SQLite (Laragon biasanya sudah otomatis)
touch database/database.sqlite

# 5. Jalankan migrasi
php artisan migrate

# 6. Seed data
php artisan db:seed

# 7. Install & build frontend
npm install
npm run build
```

## Konfigurasi Environment

File `.env` sudah dikonfigurasi default untuk SQLite:

```env
DB_CONNECTION=sqlite
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
MAIL_MAILER=log
```

Ubah sesuai kebutuhan. Untuk MySQL/MariaDB:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=turnamenps
DB_USERNAME=root
DB_PASSWORD=
```

> Setelah mengubah database config, jalankan `php artisan migrate:fresh --seed`.

## Struktur Project

```
TurnamenPS/
├── app/
│   ├── Filament/          # Admin panel resources & widgets
│   ├── Http/Livewire/     # Frontend Livewire components
│   ├── Models/            # Eloquent models
│   ├── Events/            # MatchCompleted event
│   ├── Listeners/         # UpdatePlayerAggregate listener
│   └── Services/          # TournamentService (core logic)
├── database/
│   ├── migrations/        # 18 tabel
│   └── seeders/           # ClubSeeder, PsUnitSeeder, AdminSeeder
├── resources/
│   ├── views/livewire/    # Blade views untuk frontend
│   └── css/ & js/         # Frontend assets
├── config/                # Konfigurasi Laravel & Filament
├── routes/web.php         # Routes publik & player
└── scratch/               # Development notes
```

## Perintah Berguna

```bash
composer dev          # Jalankan semua service development
composer setup        # Setup awal dari nol
composer test         # Jalankan test

php artisan migrate:fresh --seed   # Reset & seed ulang database
php artisan shield:generate        # Regenerate role & permission Filament Shield
```

## Catatan Desain

- Sistem dirancang untuk turnamen **offline/venue-based** — admin input hasil secara manual (tidak ada integrasi API game)
- **Multi-slot**: Satu pemain bisa beli beberapa slot dalam turnamen yang sama, statistik di-aggregate
- **Walkover**: 2x walkover = diskualifikasi otomatis
- **Entry expiry**: Slot yang belum dibayar otomatis expired (default 24 jam)
- **Live updates** menggunakan Livewire polling (60 detik), tanpa WebSocket/Reverb

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
