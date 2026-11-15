# Fase 07 — Optimasi CSS: Struktur 12 Bagian `css/main.css` & Konsolidasi `css/main.min.css`

- **Status:** `Done`
- **Prasyarat:** Fase 06 (`Done`)

## Tujuan
Menata ulang `css/main.css` menjadi **single source of truth** CSS berstruktur **12 bagian bernomor (`[01]`–`[12]`)** sesuai `docs/architecture/architecture-css.md`, menggabungkan `css/FontAne.css` ke Bagian `[01]` agar menghemat 1 HTTP request render-blocking, meng-compile `css/main.min.css`, dan membersihkan file `.map` serta `.css` redundan.

## Ruang Lingkup (Scope)
1. **Struktur 12 Bagian di `css/main.css`:**
   - `[01] Icons, FontAne & CSS Variables (:root)` (termasuk `.ane-icon`, `.ane-svg`, dan konsolidasi `css/FontAne.css` dengan perbaikan relative path `../fonts/FontAne.*`).
   - `[02] Reset & Flexbox Grid System (.ane-container, .ane-col-*, flex utilities)`.
   - `[03] Base Typography, Layout & Global Components`.
   - `[04] Buttons, Social Media & Breadcrumbs`.
   - `[05] Header, Desktop Menu & Mobile MeanMenu`.
   - `[06] Landing Page Sections & Blog Page Layout`.
   - `[07] Hero Big Slider & Featured Box`.
   - `[08] Single Post Layout & Post Cards`.
   - `[09] Rich Article Content & Gutenberg Blocks (.ane-content)`.
   - `[10] Custom Page Templates (.ane-page-*)`.
   - `[11] Archives, Right Sidebar & CPT Cards (Testimoni, Ustadz)`.
   - `[12] Customer Care Chat, Footer, FAQ Accordion, Mobile Nav & Linktree`.
2. **Konsolidasi & Pembersihan File di `css/`:**
   - Hapus `wp_enqueue_style( 'ane-font-icon', ... )` di `inc/makhadane.php`.
   - Hapus `css/FontAne.css`, `css/main.css.map`, `css/main.min.css.map`, `css/admin.css`, `css/admin.css.map`, dan `css/admin.min.css.map`.
   - Compile `css/main.css` → `css/main.min.css` (1.554 `{` == 1.554 `}`).
3. **Dokumentasi Arsitektur:**
   - Perbarui `docs/architecture/architecture-css.md` sesuai class grid Flexbox `.ane-container`/`.ane-col-*` milik `makhadane`.

## Hasil Verifikasi
- `composer lint:changed` → `0 errors, 0 warnings`
- `composer analyse` → `0 errors`
- `composer security:scan` → `LOLOS`
- `composer dist:check` → `LOLOS`
