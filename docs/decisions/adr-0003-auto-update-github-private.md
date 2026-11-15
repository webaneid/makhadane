# ADR-0003: Auto-Update dari GitHub Release (Repo Privat/Publik)

**Status:** Accepted
**Tanggal:** 2026-11-15

## Context
Theme dipasang di banyak situs pelanggan; update manual satu per satu tidak
skala. Standar Webane (`sekolahane`, `newsane`, `jalawarta`) memakai modul
`inc/updater.php` yang mengecek `releases/latest` di GitHub API, mendukung
token opsional (`MAKHADANE_UPDATER_TOKEN` atau ACF option `ane_updater_token`),
dan menyediakan tombol **Cek Update Sekarang** dengan proteksi nonce +
`current_user_can( 'update_themes' )`.

## Decision
1. Gunakan pola updater standar Webane di `inc/updater.php` dengan prefix
   `makhadane_updater_*` dan konstanta `MAKHADANE_UPDATER_REPO` (`webaneid/makhadane`).
2. Header `Authorization` hanya dikirim ke host `api.github.com` /
   `github.com` untuk URL yang cocok dengan repo `webaneid/makhadane`.
3. Saat tombol **Cek Update Sekarang** diklik (atau `?force-check=1` di
   `update-core.php` / `themes.php`), transient `makhadane_github_release` dan
   `update_themes` langsung dibersihkan dan di-refresh.

## Alternatif yang Dipertimbangkan
- Library `plugin-update-checker` — ditolak: menambah dependensi besar untuk
  fungsi yang sudah terbukti jalan dengan ~300 baris.

## Konsekuensi
- Kualitas setiap rilis langsung terasa di semua situs → SOP Langkah 3–6
  tidak boleh dilewati.
