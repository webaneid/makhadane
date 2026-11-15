# AGENTS.md — Makhad Ane (WordPress Theme)

> Sumber aturan TUNGGAL untuk semua AI agent (Antigravity, Claude Code, Codex,
> dst). `CLAUDE.md` hanya mengimpor file ini — JANGAN tulis aturan di dua tempat.
> Standar ini mengikuti template `master-wordpress` Webane (Sekolahane, Newsane,
> Elemenane, Jalawarta). Bagian bertanda **[theme]** spesifik makhadane; sisanya
> standar umum.

## Ringkasan Project [theme]
Theme WordPress klasik (non-block) untuk situs pesantren, ma'had, madrasah, dan
sekolah Islam milik Webane Indonesia — **proprietary, hanya untuk customer Webane**.
Dependensi wajib: **ACF Pro**. Sudah terpasang di banyak situs pelanggan,
jadi **setiap rilis berdampak ke semua situs itu** (lihat Auto-Update).

## Sebelum Mengerjakan Apa Pun
1. Cek `docs/PROGRESS.md` — fase mana yang aktif.
2. Ikuti alur `docs/SOP.md` (rencana → eksekusi → cek → security review → tutup).
3. Baca HANYA dokumen di Peta Dokumen yang relevan dengan task.

## Rules Non-Negotiable
- **Minimal & sesuai scope.** Jangan tambah abstraksi/opsi/fallback "buat
  jaga-jaga". Temuan di luar scope → catat di `docs/RETROFIT-GAPS.md` atau
  fase baru, jangan diperbaiki diam-diam.
- **Kompatibilitas mundur.** Theme ini hidup di situs pelanggan. JANGAN ubah
  nama/slug yang tersimpan di database tanpa migrasi: post type (`ustadz`,
  `ekstrakurikuler`, `testimoni`, `faq`), taxonomy (`pelajaran`, `Kelas`),
  nama field ACF, option name, menu location (`menuutama`, `menufooter`),
  sidebar ID (`default-sidebar`, `blog-sidebar`), theme_mod, image size.
- **Prefix.** Fungsi/hook/option/transient baru WAJIB berprefix `makhadane_`
  (konstanta: `MAKHADANE_`). Prefix lama (`ane_`, `musi_`, `fungsi_`, dst)
  boleh tetap ada, tapi jangan menambah yang baru.
- **Text domain** hanya `makhadane`.
- **Aset** wajib lewat `wp_enqueue_style/script` dengan versi = versi theme
  (`MAKHADANE_VERSION`) atau `filemtime()`. Tidak ada `<script>`/`<link>` mentah
  di template.
- **Tidak ada secret di kode** (API key, token, password). Ambil dari ACF
  option atau konstanta `wp-config.php`.
- **Tidak ada debug tertinggal**: `var_dump`, `print_r`, `error_log` sementara,
  `die()` untuk debug.
- **Tidak ada tulis ke DB di setiap request** (mis. `update_option()` di
  `init`/`after_setup_theme`). Hanya saat aktivasi theme / upgrade versi.
- **File PHP di `inc/`** diawali `defined( 'ABSPATH' ) || exit;`.
- Keputusan besar → ADR di `docs/decisions/`. Bug penting yang sudah di-fix →
  `docs/lessons-learned.md`.
- Commit WAJIB Conventional Commits (`docs/conventions.md`) — menentukan
  versi rilis otomatis.

## Security — Non-Negotiable (detail: `docs/architecture/architecture-security.md`)
- **Escape saat output** (late escaping): `esc_html`, `esc_attr`, `esc_url`,
  `wp_kses_post`. Termasuk data dari ACF, option, dan post meta — data DB
  TIDAK dianggap aman.
- **Sanitize saat input**: `wp_unslash()` + `sanitize_*()`/`absint()` untuk
  setiap `$_GET/$_POST/$_REQUEST/$_COOKIE/$_SERVER`.
- **Aksi yang mengubah data**: nonce (`check_admin_referer`/`check_ajax_referer`)
  + `current_user_can()`.
- **SQL**: utamakan `WP_Query`/API WP; `$wpdb` WAJIB `$wpdb->prepare()` kalau
  ada variabel.
- Selesai mengubah kode → jalankan checklist Security Review.

## Cek Wajib Sebelum Menyatakan Selesai
```bash
composer lint        # php -l + PHPCS (WordPress Coding Standards)
composer analyse     # PHPStan
composer css:check   # Compile css/main.css -> css/main.min.css + audit 0 dead CSS
```
- Tidak ada test suite otomatis; verifikasi = tiga perintah di atas + cek manual.
- Tambahan: `composer lint:changed` (PHPCS hanya file yang berubah vs `main`),
  `composer lint:fix` (phpcbf), `composer security:scan`, `composer dist:check`
  (ZIP rilis bersih dari .md/docs/tooling dev).
- Target runtime **PHP 8.0+** (`composer.json` platform 8.0.30). Fitur 8.1+
  (enum, readonly, `never`, first-class callable) jangan dipakai.
- File yang DIUBAH/DIBUAT di task ini wajib lolos ketiganya.
- Setiap perubahan tampilan/redesign: **utamakan pakai class grid/utility yang sudah ada**, ganti langsung pada blok bagian `[01]`–`[12]` di `css/main.css` (jangan menumpuk override di bawah), dan **wajib hapus CSS class lama yang tidak lagi dipakai** (`composer css:check` wajib `0 dead CSS rules`).
- Kode lama yang belum disentuh boleh masih melanggar (dicatat di baseline),
  tapi jangan menambah pelanggaran baru.
- Perubahan tampilan → cek manual di situs lokal (desktop + mobile).

## Struktur Theme [theme]
```
functions.php         → setup theme, require file inc/
inc/                  → logika PHP (enqueue, helper, CPT, widget, updater, webp)
tp/                   → template part (get_template_part('tp/...'))
  section/ pages/ news/ mobile/
*.php (root)          → template hierarchy WordPress
css/                  → css/main.css (source dev) & css/main.min.css (1 file rilis)
js/                   → js/makhadane.js (1 file JS tunggal)
img/                  → gambar statis theme
docs/                 → dokumentasi (tidak ikut ZIP rilis)
```

## Auto-Update & Rilis (detail: ADR-0002, ADR-0003)
- Merge ke `main` dengan commit `feat:`/`fix:` = **rilis ke SEMUA situs
  pelanggan**. Perlakukan `main` sebagai production.
- Versi di `style.css` dinaikkan otomatis oleh CI — JANGAN edit manual.
- File yang tidak ikut ZIP rilis didaftar di `.distignore`.

## Peta Dokumen
| Kalau task menyangkut...                    | Baca                                              |
|---------------------------------------------|---------------------------------------------------|
| Alur kerja per fase                         | `docs/SOP.md`                                     |
| Fase aktif & statusnya                      | `docs/PROGRESS.md`                                |
| Naming, struktur file, commit, branch       | `docs/conventions.md`                             |
| Struktur 12 bagian CSS & SOP Redesign       | `docs/architecture/architecture-css.md`           |
| Escape/sanitize/nonce, checklist review     | `docs/architecture/architecture-security.md`      |
| CI, versioning, auto-update GitHub release  | `docs/architecture/architecture-deployment.md`    |
| Gambaran theme & kontrak DB                 | `docs/architecture/architecture-overview.md`      |
| Gap kode vs standar                         | `docs/RETROFIT-GAPS.md`                           |
| Kenapa suatu keputusan diambil              | `docs/decisions/` (ADR)                           |
| Bug yang pernah terjadi & pencegahannya     | `docs/lessons-learned.md`                         |
| Istilah domain                              | `docs/glossary.md`                                |
