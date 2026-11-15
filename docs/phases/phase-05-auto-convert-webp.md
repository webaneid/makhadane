# Fase 05 — Auto-Convert Upload Gambar ke WebP & Auto-Crop Sub-Sizes

- **Status:** `Done`
- **Terkait:** `GAP-18`, [ADR-0004](../decisions/adr-0004-auto-convert-webp-on-upload.md)

## Tujuan
Mengonversi secara otomatis setiap gambar baru (`image/jpeg`, `image/jpg`, `image/png`) yang diunggah pelanggan menjadi format `.webp` (kualitas default `78`), menghapus file `.jpg`/`.png` asli setelah `.webp` terverifikasi valid, serta memastikan seluruh potongan gambar (`thumbnail`, `medium`, `large`, `kotak`) otomatis dibuat dalam format `.webp`.

## Langkah
- [x] Buat `inc/webp-converter.php` dengan konstanta `MAKHADANE_WEBP_QUALITY` (`78`) dan fungsi berprefix `makhadane_`:
  - `makhadane_get_webp_quality()` (filter `makhadane_webp_quality`).
  - `makhadane_set_webp_editor_quality()` pada filter `wp_editor_set_quality`.
  - `makhadane_convert_palette_png_to_webp()` (fallback GD `imagepalettetotruecolor` untuk PNG 8-bit indexed palette dengan alpha).
  - `makhadane_convert_upload_to_webp()` pada filter `wp_handle_upload` dan `wp_handle_sideload`.
- [x] Pastikan `inc/webp-converter.php` otomatis di-load oleh `functions.php` (`glob( get_template_directory() . '/inc/*.php' )`).
- [x] Jalankan `composer lint:changed`, `composer analyse`, `composer security:scan`, dan `composer dist:check`.

## Kriteria Selesai
- `inc/webp-converter.php` lolos PHPCS (`0 error, 0 warning`) dan PHPStan (`0 error`).
- `GAP-18` pada `docs/RETROFIT-GAPS.md` berstatus `Fixed`.

## Catatan Penyelesaian
- `inc/webp-converter.php` berhasil dibuat sesuai standar ADR-0004 dengan fallback aman jika server tidak mendukung `image/webp`.
