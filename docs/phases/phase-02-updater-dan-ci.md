# Fase 02 — Auto-Updater, CI, & Release Workflow

- **Status:** Done
- **Prasyarat:** Fase 01 (Retrofit Audit)
- **Branch:** `main`

## Tujuan

1. Menstandarkan `inc/updater.php` ke standar prosedural `makhadane_updater_*`:
   - Mendukung repository GitHub privat maupun publik dengan autentikasi Personal Access Token (`MAKHADANE_UPDATER_TOKEN` di `wp-config.php` atau ACF option `ane_updater_token`).
   - Menyediakan tab **Update** di ACF Options (`ane-general-setting`) lengkap dengan status versi, changelog, tombol "Cek Update Sekarang" ber-nonce, dan input token.
   - Menghapus class `Makhadane_Updater` lama serta menghapus fitur debug `$_GET['ane_debug']` / `$_GET['makhadane_debug']` yang mengekspos `print_r()` dan aksi `force_check` tanpa verifikasi capability/nonce (`GAP-05`).
   - Memastikan tombol "Cek Update Sekarang" langsung memanggil `wp_update_themes()` pada `admin_init` prioritas `1` sehingga notifikasi update tampil seketika tanpa perlu refresh kedua.
2. Membersihkan `functions.php`:
   - Mendefinisikan konstanta `MAKHADANE_VERSION` dari versi `style.css`.
   - Memindahkan 9 panggilan `update_option()` ukuran gambar dari `after_setup_theme` (setiap request) ke hook `after_switch_theme` dan upgrade satu kali via `admin_init` (`GAP-12`).
   - Memperbaiki kompatibilitas PHP 8.x pada `makhadane_remove_wp_version_strings()` (`wp_parse_url` + pengecekan `is_string` sebelum `wp_parse_str`).
   - Menghapus blok `back-compat.php` (file tidak ada) dan duplikasi `add_theme_support( 'html5' )`.
3. Menyiapkan `.releaserc.json`, `.github/workflows/ci.yml`, dan memperbarui `.github/workflows/release.yml` sesuai ADR-0002 dan ADR-0003 (`GAP-17`).

## File yang Disentuh

- `functions.php` (ubah)
- `inc/updater.php` (tulis ulang ke standar prosedural `makhadane_updater_*`)
- `.releaserc.json` (buat)
- `.github/workflows/ci.yml` (buat)
- `.github/workflows/release.yml` (ubah — tambah jalur `semantic-release` otomatis + jalur manual tag dengan `.distignore`)
- `docs/RETROFIT-GAPS.md` (perbarui status `GAP-05`, `GAP-12`, `GAP-17`)
- `docs/PROGRESS.md` (perbarui status Fase 02)

## Langkah Detail

- [x] 1. Perbarui `functions.php`: definisikan `MAKHADANE_VERSION`, pindahkan `update_option` ukuran gambar ke `after_switch_theme`, perbaiki `makhadane_remove_wp_version_strings()`, dan rapikan sesuai WPCS.
- [x] 2. Tulis ulang `inc/updater.php` dengan fungsi prosedural berprefix `makhadane_updater_*`, dukungan token privat (`MAKHADANE_UPDATER_TOKEN` / `ane_updater_token`), validasi capability + nonce pada `makhadane_force_check`, dan tab ACF Options pada `ane-general-setting`.
- [x] 3. Buat `.releaserc.json` untuk konfigurasi `semantic-release`.
- [x] 4. Buat `.github/workflows/ci.yml` dan perbarui `.github/workflows/release.yml`.
- [x] 5. Jalankan `composer lint:changed`, `composer analyse`, dan `composer dist:check`.
- [x] 6. Lakukan Security Review dan tutup Fase 02.

## Kriteria Selesai (Acceptance Criteria)

- [x] `composer lint:changed` lolos 0 error & 0 warning pada `functions.php` dan `inc/updater.php`.
- [x] `composer analyse` lolos 0 error.
- [x] `composer dist:check` lolos (ZIP bersih dari file dev/docs/AI).
- [x] `GAP-05`, `GAP-12`, dan `GAP-17` berstatus `Fixed` di `docs/RETROFIT-GAPS.md`.

## Security Review (Step 4 SOP)

- **Output Escaping:** Semua output di `inc/updater.php` (versi, changelog rilis, URL aksi update, notice admin) di-escape dengan `esc_html()`, `esc_html__()`, `esc_url()`, dan `nl2br( esc_html( ... ) )`.
- **Capability & Nonce Check:** Handler `makhadane_updater_force_check_handler()` memverifikasi `current_user_can( 'update_themes' )` dan `check_admin_referer( 'makhadane_updater_action' )`.
- **Debug & Secret Removal:** Blok debug `ane_debug` (`print_r`) dihapus. Token GitHub tidak di-hardcode di kode; diambil dari konstanta `MAKHADANE_UPDATER_TOKEN` atau ACF option `ane_updater_token` (bertipe `password`), dan hanya dikirim pada header `Authorization` ke endpoint `api.github.com/repos/webaneid/makhadane/` atau `github.com/webaneid/makhadane/`.
- **Database Write:** 9 panggilan `update_option()` di `functions.php` tidak lagi berjalan pada setiap request `after_setup_theme`, melainkan hanya saat aktivasi theme (`after_switch_theme`) atau satu kali saat versi `MAKHADANE_VERSION` berubah di `admin_init`.

## Catatan Eksekusi & Keputusan

- Nama fungsi hook internal di `functions.php` diseragamkan ke prefix `makhadane_` (`makhadane_theme_setup`, `makhadane_configure_image_sizes`, `makhadane_maybe_upgrade_image_sizes`, `makhadane_widgets`, `makhadane_remove_wp_version_strings`, `makhadane_remove_meta_version`) agar lulus `WordPress.NamingConventions.PrefixAllGlobals` dengan 0 error & 0 warning.
- Lokasi field group ACF `group_makhadane_updater` diarahkan ke `options_page == ane-general-setting` sesuai slug submenu pengaturan umum di `inc/admin.php`.
