# Retrofit Gaps — Makhad Ane

> Daftar lengkap kesenjangan antara kode `makhadane` (`v4.1.3`, baseline
> `fe60544`) dan standar Webane (`AGENTS.md` + `architecture-security.md`).
> Setiap item memiliki ID unik (`GAP-XX`) agar mudah dirujuk di commit dan
> phase doc. Jangan perbaiki di luar fase yang ditugaskan.

## Ringkasan Per Tingkat Keparahan

| Level | Jumlah | Status | Target Fase |
|---|---|---|---|
| 🔴 **Critical** | 2 | ✅ Selesai (2/2) | Fase 03 (`GAP-01`, `GAP-02` ✅) |
| 🟠 **High** | 4 | ✅ Selesai (4/4) | Fase 02 (`GAP-05` ✅) & Fase 03 (`GAP-03`, `GAP-04`, `GAP-07` ✅) |
| 🟡 **Medium** | 10 | ✅ Selesai (10/10) | Fase 02 (`GAP-12` ✅), Fase 03 (`GAP-06`, `GAP-08`, `GAP-09`, `GAP-10`, `GAP-13`, `GAP-14` ✅) & Fase 04 (`GAP-11`, `GAP-15`, `GAP-16` ✅) |
| 🔵 **Low / Clean-up** | 6 | ✅ Selesai (6/6) | Fase 02 (`GAP-17` ✅), Fase 04 (`GAP-22` ✅), Fase 05 (`GAP-18` ✅), Fase 06 (`GAP-19` ✅), Fase 07–09 (`GAP-20` ✅) & Fase 10 (`GAP-21` ✅) |

---

## A. Keamanan (Security)

### GAP-01 — 🔴 Critical: Hardcoded Google Maps API Key & Unconditional Enqueue — ✅ Fixed (Fase 03)
- **Lokasi:** [inc/acf.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/acf.php)
- **Masalah:** API key Google Maps (`AIzaSyCa5iaW5vvMX2d4Ul4vC88Y6BhYFP5YCtM`) di-hardcode secara plaintext di kode sumber dan script Google Maps di-enqueue di **setiap halaman** front-end.
- **Status Fix (Fase 03):** Plaintext API key dihapus; diganti `makhadane_get_google_maps_api_key()` (konstanta `MAKHADANE_GMAPS_API_KEY`, ACF option `ane_google_maps_api_key` pada tab **API Keys**, atau fallback XOR-Hex), dan script `googlemap` hanya di-enqueue pada `is_page_template( 'page-kontak.php' )`.

### GAP-02 — 🔴 Critical: `remove_filter( 'pre_user_description', 'wp_filter_kses' )` & Output Tanpa Escaping + Sanitasi Judul Anti-HTML/MS Word — ✅ Fixed (Fase 03)
- **Lokasi:** [inc/makhadane.php:911](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php#L911), [inc/acf.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/acf.php), [inc/post.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/post.php), dan template front-end.
- **Masalah:** Mematikan filter `wp_filter_kses` pada bio user membuka celah Stored XSS; output warna CSS `--ane-warna-*` di `inc/acf.php` dicetak tanpa `sanitize_hex_color()`; judul post yang di-copy-paste dari Microsoft Word membawa tag HTML/`mso-*` yang merusak tampilan dan keamanan.
- **Status Fix (Fase 03):** `remove_filter( 'pre_user_description', 'wp_filter_kses' )` dihapus; warna hex divalidasi dengan `sanitize_hex_color()`; sanitasi judul `makhadane_clean_title_string()` dipasang pada `title_save_pre` dan `the_title` (prioritas 5); serta pemanggilan judul di template di-escape dengan `esc_html()`/`esc_attr()`.

### GAP-03 — 🟠 High: 10 File PHP Belum Memiliki Guard `defined( 'ABSPATH' ) || exit;` — ✅ Fixed (Fase 03)
- **Lokasi:** [inc/acf.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/acf.php), [inc/admin.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/admin.php), [inc/custompost.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/custompost.php), [inc/images.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/images.php), [inc/post.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/post.php), [inc/widgets.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/widgets.php), [inc/admin/user.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/admin/user.php), [page-kontak.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/page-kontak.php), [page-linktree.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/page-linktree.php), [tp/content-sliders.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/tp/content-sliders.php).
- **Masalah:** Dapat diakses langsung lewat HTTP request dan memicu fatal error yang membocorkan full path server.
- **Status Fix (Fase 03):** Guard `defined( 'ABSPATH' ) || exit;` telah ditambahkan di seluruh file PHP; `composer security:scan` lolos 100%.

### GAP-04 — 🟠 High: Upload SVG Terbuka untuk Semua Role Upload & Sanitasi Regex Tidak Memadai — ✅ Fixed (Fase 03)
- **Lokasi:** [inc/makhadane.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php) dan [inc/media.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/media.php)
- **Masalah:**
  1. `ane_allowed_mime_types()` di `inc/makhadane.php:1269` me-return array baru yang **menimpa dan menghapus seluruh MIME default WordPress** (PDF, DOCX, MP4, MP3, ZIP tidak lagi bisa di-upload!).
  2. Upload SVG diizinkan tanpa pengecekan `current_user_can( 'manage_options' )` dan sanitasi hanya memakai 2 baris `preg_replace`.
- **Status Fix (Fase 03):** Fungsi duplikat `ane_allowed_mime_types()` di `inc/makhadane.php` dihapus; upload SVG di `inc/media.php` dibatasi hanya untuk administrator (`current_user_can( 'manage_options' )`) dan disanitasi terhadap `<!DOCTYPE>`, `<!ENTITY>` (XXE), `<script>`, `<foreignObject>`, `<iframe>`, `<object>`, `<embed>`, `<applet>`, `on*`, dan URI `javascript:`/`data:`.

### GAP-05 — 🟠 High: Handler `ane_force_check` & `ane_debug` (`print_r`) di `inc/updater.php` Tanpa Nonce & Capability Check — ✅ Fixed (Fase 02)
- **Lokasi:** [inc/updater.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/updater.php)
- **Masalah:** Parameter `$_GET['ane_force_check']` menghapus transient update tanpa mengecek `current_user_can( 'update_themes' )` maupun nonce, dan `$_GET['ane_debug']` menjalankan `print_r()` data transient di admin. Selain itu updater belum mendukung token repo privat (`MAKHADANE_UPDATER_TOKEN` / `ane_updater_token`).
- **Status Fix (Fase 02):** `inc/updater.php` telah ditulis ulang ke standar prosedural `makhadane_updater_*` dengan pengecekan `current_user_can( 'update_themes' )` + `check_admin_referer( 'makhadane_updater_action' )`, dukungan token GitHub privat (`MAKHADANE_UPDATER_TOKEN` & field ACF `ane_updater_token`), tab Update di `ane-general-setting`, serta penghapusan kode debug `print_r()`.

### GAP-06 — 🟡 Medium: Endpoint AJAX `nopriv` Post Views Tanpa Validasi Status Post & Sanitisasi IP — ✅ Fixed (Fase 03)
- **Lokasi:** [inc/makhadane.php:1303-1355](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php#L1303-L1355)
- **Masalah:** `ane_increment_views_ajax()` tidak memvalidasi `wp_unslash()` maupun mengecek `'publish' === get_post_status( $post_id )`, dan `$_SERVER['REMOTE_ADDR']` diakses tanpa `sanitize_text_field( wp_unslash() )`.
- **Status Fix (Fase 03):** `ane_set_views()` dan `ane_increment_views_ajax()` kini memvalidasi `absint( wp_unslash() )`, `'publish' === get_post_status( $post_id )`, serta men-sanitize `$_SERVER['REMOTE_ADDR']`.

---

## B. Bug & Logic Error (Tertangkap PHPStan Level 3 & Audit Manual)

### GAP-07 — 🟠 High: Kesalahan Hook & Tipe Return di `inc/media.php` (3 Error PHPStan) — ✅ Fixed (Fase 03)
- **Lokasi:** [inc/media.php:69-120](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/media.php#L69-L120)
- **Masalah:**
  1. `ane_display_svg_in_media_library()` di-hook ke filter `wp_get_attachment_image_src` (yang seharusnya me-return `array|false` berisi `[url, width, height, is_intermediate]`), tetapi fungsi ini me-return string `<img src="...">` jika attachment adalah SVG — menyebabkan fatal error saat kode memanggil `$image[0]`!
  2. Docblock `ane_fix_svg_thumbnail_display()` menulis `@param string $response` padahal `wp_prepare_attachment_for_js` mengirim `array`.
- **Status Fix (Fase 03):** Diperbaiki di `inc/media.php` sehingga `ane_display_svg_in_media_library()` mengembalikan `array( $url, 150, 150, false )` dan docblock `ane_fix_svg_thumbnail_display()` sesuai tipe `array<string, mixed>`.

### GAP-08 — 🟡 Medium: 10 Error PHPStan Lainnya di `inc/`, `single.php`, dan `tp/single.php` — ✅ Fixed (Fase 03)
- **Lokasi:** `inc/custompost.php`, `inc/makhadane.php`, `inc/post.php`, `single.php`, `tp/single.php`, `inc/seo.php`, `inc/widgets.php`.
- **Status Fix (Fase 03):** Seluruh 13 error PHPStan telah diperbaiki dan `phpstan-baseline.neon` kini **0 error** (`ignoreErrors: []`).

### GAP-09 — 🟡 Medium: Pemanggilan Fungsi Lama yang Tidak Ada di `footer.php` — ✅ Fixed (Fase 03)
- **Lokasi:** [footer.php:89-95](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/footer.php#L89-L95)
- **Masalah:** `footer.php` memeriksa `function_exists( 'webane_load_dynamic_copyright_year' )` dan `function_exists( 'webane_load_designed_by' )`, padahal nama fungsinya adalah `ane_get_designed_by()` dan `ane_get_copyright_year()`.
- **Status Fix (Fase 03):** Diperbarui untuk memanggil `ane_get_copyright_year()` dan `ane_get_designed_by()` dengan `wp_kses_post()`.

### GAP-10 — 🟡 Medium: Redirect Paksa 301 ke Homepage di `inc/handle.php` & Hook Salah — ✅ Fixed (Fase 03)
- **Lokasi:** [inc/handle.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/handle.php)
- **Masalah:** `codesnippets_disable_comments_redirect()` me-redirect `is_single() || is_page()` ke `home_url()` jika `comments_open()` bernilai true.
- **Status Fix (Fase 03):** Fungsi redirect berbahaya tersebut telah dihapus dari `inc/handle.php`.

### GAP-11 — 🟡 Medium: Inkonsistensi Case Slug Taxonomy `'kelas'` vs `'Kelas'` — ✅ Fixed (Fase 04)
- **Lokasi:** [inc/custompost.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/custompost.php) dan [taxonomy-Kelas.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/taxonomy-Kelas.php).
- **Masalah:** Di server Linux (case-sensitive), template `taxonomy-Kelas.php` tidak terpanggil untuk taxonomy `'kelas'`.
- **Status Fix (Fase 04):** Ditambahkan filter `makhadane_resolve_kelas_taxonomy_template()` pada `taxonomy_template` di `inc/custompost.php` sehingga baik `'kelas'` maupun `'Kelas'` selalu memuat `taxonomy-Kelas.php`.

---

## C. Performa & Database

### GAP-12 — 🟡 Medium: `update_option()` ×9 di `after_setup_theme` (Setiap Request) — ✅ Fixed (Fase 02)
- **Lokasi:** [functions.php:75-107](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/functions.php#L75-L107)
- **Masalah:** Mengubah 9 option ukuran gambar di setiap request halaman.
- **Status Fix (Fase 02):** Dipindahkan ke `makhadane_configure_image_sizes()` pada hook `after_switch_theme` dan `makhadane_maybe_upgrade_image_sizes()` pada `admin_init` (hanya dijalankan satu kali saat versi `MAKHADANE_VERSION` berubah).

### GAP-13 — 🟡 Medium: `update_option( 'sticky_posts', array() )` di `init` (Setiap Request) — ✅ Fixed (Fase 03)
- **Lokasi:** [inc/post.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/post.php)
- **Masalah:** Menulis ke tabel `wp_options` di setiap request (`init`).
- **Status Fix (Fase 03):** Hook `add_action( 'init', 'ane_remove_all_sticky_posts' )` telah dihapus dari `inc/post.php`.

### GAP-14 — 🟡 Medium: Query `$wpdb` Tahun Copyright Tanpa Cache Transient — ✅ Fixed (Fase 03)
- **Lokasi:** [inc/makhadane.php:1116-1155](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php#L1116-L1155)
- **Masalah:** Menjalankan query agregasi `MIN(post_date_gmt)` / `MAX(post_date_gmt)` ke tabel `wp_posts` tanpa transient cache.
- **Status Fix (Fase 03):** Disimpan dalam transient `makhadane_copyright_year` (`DAY_IN_SECONDS`) dan di-flush saat `save_post`.

---

## D. Maintainability, Aset, & Standar Webane

### GAP-15 — 🟡 Medium: Text Domain Campuran (`'elemenane'`) & Konflik Hook Submenu Admin — ✅ Fixed (Fase 03 & Fase 04)
- **Lokasi:** [inc/admin/facebook-comments.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/admin/facebook-comments.php), [inc/tracking-scripts.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/tracking-scripts.php), [inc/admin/linktree-analytics.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/admin/linktree-analytics.php), [tp/content-linktree.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/tp/content-linktree.php).
- **Masalah:** `inc/admin/facebook-comments.php` masih memakai text domain `'elemenane'`; `inc/admin/linktree-analytics.php` mengecek `$hook` yang salah; `tp/content-linktree.php` mengarah ke `logo-newsane.png` yang tidak ada.
- **Status Fix (Fase 03 & Fase 04):** Seluruh text domain diseragamkan ke `'makhadane'`, pengecekan `$hook` di `inc/admin/linktree-analytics.php` diperbaiki, dan fallback logo di `tp/content-linktree.php` diarahkan ke `logo-makhadane.png`.

### GAP-16 — 🟡 Medium: Template Part Menggantung & Aset Tidak Terpakai — ✅ Fixed (Fase 04)
- **Lokasi:** `tp/single.php`, `tp/single-ustadz.php`, `tp/content-sliders.php`, `tp/home-categories.php`, `tp/mobile/kategori.php`, `img/logo-sekolahane.png`, `img/outdoor-basketball-court-school-kids-play.jpg`.
- **Masalah:** File template part duplikat tidak pernah dipanggil dan membingungkan pemeliharaan; foto sampel 8,26 MB tidak terpakai membebani ukuran paket rilis.
- **Status Fix (Fase 04):** 4 template part mati dan 2 file gambar tidak terpakai dihapus (hemat 8,27 MB), serta panggilan `get_template_part` ke file yang tidak ada di `tp/mobile/kategori.php` dibersihkan.

### GAP-17 — 🔵 Low: Workflow CI/CD Belum Memakai `semantic-release` & `.distignore` — ✅ Fixed (Fase 02)
- **Lokasi:** [.github/workflows/ci.yml](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/.github/workflows/ci.yml), [.github/workflows/release.yml](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/.github/workflows/release.yml), [.releaserc.json](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/.releaserc.json)
- **Masalah:** Workflow lama masih menggunakan `actions/create-release@v1` (deprecated), pemicu tag manual, dan daftar `--exclude` manual. Belum ada `.github/workflows/ci.yml` dan `.releaserc.json`.
- **Status Fix (Fase 02):** Dipasang `.github/workflows/ci.yml`, `.github/workflows/release.yml` (2 jalur: `semantic-release` otomatis + manual tag fallback dengan `.distignore` & `bin/check-dist.sh`), dan `.releaserc.json` sesuai ADR-0002.

### GAP-18 — 🔵 Low: Belum Ada Auto-Convert Upload Gambar ke WebP (`inc/webp-converter.php`) — ✅ Fixed (Fase 05)
- **Lokasi:** [inc/webp-converter.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/webp-converter.php)
- **Masalah:** Gambar JPG/PNG yang diunggah pelanggan belum otomatis dikonversi ke `.webp`.
- **Status Fix (Fase 05):** Diimplementasikan `inc/webp-converter.php` sesuai ADR-0004 (`MAKHADANE_WEBP_QUALITY = 78`, konversi otomatis JPG/PNG ke `.webp` saat `wp_handle_upload` & `wp_handle_sideload`, fallback PNG 8-bit palette ke truecolor alpha, dan penghapusan file asli non-WebP setelah `.webp` terverifikasi valid).

### GAP-19 — 🔵 Low: Script JS Terpecah & Inline `<script>` di `footer.php` — ✅ Fixed (Fase 06)
- **Lokasi:** [js/makhadane.js](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/js/makhadane.js), [inc/makhadane.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php), [footer.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/footer.php)
- **Masalah:** Banyak file JS di `js/` (`gmap.js`, `postviews-cache.js`, `linktree-tracking.js`, `admin-*.js`), inline `<script>` di `footer.php` dan `inc/post.php`, selector mati di `js/makhadane.js`, versi OwlCarousel CSS (`2.2.1`) vs JS (`2.3.4`) tidak seragam, serta library dimuat di semua halaman.
- **Status Fix (Fase 06):** Dikonsolidasikan menjadi **1 file tunggal `js/makhadane.js`** (9 file JS redundan dihapus), inline `<script>` di `footer.php` & `inc/post.php` dihapus, selector mati (`.cc-contactpop`, `.commrnt-toggle`) dan `console.log` debug dibersihkan, `titleSrc` Magnific Popup diamankan dari DOM XSS, serta `owl-carousel` (`2.3.4`) dan `magnific-popup` (`1.1.0`) dimuat secara kondisional dengan cache-busting `filemtime()`.

### GAP-20 — 🔵 Low: Arsitektur CSS Masih Berbasis SCSS Manual & Belum 12 Bagian + `FontAne` (256 KB) — ✅ Fixed (Fase 07–09)
- **Lokasi:** [css/main.css](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/css/main.css), [css/main.min.css](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/css/main.min.css), [inc/makhadane.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php)
- **Masalah:** Memuat file font eksternal `FontAne` (256 KB) dan CSS belum terstruktur dalam 12 bagian (`[01]`–`[12]`) yang diaudit oleh `composer css:check` (`0 dead CSS rules`).
- **Status Fix (Fase 07–09):** `css/main.css` direstrukturisasi ke 12 bagian (`[01]`–`[12]`) dan dikompilasi ke 1 file rilis `css/main.min.css` (`124.1 KB`, turun dari `145.4 KB`), seluruh icon `FontAne` & FontAwesome dimigrasikan ke helper inline SVG `makhadane_icon()`, `0 dead CSS rules` diverifikasi oleh `composer css:check`, serta folder `fonts/`, `scss/`, `css/FontAne.css`, `css/admin.css`, dan 4 file `.map` dihapus.

### GAP-21 — 🔵 Low: Definisi Field ACF Belum Disimpan di Repo (`inc/acf-json/`) — ✅ Fixed (Fase 10)
- **Lokasi:** [inc/acf-json/field-groups.json](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/acf-json/field-groups.json), [inc/acf-importer.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/acf-importer.php)
- **Masalah:** Struktur field group ACF hanya ada di database situs pelanggan.
- **Status Fix (Fase 10):** Seluruh 16 Field Group ACF database diekspor ke `inc/acf-json/field-groups.json` dan dilengkapi `inc/acf-importer.php` (auto-fallback lokal jika belum ada di DB, auto-sync 1 kali saat hash MD5 JSON berubah, auto-import saat `after_switch_theme`, dan tab **Import ACF JSON** di halaman `ane-general-setting`).

### GAP-22 — 🔵 Low: Format Kode Belum Sesuai WPCS — ✅ Fixed (Fase 04)
- **Lokasi:** Seluruh codebase PHP.
- **Status Fix (Fase 04):** Dijalankan `composer lint:fix` (`phpcbf`) yang memperbaiki 2.537 pelanggaran format WPCS pada 64 file PHP secara otomatis.

---

## E. Audit Security 2026-11-19

> Audit manual atas seluruh `*.php` (superglobal, AJAX, `$wpdb`, updater,
> output escaping, tracking scripts). Perbaikan dikerjakan dalam satu commit
> `fix(security): …`.

### GAP-23 — 🟡 Medium: Token GitHub Updater Bisa Terkirim ke Host Lain — ✅ Fixed
- **Lokasi:** [inc/updater.php](inc/updater.php) (`makhadane_updater_auth_header()`)
- **Masalah:** Filter `http_request_args` mencocokkan URL dengan `strpos()`, sehingga URL apa pun yang *memuat* `github.com/webaneid/makhadane/` (mis. `https://evil.tld/?x=github.com/webaneid/makhadane/`) ikut dikirimi header `Authorization`.
- **Status Fix:** URL di-parse dengan `wp_parse_url()`; token hanya dikirim bila skema `https`, host persis `api.github.com` / `github.com`, dan path diawali repo yang dikonfigurasi.

### GAP-24 — 🟠 Low-Medium: AJAX Publik Klik Linktree Tanpa Whitelist & Batas — ✅ Fixed
- **Lokasi:** [inc/admin/linktree-core.php](inc/admin/linktree-core.php) (`ane_track_linktree_click()`)
- **Masalah:** Handler `nopriv` menulis ke DB tiap request; nonce ikut ter-cache bersama halaman sehingga bot bisa membanjiri tabel. `link_type` tidak di-whitelist dan panjang field tidak dibatasi.
- **Status Fix:** Whitelist `link_type` (`whatsapp`, `website`, `tiktok`, `telegram`, `custom`), field dipotong sesuai panjang kolom, rate limit 30 request/menit per IP (transient `makhadane_lt_rate_*`).

### GAP-25 — 🔵 Low: Chart.js dari CDN Tanpa SRI — ✅ Fixed
- **Lokasi:** [inc/admin/linktree-analytics.php](inc/admin/linktree-analytics.php)
- **Status Fix:** Filter `script_loader_tag` menambahkan `integrity` (SHA-384) + `crossorigin="anonymous"` untuk `chart.js@4.4.0`. **Saat versi Chart.js dinaikkan, hash wajib dihitung ulang.**

### GAP-26 — 🔵 Low: View Counter Membuat 1 Transient per IP × Post — ✅ Fixed
- **Lokasi:** [inc/makhadane.php](inc/makhadane.php) (`ane_set_views()`)
- **Status Fix:** Satu transient per IP (`makhadane_views_*`) berisi daftar `post_id => waktu`, entri kedaluwarsa dibuang, maksimum 100 entri per IP.

### GAP-27 — 🔵 Low: `$schema_type` Dicetak ke Komentar HTML Tanpa Validasi — ✅ Fixed
- **Lokasi:** [inc/seo.php](inc/seo.php) (`ane_output_page_schema()`)
- **Masalah:** Nilai meta bisa ditulis di luar dropdown ACF (Custom Fields, XML-RPC); nilai berisi `-->` keluar dari komentar HTML → stored XSS oleh user setingkat editor.
- **Status Fix:** Nilai dicocokkan dengan whitelist 8 tipe schema, fallback `WebPage`.

### GAP-28 — 🔵 Low: `REMOTE_ADDR` di Belakang Proxy/CDN — ⏳ Open
- **Lokasi:** `ane_set_views()` ([inc/makhadane.php](inc/makhadane.php)), `ane_track_linktree_click()` ([inc/admin/linktree-core.php](inc/admin/linktree-core.php))
- **Masalah:** Di balik Cloudflare/reverse proxy, `REMOTE_ADDR` adalah IP proxy: semua pengunjung berbagi satu jatah dedupe view (1×/jam/post) dan satu jatah rate limit Linktree (30/menit).
- **Catatan:** `X-Forwarded-For` / `CF-Connecting-IP` **tidak** dipakai karena bisa dipalsukan. Solusi yang aman butuh daftar proxy tepercaya (mis. konstanta di `wp-config.php`) → putuskan lewat ADR sebelum dikerjakan.

### GAP-29 — 🔵 Info: Field Tracking Scripts Melewati `unfiltered_html` di Multisite — ⏳ Open
- **Lokasi:** [inc/tracking-scripts.php](inc/tracking-scripts.php); field `ane_ga_header`, `ane_ga_footer`, `ane_sc_header`, `ane_metapixel_header`, `ane_metasdk_body` (halaman `ane-general-setting`, capability `manage_options`).
- **Masalah:** Output sengaja mentah (kode GTM/Pixel). Di instalasi tunggal ini aman. Di **multisite**, admin per-situs tidak punya `unfiltered_html` namun tetap bisa menyuntik JS lewat field ini.
- **Catatan:** Hanya relevan bila ada pelanggan multisite. Perbaikan: batasi output/simpan bila `current_user_can( 'unfiltered_html' )`.

### GAP-30 — 🟡 Medium: Baseline `composer lint` Masih Merah — ⏳ Open
- **Lokasi:** 31 file, antara lain [inc/seo.php](inc/seo.php) (41 error + 1 warning: fungsi tanpa prefix `makhadane_`), `category.php`, `taxonomy-pelajaran.php`, `tp/content-page.php`, `tp/section/content.php`, `tp/mobile/kategori.php`.
- **Masalah:** Pelanggaran lama yang belum tercatat di baseline PHPCS; jumlah di `inc/seo.php` sama sebelum dan sesudah audit ini (tidak ada pelanggaran baru dari perubahan audit).
- **Catatan:** Kerjakan per file saat file tersebut disentuh, atau jadikan fase tersendiri.
