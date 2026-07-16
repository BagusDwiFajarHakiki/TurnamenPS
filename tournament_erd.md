# ERD LENGKAP — TOURNAMENT MANAGEMENT SYSTEM (PS3/PLAYBOX)

**Versi:** 1.1 (update sesuai `tournament_system_design.md` v3.2)
**Database:** MySQL/MariaDB, Laravel 13 Eloquent

Dokumen ini merangkum SELURUH tabel yang sudah dibahas di dokumen alur sistem menjadi satu
ERD utuh & siap dipakai sebagai acuan migration.

---

## 1. DIAGRAM ERD (MERMAID)

```mermaid
erDiagram
    PLAYERS ||--o{ TOURNAMENT_ENTRIES : "punya banyak slot"
    PLAYERS ||--o{ ENTRY_BATCHES : "melakukan transaksi"
    PLAYERS ||--o{ TOURNAMENT_PLAYER_AGGREGATES : "punya akumulasi skor"
    PLAYERS ||--o{ PLAYER_CODE_RESET_REQUESTS : "minta reset kode"
    PLAYERS ||--o{ PLAYER_LOGIN_ATTEMPTS : "tercatat percobaan login"

    TOURNAMENTS ||--o{ TOURNAMENT_STAGES : "punya stage"
    TOURNAMENTS ||--o{ TOURNAMENT_ENTRIES : "punya peserta"
    TOURNAMENTS ||--o{ ENTRY_BATCHES : "punya transaksi"
    TOURNAMENTS ||--o{ TOURNAMENT_PLAYER_AGGREGATES : "punya leaderboard"

    TOURNAMENT_STAGES ||--o{ TOURNAMENT_GROUPS : "punya grup (opsional)"
    TOURNAMENT_STAGES ||--o{ MATCHES : "punya pertandingan"

    TOURNAMENT_GROUPS ||--o{ TOURNAMENT_ENTRIES : "mengelompokkan entry"
    TOURNAMENT_GROUPS ||--o{ MATCHES : "punya match grup"

    ENTRY_BATCHES ||--o{ TOURNAMENT_ENTRIES : "menghasilkan N slot"

    TOURNAMENT_ENTRIES ||--o{ MATCH_PARTICIPANTS : "tampil sebagai peserta match"
    TOURNAMENT_ENTRIES ||--o{ MATCH_DISPUTES : "mengajukan protes"
    TOURNAMENT_ENTRIES ||--o{ MATCH_GAME_PARTICIPANTS : "tampil di game ke-N"

    MATCHES ||--o{ MATCH_PARTICIPANTS : "punya 2 sisi (home/away)"
    MATCHES ||--o{ MATCH_GAMES : "punya detail per game (best_of)"
    MATCHES ||--o{ MATCH_DISPUTES : "bisa diprotes"
    MATCHES }o--|| PS_UNITS : "dimainkan di unit"
    MATCHES ||--o{ PS_UNIT_SCHEDULES : "punya jadwal unit"
    MATCHES |o--|| MATCHES : "next_match_id / loser_next_match_id"

    MATCH_GAMES ||--o{ MATCH_GAME_PARTICIPANTS : "punya 2 sisi per game"

    CLUBS ||--o{ MATCH_PARTICIPANTS : "dipakai sebagai club_used"

    PS_UNITS ||--o{ PS_UNIT_SCHEDULES : "punya histori jadwal"

    USERS ||--o{ TOURNAMENT_ENTRIES : "verifikasi pembayaran"
    USERS ||--o{ ENTRY_BATCHES : "verifikasi transaksi"
    USERS ||--o{ MATCH_DISPUTES : "review dispute"
    USERS ||--o{ PLAYER_CODE_RESET_REQUESTS : "issue kode baru"

    CLUBS ||--o{ MATCH_GAME_PARTICIPANTS : "dipakai sebagai club_used"

    PLAYERS {
        bigint id PK
        string name
        string username UK
        string email
        string login_code "hashed, 6 char alfanumerik"
        string login_code_plain_hint "nullable, tampil sekali saat daftar"
        string avatar
        boolean is_active
        datetime last_login_at
        timestamps timestamps
    }

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string avatar
        datetime created_at
        datetime updated_at
    }

    TOURNAMENTS {
        bigint id PK
        string name
        string slug UK
        string game_title
        decimal price_per_slot
        int max_slot_per_player
        int max_entries
        int entry_expiry_hours "default 24"
        text payment_info
        text rules_content
        int no_show_deadline_minutes
        datetime registration_start
        datetime registration_end
        datetime tournament_start
        datetime tournament_end
        enum status
        timestamps timestamps
    }

    TOURNAMENT_STAGES {
        bigint id PK
        bigint tournament_id FK
        string name
        int stage_order
        enum format
        enum status
        enum source_type
        json config
        timestamps timestamps
    }

    TOURNAMENT_GROUPS {
        bigint id PK
        bigint tournament_stage_id FK
        string name
        timestamps timestamps
    }

    ENTRY_BATCHES {
        bigint id PK
        bigint tournament_id FK
        bigint player_id FK
        int slot_count
        decimal total_price
        string payment_proof_path
        enum status
        text rejection_reason
        bigint verified_by FK
        datetime verified_at
        timestamps timestamps
    }

    TOURNAMENT_ENTRIES {
        bigint id PK
        bigint tournament_id FK
        bigint player_id FK
        bigint entry_batch_id FK
        bigint group_id FK
        string entry_label
        int entry_number
        int seed
        enum status
        string payment_proof_path
        datetime payment_verified_at
        bigint payment_verified_by FK
        datetime expires_at
        int walkover_count "default 0"
        datetime rules_accepted_at
        datetime registered_at
        timestamps timestamps
    }

    TOURNAMENT_PLAYER_AGGREGATES {
        bigint id PK
        bigint tournament_id FK
        bigint player_id FK
        int total_entries
        int total_matches_played
        int total_goals_scored
        int total_goals_conceded
        int total_wins
        int total_losses
        int total_draws
        int current_win_streak
        int best_win_streak
        int active_entries_count
        int rank_position
        datetime updated_at
    }

    MATCHES {
        bigint id PK
        bigint tournament_stage_id FK
        bigint group_id FK "nullable"
        int round_number
        int match_order
        string bracket_position
        bigint next_match_id FK "nullable"
        bigint loser_next_match_id FK "nullable"
        boolean is_bye
        enum status
        bigint ps_unit_id FK "nullable"
        datetime scheduled_at
        datetime started_at
        datetime finished_at
        int best_of
        boolean decided_by_penalty
        int penalty_score_home "nullable"
        int penalty_score_away "nullable"
        string result_proof_path "nullable"
        bigint no_show_entry_id FK "nullable"
        string walkover_reason "nullable"
        timestamps timestamps
    }

    MATCH_PARTICIPANTS {
        bigint id PK
        bigint match_id FK
        bigint tournament_entry_id FK
        enum side "home/away"
        bigint club_id FK
        int goals_scored
        boolean is_winner "nullable"
        timestamps timestamps
    }

    MATCH_GAMES {
        bigint id PK
        bigint match_id FK
        int game_number
        timestamps timestamps
    }

    MATCH_GAME_PARTICIPANTS {
        bigint id PK
        bigint match_game_id FK
        bigint tournament_entry_id FK
        bigint club_id FK
        int goals_scored
        boolean is_winner
    }

    MATCH_DISPUTES {
        bigint id PK
        bigint match_id FK
        bigint raised_by_entry_id FK
        text reason
        enum status
        bigint reviewed_by FK
        text resolution_note
        datetime created_at
        datetime resolved_at
    }

    CLUBS {
        bigint id PK
        string name
        string logo
        string league
        timestamps timestamps
    }

    PS_UNITS {
        bigint id PK
        string code UK
        string name
        string location
        enum console_type
        int controller_count
        enum status
        text notes
        timestamps timestamps
    }

    PS_UNIT_SCHEDULES {
        bigint id PK
        bigint ps_unit_id FK
        bigint match_id FK
        datetime booked_from
        datetime booked_until
        enum status
        timestamps timestamps
    }

    PLAYER_LOGIN_ATTEMPTS {
        bigint id PK
        bigint player_id FK "nullable"
        string username_attempted
        string ip_address
        boolean success
        datetime created_at
    }

    PLAYER_CODE_RESET_REQUESTS {
        bigint id PK
        bigint player_id FK
        string username_submitted
        enum status
        bigint new_code_issued_by FK
        boolean issued_same_code
        datetime created_at
        datetime resolved_at
    }
```

---

## 2. PENJELASAN RELASI KUNCI

| Relasi | Kardinalitas | Catatan |
|---|---|---|
| `players` → `tournament_entries` | 1—N | Satu player bisa punya banyak entry/slot **dalam turnamen yang sama maupun berbeda** — inilah basis fitur multi-slot |
| `players` → `entry_batches` | 1—N | Riwayat transaksi pembelian slot, bisa lebih dari 1 batch per turnamen (daftar awal + tambah slot belakangan) |
| `entry_batches` → `tournament_entries` | 1—N | Satu transaksi bisa menghasilkan banyak entry sekaligus (sesuai jumlah slot yang dibeli) |
| `tournaments` → `tournament_stages` | 1—N | Default 1 stage (dibuat otomatis), bisa ditambah manual |
| `tournament_stages` → `tournament_groups` | 1—N | Hanya dipakai untuk format `round_robin`/`group_stage` |
| `tournament_stages` → `matches` | 1—N | Semua match berada di bawah satu stage |
| `matches` → `match_participants` | 1—2 | Selalu tepat 2 baris (home & away) per match, menggantikan kolom `entry_1/entry_2` agar fleksibel |
| `match_participants` → `clubs` | N—1 | Klub yang dipakai tiap sisi di match tsb, dasar query ranking klub terpopuler |
| `matches` → `matches` (self-relation) | N—1 | `next_match_id` (pemenang lanjut) & `loser_next_match_id` (untuk double elimination) |
| `tournament_entries` → `tournament_player_aggregates` | N—1 (lewat player_id+tournament_id) | Banyak entry milik 1 player diakumulasikan jadi 1 baris aggregate per turnamen |
| `matches` → `match_disputes` | 1—N | Satu match bisa diprotes lebih dari sekali (misal dari sisi berbeda) |
| `users` → `tournament_entries` | 1—N | `payment_verified_by` — admin verifikasi pembayaran entry |
| `users` → `entry_batches` | 1—N | `verified_by` — admin verifikasi transaksi |
| `users` → `match_disputes` | 1—N | `reviewed_by` — admin review & putuskan dispute |
| `users` → `player_code_reset_requests` | 1—N | `new_code_issued_by` — admin terbitkan kode baru |
| `clubs` → `match_game_participants` | 1—N | Klub dipakai di game individual (best_of) |
| `ps_units` → `matches` | 1—N | Satu unit dipakai bergantian oleh banyak match dari waktu ke waktu |

---

## 3. CATATAN IMPLEMENTASI MIGRATION

1. **Urutan migration disarankan** (mengikuti dependency FK): `users` (bawaan Laravel) → `players` →
   `clubs` → `ps_units` → `tournaments` → `tournament_stages` → `tournament_groups` →
   `entry_batches` → `tournament_entries` → `matches` (tanpa FK self dulu, lalu `alter table`
   tambah `next_match_id`/`loser_next_match_id` belakangan karena self-referencing) →
   `match_participants` → `match_games` → `match_game_participants` → `match_disputes` →
   `tournament_player_aggregates` → `ps_unit_schedules` → `player_login_attempts` →
   `player_code_reset_requests`.
2. **Model `GameMatch`**, bukan `Match` — `match` adalah reserved keyword di PHP 8.
3. Kolom `club_used` versi sebelumnya (free text) sudah dinormalisasi jadi `club_id` (FK ke
   `clubs`) di ERD final ini, supaya ranking klub akurat (tidak ada duplikasi penulisan
   "Real Madrid" vs "real madrid" vs "RealMadrid").
4. Tabel `activity_log` (dari package `spatie/laravel-activitylog`) tidak digambar di ERD
   karena dibuat otomatis oleh package tsb, bukan tabel custom — cukup tambahkan trait
   `LogsActivity` di model `TournamentEntry`, `GameMatch`, `MatchParticipant`, `EntryBatch`.
5. Semua tabel berstatus enum disarankan pakai native MySQL `ENUM` atau Laravel cast `enum`
   (PHP 8.1+ backed enum) supaya validasi status konsisten di level database & aplikasi.
6. Kolom `player_id` di `player_login_attempts` bersifat **nullable** karena percobaan login
   bisa dilakukan dengan username yang tidak dikenal (belum terdaftar) — lihat 2.1 desain sistem.
7. Tabel `users` (default Laravel) ditambahkan ke ERD karena direferensikan sebagai FK di
   beberapa tabel: `tournament_entries.payment_verified_by`, `entry_batches.verified_by`,
   `match_disputes.reviewed_by`, dan `player_code_reset_requests.new_code_issued_by`.
8. Field `login_code_plain_hint` di `players` bersifat sementara (nullable) — hanya diisi saat
   pertama kali generate kode untuk ditampilkan ke user, TIDAK disimpan permanen (segera di-null
   setelah ditampilkan). Ini sesuai catatan keamanan di desain sistem 3.3.
9. Field `entry_expiry_hours` di `tournaments` (default 24) mengatur batas waktu pembayaran
   entry `pending_payment` — lihat 3.2.2 desain sistem.
10. Field `payment_proof_path`, `payment_verified_at`, `payment_verified_by`, `expires_at`,
    dan `walkover_count` ditambahkan ke `tournament_entries` sesuai spesifikasi 2.2 desain sistem.
11. Relasi `clubs` → `match_game_participants` ditambahkan karena `match_game_participants`
    memiliki FK `club_id` (sama seperti `match_participants`).
