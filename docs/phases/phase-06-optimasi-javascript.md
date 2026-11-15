# Fase 06 — Optimasi & Konsolidasi JavaScript (1 File `js/makhadane.js` & Conditional Enqueue)

- **Status:** `Done`
- **Terkait:** `GAP-19`

## Tujuan
Menyederhanakan seluruh JavaScript front-end theme menjadi **hanya 1 file lokal (`js/makhadane.js`)** yang bersih dari selector/plugin mati dan raw `<script>` inline di template, serta memuat library eksternal (`OwlCarousel2` & `Magnific Popup`) secara kondisional hanya pada halaman yang benar-benar menggunakannya.

## Langkah
- [x] **Konsolidasi Menjadi 1 File `js/makhadane.js` di Folder `js/`:**
  - Gabungkan logika `js/postviews-cache.js` (`postViewsCache`), `js/linktree-tracking.js` (`aneLinktreeTracking`), dan pengiriman pesan WhatsApp round-robin dari inline `<script>` `footer.php` ke dalam `js/makhadane.js`.
  - Hapus file duplikat `js/gmap.js`, `js/postviews-cache.js`, `js/linktree-tracking.js`, serta `js/admin-*.js` (dipindahkan ke `wp_add_inline_script` pada halaman admin terkait tanpa `console.log` debug, sementara `js/admin-animations.js` yang tidak terpakai/mengganggu dihapus).
  - Hapus inline `<script>` di `footer.php` (diganti atribut `data-wa-numbers` pada `.floating-chat`) dan hapus `ane_hide_admin_sticky_checkbox()` di `inc/post.php` yang memakai event deprecated `DOMNodeInserted`.
- [x] **Pangkas Kode & Selector Mati di `js/makhadane.js`:**
  - Hapus listener `.cc-contactpop`, `.commrnt-toggle a`, dan `.blog-comment-content-wrap` yang tidak ada di template `makhadane`.
  - Rampingkan plugin `$.fn.meanmenu` (hapus hack IE7/IE8, hapus duplikasi listener `$(window).resize`, tambah `aria-label` aksesibilitas).
  - Gunakan selector `[id="home-sliding"], #post-block-slider` agar semua blok carousel `#home-sliding` di beranda ter-inisialisasi, lengkap dengan `aria-label` pada tombol `.owl-prev`, `.owl-next`, dan `.owl-dot`.
  - Tutup celah DOM XSS pada `titleSrc` Magnific Popup menggunakan `$('<strong/>').text(alt).prop('outerHTML')`.
- [x] **Conditional Enqueue di `inc/makhadane.php` (`ane_load_css_and_js`):**
  - Seragamkan versi OwlCarousel2 CSS & JS ke `2.3.4` dan muat hanya saat dibutuhkan (`is_front_page() || is_home() || is_page_template( array( 'page-home.php', 'page-newslist.php' ) ) || is_singular( 'ekstrakurikuler' )` atau saat konten singular memiliki shortcode `[post-block]`).
  - Muat Magnific Popup (CSS & JS) hanya pada halaman artikel/halaman tunggal (`is_singular()` selain landing/kontak/linktree/mcategory).
  - Gunakan `filemtime()` (fallback `MAKHADANE_VERSION`) untuk versi `css/main.min.css` dan `js/makhadane.js`.

## Kriteria Selesai
- Folder `js/` hanya berisi 1 file: `js/makhadane.js`.
- `composer lint:changed`, `composer analyse` (`0 errors`), `composer security:scan`, dan `composer dist:check` lolos 100%.
- `GAP-19` pada `docs/RETROFIT-GAPS.md` berstatus `Fixed`.
