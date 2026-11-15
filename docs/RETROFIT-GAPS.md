# Retrofit Gaps — Makhad Ane

> Daftar lengkap kesenjangan antara kode `makhadane` (`v4.1.3`, baseline
> `fe60544`) dan standar Webane (`AGENTS.md` + `architecture-security.md`).
> Setiap item memiliki ID unik (`GAP-XX`) agar mudah dirujuk di commit dan
> phase doc. Jangan perbaiki di luar fase yang ditugaskan.

## Ringkasan Per Tingkat Keparahan

| Level | Jumlah | Status | Target Fase |
|---|---|---|---|
| 🔴 **Critical** | 2 | ⏳ Belum (0/2) | Fase 03 |
| 🟠 **High** | 4 | 🔄 Sebagian (1/4) | Fase 02 (`GAP-05` ✅) & Fase 03 |
| 🟡 **Medium** | 10 | 🔄 Sebagian (1/10) | Fase 02 (`GAP-12` ✅), Fase 03 & Fase 04 |
| 🔵 **Low / Clean-up** | 6 | 🔄 Sebagian (1/6) | Fase 02 (`GAP-17` ✅) & Fase 04 – Fase 10 |

---

## A. Keamanan (Security)

### GAP-01 — 🔴 Critical: Hardcoded Google Maps API Key & Unconditional Enqueue
- **Lokasi:** [inc/acf.php:14-22](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/acf.php#L14-L22)
- **Masalah:** API key Google Maps (`AIzaSyCa5iaW5vvMX2d4Ul4vC88Y6BhYFP5YCtM`) di-hardcode secara plaintext di kode sumber dan script Google Maps di-enqueue di **setiap halaman** front-end.
- **Rencana Fix (Fase 03):** Hapus plaintext API key; gunakan helper `makhadane_get_google_maps_api_key()` (konstanta `MAKHADANE_GMAPS_API_KEY`, ACF option `ane_google_maps_api_key`, atau fallback terobfuskasi XOR-Hex seperti `sekolahane`), dan enqueue hanya di halaman kontak.

### GAP-02 — 🔴 Critical: `remove_filter( 'pre_user_description', 'wp_filter_kses' )` & Output Tanpa Escaping (Stored XSS)
- **Lokasi:** [inc/makhadane.php:912](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php#L912), [inc/acf.php:50-58](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/acf.php#L50-L58), [inc/makhadane.php:1403](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php#L1403), serta puluhan output variabel/ACF di `tp/news/*.php`, `tp/section/*.php`, `tp/pages/*.php`, dan `tp/content-*.php`.
- **Masalah:** Mematikan filter `wp_filter_kses` pada bio user membuka celah Stored XSS bagi user berlevel Author/Contributor; output warna CSS `--ane-warna-*` di `inc/acf.php` dicetak tanpa `sanitize_hex_color()`/`esc_html()`; sejumlah template part mencetak URL/judul tanpa fungsi `esc_*()`.
- **Rencana Fix (Fase 03):** Hapus `remove_filter( 'pre_user_description', 'wp_filter_kses' )`, validasi warna hex di `inc/acf.php`, dan escape seluruh output di `inc/` serta `tp/`.

### GAP-03 — 🟠 High: 10 File PHP Belum Memiliki Guard `defined( 'ABSPATH' ) || exit;`
- **Lokasi:** [inc/acf.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/acf.php), [inc/admin.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/admin.php), [inc/custompost.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/custompost.php), [inc/images.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/images.php), [inc/post.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/post.php), [inc/widgets.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/widgets.php), [inc/admin/user.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/admin/user.php), [page-kontak.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/page-kontak.php), [page-linktree.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/page-linktree.php), [tp/content-sliders.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/tp/content-sliders.php).
- **Masalah:** Dapat diakses langsung lewat HTTP request dan memicu fatal error yang membocorkan full path server.
- **Rencana Fix (Fase 03):** Tambahkan `defined( 'ABSPATH' ) || exit;` di awal seluruh file PHP hingga `composer security:scan` lolos 100%.

### GAP-04 — 🟠 High: Upload SVG Terbuka untuk Semua Role Upload & Sanitasi Regex Tidak Memadai
- **Lokasi:** [inc/makhadane.php:1269-1279](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php#L1269-L1279) dan [inc/media.php:21-36,142-160](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/media.php#L21-L36)
- **Masalah:**
  1. `ane_allowed_mime_types()` di `inc/makhadane.php:1269` me-return array baru yang **menimpa dan menghapus seluruh MIME default WordPress** (PDF, DOCX, MP4, MP3, ZIP tidak lagi bisa di-upload!).
  2. Upload SVG diizinkan tanpa pengecekan `current_user_can( 'manage_options' )` dan sanitasi hanya memakai 2 baris `preg_replace` yang mudah di-bypass (`<svg onload=...>`, `<foreignObject>`, `xlink:href="javascript:..."`).
- **Rencana Fix (Fase 03):** Hapus fungsi duplikat `ane_allowed_mime_types()` di `inc/makhadane.php`, batasi upload SVG hanya untuk administrator (`current_user_can( 'manage_options' )`), dan perketat sanitasi XML/SVG (seperti standar `sekolahane`).

### GAP-05 — 🟠 High: Handler `ane_force_check` & `ane_debug` (`print_r`) di `inc/updater.php` Tanpa Nonce & Capability Check — ✅ Fixed (Fase 02)
- **Lokasi:** [inc/updater.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/updater.php)
- **Masalah:** Parameter `$_GET['ane_force_check']` menghapus transient update tanpa mengecek `current_user_can( 'update_themes' )` maupun nonce, dan `$_GET['ane_debug']` menjalankan `print_r()` data transient di admin. Selain itu updater belum mendukung token repo privat (`MAKHADANE_UPDATER_TOKEN` / `ane_updater_token`).
- **Status Fix (Fase 02):** `inc/updater.php` telah ditulis ulang ke standar prosedural `makhadane_updater_*` dengan pengecekan `current_user_can( 'update_themes' )` + `check_admin_referer( 'makhadane_updater_action' )`, dukungan token GitHub privat (`MAKHADANE_UPDATER_TOKEN` & field ACF `ane_updater_token`), tab Update di `ane-general-setting`, serta penghapusan kode debug `print_r()`.

### GAP-06 — 🟡 Medium: Endpoint AJAX `nopriv` Post Views Tanpa Validasi Status Post & Sanitisasi IP
- **Lokasi:** [inc/makhadane.php:1317-1358](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php#L1317-L1358)
- **Masalah:** `ane_increment_views_ajax()` tidak memvalidasi `wp_unslash()` maupun mengecek `'publish' === get_post_status( $post_id )`, dan `$_SERVER['REMOTE_ADDR']` diakses tanpa `sanitize_text_field( wp_unslash() )`. Selain itu `single.php:14` dan `single-ustadz.php:14` memanggil `ane_set_views()` secara sinkron sekaligus me-load `js/postviews-cache.js` via AJAX.
- **Rencana Fix (Fase 03):** Validasi `'publish' === get_post_status( $post_id )` dan sanitize `$_SERVER['REMOTE_ADDR']`.

---

## B. Bug & Logic Error (Tertangkap PHPStan Level 3 & Audit Manual)

### GAP-07 — 🟠 High: Kesalahan Hook & Tipe Return di `inc/media.php` (3 Error PHPStan)
- **Lokasi:** [inc/media.php:68-111](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/media.php#L68-L111)
- **Masalah:**
  1. `ane_display_svg_in_media_library()` di-hook ke filter `wp_get_attachment_image_src` (yang seharusnya me-return `array|false` berisi `[url, width, height, is_intermediate]`), tetapi fungsi ini me-return string `<img src="...">` jika attachment adalah SVG — menyebabkan fatal error saat kode memanggil `$image[0]`!
  2. Docblock `ane_fix_svg_thumbnail_display()` menulis `@param string $response` padahal `wp_prepare_attachment_for_js` mengirim `array`.
- **Rencana Fix (Fase 03):** Perbaiki hook/return type di `inc/media.php`.

### GAP-08 — 🟡 Medium: 10 Error PHPStan Lainnya di `inc/`, `single.php`, dan `tp/single.php`
- **Lokasi:**
  - [inc/custompost.php:168-173](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/custompost.php#L168-L173): `ane_custom_post_feed()` di-hook ke `add_filter( 'pre_get_posts', ... )` tanpa return (seharusnya `add_action`).
  - [inc/makhadane.php:911](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php#L911): `ane_author_info_box()` di-hook ke `add_action( 'the_content', ... )` (seharusnya `add_filter`).
  - [inc/post.php:99](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/post.php#L99), [single.php:28-29](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/single.php#L28-L29), [tp/single.php:22-23](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/tp/single.php#L22-L23): Akses properti warisan `$cat->cat_name` pada `WP_Term` (seharusnya `$cat->name`).
  - [tp/single.php:46](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/tp/single.php#L46): `echo ane_get_title()` padahal `ane_get_title()` bertipe `void` (langsung mencetak output).
  - [inc/seo.php:1294](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/seo.php#L1294): Pengecekan `! empty( $subjects )` yang redundan setelah `if ( ! empty( $subjects ) )`.
  - [inc/widgets.php:161](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/widgets.php#L161): Docblock `@return void` pada `Ane_Posts_Widget::form()` tidak cocok dengan `WP_Widget::form()`.
- **Rencana Fix (Fase 03):** Perbaiki seluruh 13 error PHPStan dan kosongkan `phpstan-baseline.neon`.

### GAP-09 — 🟡 Medium: Pemanggilan Fungsi Lama yang Tidak Ada di `footer.php`
- **Lokasi:** [footer.php:93-99](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/footer.php#L93-L99)
- **Masalah:** `footer.php` memeriksa `function_exists( 'webane_load_dynamic_copyright_year' )` dan `function_exists( 'webane_load_designed_by' )`, padahal di `inc/makhadane.php:1085,1117` nama fungsinya adalah `ane_get_designed_by()` dan `ane_get_copyright_year()`. Akibatnya atribusi desain dan tahun copyright dinamis tidak pernah tampil.
- **Rencana Fix (Fase 03):** Panggil `ane_get_copyright_year()` dan `ane_get_designed_by()` (dengan `wp_kses_post()`).

### GAP-10 — 🟡 Medium: Redirect Paksa 301 ke Homepage di `inc/handle.php` & Hook Salah
- **Lokasi:** [inc/handle.php:10-34](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/handle.php#L10-L34)
- **Masalah:** `codesnippets_disable_comments()` di-hook ke `admin_init`, lalu di dalamnya memasang `add_action( 'template_redirect', 'codesnippets_disable_comments_redirect' )` yang me-redirect `is_single() || is_page()` ke `home_url()` jika `comments_open()` bernilai true!
- **Rencana Fix (Fase 03/04):** Bersihkan `inc/handle.php` agar tidak memicu redirect berbahaya pada post/page pelanggan.

### GAP-11 — 🟡 Medium: Inkonsistensi Case Slug Taxonomy `'kelas'` vs `'Kelas'`
- **Lokasi:** [inc/custompost.php:112](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/custompost.php#L112) (`'kelas'`), [single-ustadz.php:27,99](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/single-ustadz.php#L27) (`'kelas'`), [tp/single-ustadz.php:20,85](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/tp/single-ustadz.php#L20) (`'Kelas'`), dan nama file [taxonomy-Kelas.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/taxonomy-Kelas.php).
- **Masalah:** Di server Linux (case-sensitive), template `taxonomy-Kelas.php` tidak akan terpanggil untuk taxonomy `'kelas'`.
- **Rencana Fix (Fase 03/04):** Pastikan kompatibilitas template taxonomy untuk `'kelas'` / `'Kelas'`.

---

## C. Performa & Database

### GAP-12 — 🟡 Medium: `update_option()` ×9 di `after_setup_theme` (Setiap Request) — ✅ Fixed (Fase 02)
- **Lokasi:** [functions.php:75-107](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/functions.php#L75-L107)
- **Masalah:** Mengubah 9 option ukuran gambar di setiap request halaman.
- **Status Fix (Fase 02):** Dipindahkan ke `makhadane_configure_image_sizes()` pada hook `after_switch_theme` dan `makhadane_maybe_upgrade_image_sizes()` pada `admin_init` (hanya dijalankan satu kali saat versi `MAKHADANE_VERSION` berubah).

### GAP-13 — 🟡 Medium: `update_option( 'sticky_posts', array() )` di `init` (Setiap Request)
- **Lokasi:** [inc/post.php:260-263](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/post.php#L260-L263)
- **Masalah:** Menulis ke tabel `wp_options` di setiap request (`init`).
- **Rencana Fix (Fase 03):** Hapus penulisan DB di setiap `init` atau pindahkan ke `after_switch_theme`.

### GAP-14 — 🟡 Medium: Query `$wpdb` Tahun Copyright Tanpa Cache Transient
- **Lokasi:** [inc/makhadane.php:1117-1142](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php#L1117-L1142)
- **Masalah:** Menjalankan query agregasi `MIN(post_date_gmt)` / `MAX(post_date_gmt)` ke tabel `wp_posts` tanpa transient cache.
- **Rencana Fix (Fase 03):** Bungkus dengan transient `makhadane_copyright_year` (`DAY_IN_SECONDS`) dan bersihkan saat `save_post`.

---

## D. Maintainability, Aset, & Standar Webane

### GAP-15 — 🟡 Medium: Text Domain Campuran (`'elemenane'`) & Konflik Hook Submenu Admin
- **Lokasi:** [inc/admin/facebook-comments.php:103-168](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/admin/facebook-comments.php#L103-L168), [inc/tracking-scripts.php:8](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/tracking-scripts.php#L8), [inc/admin/linktree-analytics.php:33](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/admin/linktree-analytics.php#L33).
- **Masalah:** `inc/admin/facebook-comments.php` masih memakai text domain `'elemenane'`; `inc/admin/linktree-analytics.php:33` mengecek `$hook !== 'elemen-ane_page_ane-linktree-analytics'` padahal parent menu berjudul `'Makhad Ane'` (`makhad-ane_page_ane-linktree-analytics`), sehingga Chart.js tidak pernah ter-enqueue di halaman Linktree Analytics!
- **Rencana Fix (Fase 03 & Fase 04):** Seragamkan text domain ke `'makhadane'` dan perbaiki pengecekan `$hook` di `inc/admin/linktree-analytics.php`.

### GAP-16 — 🟡 Medium: Duplikasi Pendaftaran Options Page & Template Part Menggantung
- **Lokasi:** `tp/single.php`, `tp/single-ustadz.php`, `tp/content-sliders.php`, `tp/home-categories.php`, serta referensi `inc/back-compat.php` di `functions.php:14`.
- **Masalah:** File template part tidak pernah dipanggil dan membingungkan pemeliharaan.
- **Rencana Fix (Fase 04):** Hapus file/fungsi duplikat yang tidak terpakai.

### GAP-17 — 🔵 Low: Workflow CI/CD Belum Memakai `semantic-release` & `.distignore` — ✅ Fixed (Fase 02)
- **Lokasi:** [.github/workflows/ci.yml](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/.github/workflows/ci.yml), [.github/workflows/release.yml](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/.github/workflows/release.yml), [.releaserc.json](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/.releaserc.json)
- **Masalah:** Workflow lama masih menggunakan `actions/create-release@v1` (deprecated), pemicu tag manual, dan daftar `--exclude` manual. Belum ada `.github/workflows/ci.yml` dan `.releaserc.json`.
- **Status Fix (Fase 02):** Dipasang `.github/workflows/ci.yml`, `.github/workflows/release.yml` (2 jalur: `semantic-release` otomatis + manual tag fallback dengan `.distignore` & `bin/check-dist.sh`), dan `.releaserc.json` sesuai ADR-0002.

### GAP-18 — 🔵 Low: Belum Ada Auto-Convert Upload Gambar ke WebP (`inc/webp-converter.php`)
- **Lokasi:** `inc/media.php`
- **Masalah:** Gambar JPG/PNG yang diunggah pelanggan belum otomatis dikonversi ke `.webp`.
- **Rencana Fix (Fase 05):** Implementasikan `inc/webp-converter.php` sesuai ADR-0004.

### GAP-19 — 🔵 Low: Script JS Terpecah & Inline `<script>` di `footer.php`
- **Lokasi:** `js/makhadane.js`, `js/gmap.js`, `js/postviews-cache.js`, `footer.php:183-228`
- **Masalah:** Banyak file JS front-end dan inline `<script>` WhatsApp di `footer.php`; versi OwlCarousel CSS (`2.2.1`) dan JS (`2.3.4`) tidak seragam; versi enqueue masih hardcoded `'4.1.1'`.
- **Rencana Fix (Fase 06):** Konsolidasikan ke 1 file `js/makhadane.js` dengan konstanta `MAKHADANE_VERSION` dan conditional enqueue.

### GAP-20 — 🔵 Low: Arsitektur CSS Masih Berbasis SCSS Manual & Belum 12 Bagian + `FontAne` (256 KB)
- **Lokasi:** `scss/`, `css/main.css`, `css/FontAne.css`, `fonts/FontAne.*`
- **Masalah:** Memuat file font eksternal `FontAne` (256 KB) dan CSS belum terstruktur dalam 12 bagian (`[01]`–`[12]`) yang diaudit oleh `composer css:check` (`0 dead CSS rules`).
- **Rencana Fix (Fase 07–09):** Konsolidasi CSS ke 12 bagian (`css/main.css` → `css/main.min.css`), migrasi `FontAne` ke inline SVG `makhadane_icon()`, dan hapus folder `fonts/` serta `scss/`.

### GAP-21 — 🔵 Low: Definisi Field ACF Belum Disimpan di Repo (`inc/acf-json/`)
- **Lokasi:** Belum ada `inc/acf-json/` dan `inc/acf-importer.php`.
- **Masalah:** Struktur field group ACF hanya ada di database situs pelanggan.
- **Rencana Fix (Fase 10):** Bundel `inc/acf-json/field-groups.json` dan `inc/acf-importer.php`.

### GAP-22 — 🔵 Low: Format Kode Belum Sesuai WPCS (3.485 Error & 423 Warning)
- **Lokasi:** 94 file PHP.
- **Rencana Fix (Fase 04):** Jalankan `composer lint:fix` (`phpcbf`) dan rapikan sisa pelanggaran WPCS.
