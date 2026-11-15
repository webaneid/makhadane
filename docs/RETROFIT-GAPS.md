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
| 🟡 **Medium** | 10 | 🔄 Sebagian (7/10) | Fase 02 (`GAP-12` ✅), Fase 03 (`GAP-06`, `GAP-08`, `GAP-09`, `GAP-10`, `GAP-13`, `GAP-14` ✅) & Fase 04 |
| 🔵 **Low / Clean-up** | 6 | 🔄 Sebagian (1/6) | Fase 02 (`GAP-17` ✅) & Fase 04 – Fase 10 |

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

### GAP-11 — 🟡 Medium: Inkonsistensi Case Slug Taxonomy `'kelas'` vs `'Kelas'`
- **Lokasi:** [inc/custompost.php:112](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/custompost.php#L112) (`'kelas'`), [single-ustadz.php:27,99](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/single-ustadz.php#L27) (`'kelas'`), [tp/single-ustadz.php:20,85](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/tp/single-ustadz.php#L20) (`'Kelas'`), dan nama file [taxonomy-Kelas.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/taxonomy-Kelas.php).
- **Masalah:** Di server Linux (case-sensitive), template `taxonomy-Kelas.php` tidak akan terpanggil untuk taxonomy `'kelas'`.
- **Rencana Fix (Fase 04):** Pastikan kompatibilitas template taxonomy untuk `'kelas'` / `'Kelas'`.

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
