# Arsitektur CSS & SOP Redesign — Makhad Ane

> Dokumen referensi wajib setiap kali mengubah tampilan, menambah komponen UI,
> atau menjalankan **Fase Redesign Total**. Tujuan utamanya: menjaga
> `css/main.min.css` tetap **1 file tunggal yang super ringan** dan **0% dead CSS**.

---

## 1. Prinsip Utama CSS `makhadane`

1. **Satu File Produksi Front-End (`css/main.min.css`):**
   - `css/main.css` adalah **file sumber (source)** yang diedit oleh developer/AI agent (dikecualikan dari ZIP rilis lewat `.distignore`).
   - `css/main.min.css` adalah **hasil compile/minify otomatis** dari `css/main.css` menggunakan perintah `composer css:check` (`python3 bin/css-audit.py`).
   - JANGAN pernah mengedit `css/main.min.css` secara langsung tanpa mengubah `css/main.css`.
   - JANGAN menambah file `.css` baru di folder `css/` (hanya `css/main.css`, `css/main.min.css`, dan `css/admin.min.css`).

2. **Reuse Class yang Sudah Ada Sebelum Menambah CSS Baru:**
   Sebelum menulis selector CSS baru, gunakan class **Flexbox Grid System & Utility Makhad Ane** yang sudah tersedia di Bagian `[02]`–`[04]`:
   - **Container & Row/Column:** `.ane-container`, `.ane-col-row`, `.ane-col-column`, `.ane-kiri`, `.ane-kanan`, `.ane-tengah`, `.isi-konten`.
   - **Grid Proporsional:** `.ane-col-2`, `.ane-col-3`, `.ane-col-4`, `.ane-col-46` (40%/60%), `.ane-col-64` (60%/40%), `.ane-col-37` (30%/70%), `.ane-col-73` (70%/30%), `.ane-col-55` (50%/50%).
   - **Spacing & Padding Utilities:** `.mplr` (margin negatif row `-15px`), `.plr` (padding kolom `15px`), `.mb15`, `.mb20`, `.mb40`, `.mt20`, `.mtb20`, `.mtb40`, `.pt40`, `.pb40`, `.ptb40`.
   - **Tombol & Komponen Global:** `.btn`, `.btn-primary`, `.btn-alternatif`, `.ane-title`, `.section-title`, `.ane-kategori`, `.ane-image`, `.ane-meta`, `.ane-sosmed`, `.breadcrumb-area`.
   - **Icon:** Gunakan helper `makhadane_icon( $name )` (inline SVG `.ane-icon > .ane-svg`) — JANGAN menambah font file baru di `fonts/`.

---

## 2. Peta 12 Bagian di `css/main.css`

File `css/main.css` dibagi menjadi 12 blok bernomor (`[01]` s/d `[12]`):

| Bagian | Nama Bagian | Selector Utama | Dipakai di File Template |
|---|---|---|---|
| `[01]` | Icons, FontAne & CSS Variables (`:root`) | `:root`, `.ane-icon`, `.ane-svg`, `@font-face` (`FontAne`), `.ane-*` | `inc/makhadane.php`, seluruh template PHP |
| `[02]` | Reset & Flexbox Grid System | Reset HTML5, `.ane-container`, `.ane-col-row`, `.ane-col-column`, `.ane-col-2/3/4/46/64/37/73/55`, `.mplr`, `.plr`, `.mb15/20/40`, `.ptb40` | Seluruh template PHP |
| `[03]` | Base Typography, Layout & Global Components | `body`, `.webane-wrapper`, `.content-area`, `.author_bio_section`, `.section-banner`, `.share-this`, `.ane-kategori`, `.ane-image`, `.ane-meta`, `.pagination-area`, `.search-form`, `.section-title`, `.ane-title`, `.owl-nav` | `header.php`, `author.php`, `searchform.php`, `tp/section/banner.php`, `inc/makhadane.php` |
| `[04]` | Buttons, Social Media & Breadcrumbs | `.btn`, `.btn-primary`, `.btn-alternatif`, `.wp-block-button__link`, `.ane-sosmed`, `.breadcrumbs`, `.breadcrumb-area` | `tp/content-sosmed.php`, `inc/makhadane.php` (`ane_breadcrumbs()`) |
| `[05]` | Header, Desktop Menu & Mobile MeanMenu | `.ane-header`, `.desktop-menu`, `#main-menu`, `.primary-mobile-menu`, `.mean-container`, `.Header-search` | `header.php`, `js/makhadane.js` |
| `[06]` | Landing Page Sections & Blog Page Layout | `.landing-page`, `.konten-populer`, `.home-default`, `.home-klasik`, `.home-column`, `.home-sliding`, `.home-ekstrakurikuler`, `.home-cta`, `.home-testimoni`, `.home-ustadz`, `.ane-post-mobile`, `.ane-blog-*` | `tp/pages/home-desktop.php`, `tp/pages/home-mobile.php`, `tp/pages/news-*.php`, `tp/news/*.php`, `tp/section/*.php` |
| `[07]` | Hero Big Slider & Featured Box | `.home-slider`, `.home-slider-item`, `.pemanis-slider`, `.section-featured` | `tp/section/big-slider.php`, `tp/section/featured.php` |
| `[08]` | Single Post Layout & Post Cards | `.ane-post-single`, `.ane-konten-default`, `.ane-konten-overlay`, `.ane-konten-klasik`, `.ane-konten-lis`, `.ane-konten-lis-title`, `.post-meta-info`, `.ane-konten-nav` | `tp/single.php`, `tp/single-video.php`, `tp/content*.php` |
| `[09]` | Rich Article Content & Gutenberg Blocks (`.ane-content`) | `.ane-content`, `.wp-block-*`, `.alignleft`, `.alignright`, `.post-nav`, `blockquote`, `.comments-form`, `.ane-post-block` | `tp/single.php`, `tp/content-page.php`, `inc/makhadane.php` |
| `[10]` | Custom Page Templates (`.ane-page-*`) | `.ane-page-default`, `.ane-page-profil`, `.ane-page-kelembagaan`, `.ane-cpt-arsip`, `.ane-page-kontak`, `.kontak-cs-mobile` | `tp/pages/profil.php`, `tp/pages/kelembagaan.php`, `tp/content-page-kontak.php`, `tp/single-ekstrakurikuler.php` |
| `[11]` | Archives, Right Sidebar & CPT Cards (Testimoni, Ustadz) | `.judul-arsip`, `.ane-arsip`, `.ane-arsip-post`, `.right-sidebar`, `.ane-sidebar`, `.ane-konten-testimoni`, `.konten-terkait-v2`, `.ane-konten-ustadz`, `.ane-single-ustadz` | `archive*.php`, `category.php`, `sidebar.php`, `tp/content-testimoni.php`, `tp/content-ustadz.php`, `tp/single-ustadz.php` |
| `[12]` | Customer Care Chat, Footer, FAQ Accordion, Mobile Nav & Linktree | `.whatsapp-chat`, `.ane-footer`, `#footer-bar`, `.ane-faq`, `.ane-mobile-nav`, `.ane-mobile-cat`, `.ane-linktree` | `footer.php`, `archive-faq.php`, `tp/mobile/menu-footer.php`, `tp/mobile/menu-lembaga.php`, `tp/mobile/kategori*.php`, `page-linktree.php` |

---

## 3. SOP Wajib Saat Redesign (Per Bagian / Total)

Setiap kali melakukan redesign pada suatu bagian (mis. Header, Hero Slider, Card Berita, Halaman Profil, atau Footer):

1. **Identifikasi Blok di `css/main.css`:**
   Lihat tabel Bagian `[01]`–`[12]` di atas dan temukan blok CSS komponen yang akan di-redesign.
2. **Ganti, Jangan Menumpuk (Replace, Don't Append):**
   - DILARANG menaruh CSS baru di baris paling bawah `css/main.css` untuk menimpa (`override`) style lama dari komponen yang sama.
   - Edit langsung pada blok bagian (`[01]`–`[12]`) yang bersangkutan dan **hapus properti/selector lama** yang sudah digantikan desain baru.
3. **Hapus Class Mati Saat Mengubah HTML Template:**
   - Jika di template PHP sebuah class diganti atau dihapus, **wajib hapus juga selector class tersebut dari `css/main.css`** pada commit yang sama.
4. **Jalankan Audit & Compile Otomatis (`composer css:check`):**
   ```bash
   composer css:check   # menjalankan python3 bin/css-audit.py
   ```
   Perintah ini otomatis:
   - Memverifikasi kurung kurawal `{` dan `}` seimbang.
   - Meng-compile ulang `css/main.css` → `css/main.min.css`.
   - Memindai seluruh file `.php` dan `.js` dan **menggagalkan check jika ada 1 saja rule di `css/main.css` yang class/ID-nya sudah tidak dipakai lagi** (`0 dead CSS rules`).
