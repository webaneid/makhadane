# Fase 03 — Security Fix Critical/High, Sanitasi Judul Anti-HTML/Word, PHPStan 0 Error, & Hapus DB Write Per-Request

- **Status:** Done
- **Prasyarat:** Fase 02 (Auto-Updater, CI, & Release Workflow)
- **Branch:** `main`

## Tujuan

1. **Sanitasi Judul Post/Page/CPT Anti-HTML & Anti-Microsoft Word (`inc/post.php` & Template):**
   - Menambahkan fungsi `makhadane_clean_title_string()` yang membersihkan tag HTML, komentar kondisional MS Word (`<!--[if ...]-->`), blok `<style>`/`<script>`, inline style `mso-*`, entitas `&nbsp;` / non-breaking space UTF-8, dan zero-width characters.
   - Memasang filter pada `title_save_pre` (saat judul disimpan ke DB) dan `the_title` prioritas `5` (saat judul ditampilkan, untuk membersihkan post lama yang sudah terlanjur menyimpan HTML/style Word di DB).
   - Mengganti pemanggilan `the_title()` tanpa escape di template dengan `esc_html( get_the_title() )` (dan `esc_attr( get_the_title() )` pada atribut `alt`).
2. **Menutup Celah Keamanan Critical & High (`GAP-01` s/d `GAP-04`, `GAP-06`):**
   - `GAP-01`: Hapus hardcoded Google Maps API key di `inc/acf.php`, ganti dengan `makhadane_get_google_maps_api_key()` (konstanta `MAKHADANE_GMAPS_API_KEY` → ACF option `ane_google_maps_api_key` → fallback XOR-Hex), tambahkan tab **API Keys** di `ane-general-setting`, dan enqueue Google Maps hanya di halaman kontak (`page-kontak.php`).
   - `GAP-02`: Hapus `remove_filter( 'pre_user_description', 'wp_filter_kses' )` di `inc/makhadane.php`, validasi warna hex di `inc/acf.php` dengan `sanitize_hex_color()`, dan escape output di template.
   - `GAP-03`: Tambahkan guard `defined( 'ABSPATH' ) || exit;` di 10 file PHP yang belum memilikinya hingga `composer security:scan` lolos 100%.
   - `GAP-04`: Hapus fungsi duplikat `ane_allowed_mime_types()` di `inc/makhadane.php` (yang merusak upload PDF/DOCX/MP4/ZIP), batasi upload SVG hanya untuk administrator (`current_user_can( 'manage_options' )`), dan perketat sanitasi XML/SVG di `inc/media.php`.
   - `GAP-06`: Validasi `wp_unslash()`, `absint()`, `'publish' === get_post_status( $post_id )`, dan sanitasi `$_SERVER['REMOTE_ADDR']` pada endpoint post views di `inc/makhadane.php`.
3. **Perbaikan Seluruh 13 Error PHPStan hingga Baseline `0` (`GAP-07`, `GAP-08`):**
   - Perbaiki seluruh 13 error di `inc/media.php`, `inc/custompost.php`, `inc/makhadane.php`, `inc/post.php`, `inc/seo.php`, `inc/widgets.php`, `single.php`, dan `tp/single.php`, lalu kosongkan `phpstan-baseline.neon`.
4. **Perbaikan Bug Fungsional & Penghapusan DB Write Per-Request (`GAP-09`, `GAP-10`, `GAP-13`, `GAP-14`, `GAP-15`):**
   - `GAP-09`: Perbaiki pemanggilan `ane_get_copyright_year()` dan `ane_get_designed_by()` di `footer.php`.
   - `GAP-10`: Hapus redirect 301 berbahaya ke homepage di `inc/handle.php`.
   - `GAP-13`: Hapus `update_option( 'sticky_posts', array() )` pada hook `init` di `inc/post.php`.
   - `GAP-14`: Tambahkan transient cache `makhadane_copyright_year` pada query `$wpdb` di `ane_get_copyright_year()`.
   - `GAP-15`: Perbaiki pengecekan `$hook` admin di `inc/admin/linktree-analytics.php`.

## File yang Disentuh

- `inc/acf.php`
- `inc/post.php`
- `inc/media.php`
- `inc/makhadane.php`
- `inc/custompost.php`
- `inc/widgets.php`
- `inc/seo.php`
- `inc/handle.php`
- `inc/images.php`
- `inc/admin.php`
- `inc/admin/user.php`
- `inc/admin/linktree-analytics.php`
- `single.php`
- `single-ustadz.php`
- `single-ekstrakurikuler.php`
- `archive-faq.php`
- `footer.php`
- `page-kontak.php`
- `page-linktree.php`
- `tp/single.php`
- `tp/single-ustadz.php`
- `tp/content-sliders.php`
- `tp/content-page-kontak.php`
- `tp/content-testimoni.php`
- `tp/pages/profil.php`
- `tp/pages/kelembagaan.php`
- `tp/section/faq.php`
- `phpstan-baseline.neon`
- `docs/lessons-learned.md`
- `docs/RETROFIT-GAPS.md`
- `docs/PROGRESS.md`

## Langkah Detail

- [x] 1. Implementasikan sanitasi judul anti-HTML & anti-MS Word di `inc/post.php` (`title_save_pre` & `the_title`), hapus `update_option( 'sticky_posts' )` di `init`, dan perbaiki `$category->name`.
- [x] 2. Amankan `inc/acf.php` (`GAP-01`, `GAP-02`, `GAP-03`): hapus hardcoded API key, tambah helper `makhadane_get_google_maps_api_key()` & tab ACF API Keys, batasi enqueue Google Maps di halaman kontak, dan validasi warna hex.
- [x] 3. Amankan `inc/media.php` (`GAP-04`, `GAP-07`): batasi SVG untuk admin, perketat sanitasi SVG anti-XSS/XXE, dan perbaiki 3 error PHPStan pada filter thumbnail SVG.
- [x] 4. Perbaiki `inc/makhadane.php` (`GAP-02`, `GAP-04`, `GAP-06`, `GAP-08`, `GAP-14`): hapus `remove_filter( 'pre_user_description' )`, hapus `ane_allowed_mime_types()`, ubah `add_action( 'the_content' )` ke `add_filter`, perketat AJAX post views, escape kolom views admin, dan cache `ane_get_copyright_year()`.
- [x] 5. Tambahkan guard `ABSPATH` dan perbaiki bug/escaping di `inc/custompost.php`, `inc/widgets.php`, `inc/seo.php`, `inc/handle.php`, `inc/images.php`, `inc/admin.php`, `inc/admin/user.php`, `inc/admin/linktree-analytics.php`, serta template front-end.
- [x] 6. Kosongkan `phpstan-baseline.neon` dan jalankan `composer security:scan`, `composer lint:syntax`, `composer analyse`, serta `composer dist:check`.

## Kriteria Selesai (Acceptance Criteria)

- [x] `composer security:scan` lolos 100% (0 hardcoded secret, 0 file PHP tanpa `ABSPATH`, 0 fungsi webshell).
- [x] `composer analyse` lolos 0 error dengan `phpstan-baseline.neon` kosong (`ignoreErrors: []`).
- [x] `composer lint:syntax` lolos 0 error.
- [x] `composer dist:check` lolos.
- [x] Judul yang di-copy-paste dari Microsoft Word atau mengandung HTML otomatis dibersihkan saat disimpan maupun saat ditampilkan.

## Security Review (Step 4 SOP)

| # | Area | Status | Catatan |
|---|---|---|---|
| 1 | Sanitasi Judul Anti-HTML / MS Word | **PASS** | `makhadane_clean_title_string()` di `inc/post.php` menghapus komentar kondisional MS Word, `<style>`, `<script>`, `<xml>`, `<o:p>`, seluruh tag HTML (`wp_strip_all_tags`), `&nbsp;`, UTF-8 non-breaking spaces, dan zero-width spaces pada saat simpan (`title_save_pre`) maupun tampil (`the_title` prioritas 5). |
| 2 | Output Escaping (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`) | **PASS** | `remove_filter( 'pre_user_description', 'wp_filter_kses' )` dihapus; warna CSS di `inc/acf.php` divalidasi dengan `sanitize_hex_color()`; pemanggilan judul dan atribut `alt` di template menggunakan `esc_html( get_the_title() )` / `esc_attr( get_the_title() )`. |
| 3 | Input Sanitization & Capability Check | **PASS** | Upload SVG dibatasi hanya untuk administrator (`current_user_can( 'manage_options' )`) dan disanitasi terhadap XXE/XSS. Endpoint `postviews` memvalidasi `absint( wp_unslash() )`, `'publish' === get_post_status()`, dan `sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) )`. |
| 4 | Database / Performa | **PASS** | Penulisan `update_option( 'sticky_posts', array() )` pada setiap `init` dihapus (`GAP-13`); query `$wpdb` tahun copyright di-cache menggunakan transient `makhadane_copyright_year` (`GAP-14`). |
| 5 | Anti-Webshell & Secrets (`composer security:scan`) | **PASS** | 102/102 file PHP memiliki guard `defined( 'ABSPATH' ) || exit;`, hardcoded Google Maps API key dihapus (`GAP-01`), dan script Google Maps hanya dimuat di halaman kontak. |
