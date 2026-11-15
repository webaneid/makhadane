# Makhad Ane — WordPress Theme

Theme WordPress untuk situs pesantren, ma'had, madrasah, dan sekolah Islam oleh
Webane Indonesia. Proprietary — hanya untuk customer Webane.

## Untuk Siapa File Ini
Onboarding singkat untuk developer manusia. Aturan kerja untuk AI agent ada di
`AGENTS.md`; proses kerja di `docs/SOP.md`.

## Kebutuhan
- WordPress 6.0+, PHP 8.0+
- Plugin **ACF Pro** (wajib)
- Dev: PHP + Composer (mis. via Laravel Herd), Git

## Quick Start (development)
```bash
composer install     # tooling dev: PHPCS, WPCS, PHPStan
composer lint        # cek sintaks + coding standard
composer analyse     # analisis statis
```
Aktifkan `WP_DEBUG` dan `WP_DEBUG_LOG` di `wp-config.php` situs lokal.

## Alur Kerja Singkat
1. Lihat fase aktif di `docs/PROGRESS.md`.
2. Branch `feat/...` atau `fix/...` dari `main`.
3. Ikuti `docs/SOP.md` (rencana → eksekusi → cek → security review → tutup).
4. Commit dengan Conventional Commits (`docs/conventions.md`).
5. Merge ke `main` → CI merilis versi baru → situs pelanggan menerima update.

## Dokumentasi
- `AGENTS.md` — aturan non-negotiable + peta dokumen
- `docs/SOP.md` — alur kerja per fase
- `docs/conventions.md` — naming, commit, branch
- `docs/architecture/architecture-security.md` — standar & checklist keamanan
- `docs/decisions/` — ADR (alasan di balik keputusan besar)
