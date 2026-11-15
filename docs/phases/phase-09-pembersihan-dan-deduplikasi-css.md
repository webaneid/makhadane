# Fase 09 — Audit, Pembersihan Dead CSS (`0 dead CSS rules`), & Penghapusan Folder `scss/`

- **Status:** `Done`
- **Prasyarat:** Fase 08 (`Done`)
- **Gap Terkait:** `GAP-20` (Selesai penuh)

## Tujuan
Menuntaskan audit dead CSS pada `css/main.css` hingga `composer css:check` lolos dengan **`0 dead CSS rules`**, mengkompilasi ulang `css/main.min.css`, dan menghapus folder `scss/` yang sudah digantikan oleh arsitektur 12 bagian `css/main.css`.

## Perubahan yang Dilakukan
1. **Perbaikan Akurasi `bin/css-audit.py`:**
   - Memperbaiki pendeteksian batas mulai bagian `[03]` (`l.strip().startswith("[03]")`) agar tidak mencocokkan teks `[03]` di komentar daftar isi baris ke-8.
   - Menambahkan class blok inti WordPress Gutenberg (`wp-block-media-text`, `wp-block-media-text__media`, `wp-block-media-text__content`, `has-large-font-size`) serta nilai field pilihan ACF dinamis (`align-content-start`, `align-content-center`, `align-content-end`, `align-content-start-top`, `align-content-center-top`, `align-content-end-top` dari `tp/section/big-slider.php`, dan `gambar-kiri`, `gambar-kanan` dari `tp/section/image-side-text.php`) ke `ALLOWED_DYNAMIC_CLASSES`.
2. **Pembersihan Dead CSS di `css/main.css` & Kompilasi `css/main.min.css`:**
   - Menghapus 19 kelompok aturan CSS warisan yang tidak lagi direferensikan di seluruh template PHP maupun JS (`.ane-col-6`, `.ane-kiri-1`, `.ane-kiri-2`, `.ane-col-333`, `.flex-list`, `.flex-list__item`, `.post-tengah`, `.sub-judul`, `.kontak-cs`, `.skillframe`, `.home-client`, `.btn-default`, `.btn-putih`, `.btn-outline`, `.search-result`, `.header-navigation`, `.tools-mobile`, `.mobile-populer`, `.ane-konten-video`, `.ane-konten-gallery`, `.post-meta-info`, `.ane-cpt-arsip`, `.kontak-cs-mobile`, `.ane-single-overlay`, `.fa-arrow-down`).
   - Ukuran `css/main.min.css` turun lagi dari `132.3 KB` menjadi **`124.1 KB`** (total penghematan dari awal Fase 07: `21.3 KB` CSS terkompresi + `256 KB` font icon).
3. **Penghapusan Folder `scss/`:**
   - Menghapus direktori `scss/` beserta seluruh 25 file `.scss` warisan karena sumber kebenaran tunggal CSS kini adalah `css/main.css` (12 bagian) yang dikompilasi ke `css/main.min.css` melalui `composer css:check`.

## Verifikasi & Cek Kualitas
- `composer css:check` → Lolos (`6613 baris -> css/main.min.css (124.1 KB), 0 dead CSS rules`).
- `composer lint:changed` → Lolos (`php -l` & PHPCS 0 error).
- `composer analyse` → Lolos (PHPStan 0 error).
- `composer security:scan` → Lolos.
- `composer dist:check` → Lolos.
