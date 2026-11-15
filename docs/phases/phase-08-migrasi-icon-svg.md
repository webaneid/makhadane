# Fase 08 — Migrasi Icon `FontAne` & FontAwesome ke Inline SVG (`makhadane_icon()`) + Hapus `fonts/`

- **Status:** `Done`
- **Prasyarat:** Fase 07 (`Done`)

## Tujuan
Menghilangkan seluruh ketergantungan pada file icon font eksternal (`fonts/FontAne.*` 256 KB dan class FontAwesome `fa-*`), menggantinya dengan sistem inline SVG `makhadane_icon( $name )` yang bebas HTTP request eksternal dan bebas CLS/FOIT.

## Ruang Lingkup (Scope)
1. **Helper `makhadane_icon( $name )` & `ane_icon( $name )` di `inc/makhadane.php`:**
   - Mendukung semua nama icon lama (`ane-*`, `fa-*`, `fa *`) beserta peta alias lengkap untuk input field ACF (`ane_icon`) di pelanggan lama.
   - Mengembalikan `<i class="ane-icon" aria-hidden="true"><svg class="ane-svg" viewBox="0 0 24 24" ...>...</svg></i>`.
   - Menambahkan filter `makhadane_allow_svg_in_kses()` pada `wp_kses_allowed_html` agar `wp_kses_post()` mengizinkan `<svg>` dan `<path>`.
2. **Migrasi Seluruh Template & Script ke `makhadane_icon()`:**
   - `header.php`, `footer.php`, `searchform.php`, `inc/makhadane.php`, `inc/post.php`, `js/makhadane.js`.
   - `tp/content-sosmed.php`, `tp/content-linktree.php`, `tp/mobile/menu-footer.php`, `tp/mobile/menu-lembaga.php`, `tp/mobile/kategori-menu.php`.
   - `tp/section/angka.php`, `tp/section/calltoaction.php`, `tp/section/ekstra.php`, `tp/section/guru.php`, `tp/section/testimoni.php`.
   - `tp/news/classic.php`, `tp/news/column.php`, `tp/news/default.php`, `tp/news/home-gallery.php`, `tp/news/sliding.php`.
3. **Pembersihan `FontAne` dari `css/main.css` & Penghapusan `fonts/`:**
   - Menghapus seluruh blok `@font-face` dan `.ane-*:before` dari Bagian `[01]` `css/main.css` serta mengganti 8 pseudo-element `FontAne` dengan karakter Unicode / SVG mask murni.
   - Menghapus folder `fonts/` (`FontAne.eot`, `FontAne.svg`, `FontAne.ttf`, `FontAne.woff` — hemat 256 KB).
   - Compile ulang `css/main.min.css` (turun dari `145.4 KB` ke `132.3 KB`).

## Hasil Verifikasi
- `composer lint:changed` → `0 errors, 0 warnings`
- `composer analyse` → `0 errors`
- `composer security:scan` → `LOLOS`
- `composer dist:check` → `LOLOS`
