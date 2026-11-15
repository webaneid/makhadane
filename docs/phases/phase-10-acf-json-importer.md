# Fase 10 — Bundling & Importer ACF JSON (`inc/acf-importer.php`)

- **Status:** `Done`
- **Prasyarat:** Fase 09 (`Done`)
- **Gap Terkait:** `GAP-21`

## Tujuan
Menyimpan seluruh definisi Field Group ACF theme (`16` grup) di dalam repositori (`inc/acf-json/field-groups.json`) dan menyediakan mekanisme auto-fallback, auto-sync berbasis hash MD5, serta UI sinkronisasi di halaman pengaturan theme (`inc/acf-importer.php`).

## Perubahan yang Dilakukan
1. **Bundling `inc/acf-json/field-groups.json`:**
   - Mengekspor seluruh 16 Field Group ACF database (`Customer Care`, `Detail Ustadz`, `Jadikan Berita Utama`, `Makhad Ane Setup`, `Page Linktree`, `Photo Profil`, `Script`, `Testimoni Detail`, `Warna`, `Blog Type Settings`, `Konten laman Dinamis`, `Page Profil Lembaga`, `Mobile Footer Menu`, `Mobile Profil Lembaga`, `Mobile Menu Kategori`, `Banner Iklan`) menggunakan `acf_prepare_field_group_for_export()` dan `acf_json_encode()`.
2. **Implementasi `inc/acf-importer.php`:**
   - `makhadane_acf_missing_notice()`: Menampilkan peringatan admin jika plugin ACF Pro belum aktif.
   - `makhadane_maybe_sync_acf_json_on_change()`: Membandingkan hash MD5 file JSON dengan option `makhadane_acf_json_hash` dan menyinkronkan Field Group ke database **hanya 1 kali** setiap kali file JSON berubah.
   - `makhadane_auto_import_acf_on_activation()`: Meng-import Field Group yang belum ada saat `after_switch_theme`.
   - `makhadane_register_acf_json_and_settings_tab()`: Mendaftarkan Field Group yang belum ada di DB secara lokal (`acf_add_local_field_group`) sebagai fallback instan, serta menambahkan tab **Import ACF JSON** di halaman `ane-general-setting` dengan proteksi capability `manage_options` dan nonce `makhadane_acf_import_action`.

## Verifikasi & Cek Kualitas
- `composer lint:changed` → Lolos (`php -l` & PHPCS 0 error).
- `composer analyse` → Lolos (PHPStan 0 error).
- `composer css:check` → Lolos (`0 dead CSS rules`).
- `composer security:scan` → Lolos.
- `composer dist:check` → Lolos (`inc/acf-json/field-groups.json` dan `inc/acf-importer.php` ikut terbawa di paket rilis).
