# Architecture Overview — Makhad Ane

> Dokumen ini ditulis dari hasil audit kode aktual (`v4.1.3`, commit baseline
> `517b583` / `fe60544`). Klaim di sini mencerminkan apa yang **benar-benar
> berjalan di kode saat ini**, termasuk duplikasi modul, warisan dari
> `elemenane`/`sekolahane`, dan bagian yang belum sesuai standar.

## 1. Gambaran Sistem
**Makhad Ane** adalah classic WordPress theme (non-block, PHP prosedural) untuk
situs pesantren, ma'had, madrasah, dan sekolah Islam milik pelanggan Webane
Indonesia.

- **Versi saat ini:** `4.1.3`
- **Target environment:** WordPress 6.0+, PHP 8.0+
- **Hard dependency:** **ACF Pro** (Advanced Custom Fields Pro — repeater,
  flexible content, dan options page dipakai secara ekstensif di seluruh
  template).
- **Pendekatan tampilan:** Satu instalasi melayani tampilan desktop dan mobile
  secara terpisah di beberapa halaman dengan deteksi server-side menggunakan
  fungsi bawaan WordPress `wp_is_mobile()`.

---

## 2. Siklus Muat PHP (`functions.php` & `inc/`)

[functions.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/functions.php) menjalankan:
1. Pengecekan versi WordPress (`< 4.7` memanggil `inc/back-compat.php` — file tersebut tidak ada).
2. Hook `after_setup_theme` → `ane_theme_setup()` & `makhadane_load_theme_textdomain()`:
   - `load_theme_textdomain( 'makhadane', get_template_directory() . '/languages' )`
   - Theme supports: `title-tag`, `automatic-feed-links`, `post-thumbnails`,
     `post-formats` (`video`, `gallery`), `html5`, `custom-logo` (400×97),
     `yoast-seo-breadcrumbs`.
   - Image sizes kustom: `kotak` (400×400), `persegi` (800×1000), `backend-persegi` (100×125), `backend-kotak` (100×100), `backend-default` (100×56), `backend-banner` (200×44).
   - ⚠️ Menjalankan 9× `update_option()` untuk ukuran `medium`, `large`, dan `thumbnail` di setiap request (`GAP-12`).
   - Menu locations: `menuutama` ("Menu Utama"), `menufooter` ("Menu Footer").
3. Hook `widgets_init` → `ane_widgets()`: mendaftarkan 2 sidebar (`default-sidebar`, `blog-sidebar`).
4. Filter penghapus query string versi WP pada aset & meta generator.
5. Auto-load seluruh file PHP di `inc/*.php` (12 file) dan `inc/**/*.php` (`inc/admin/*.php`, 13 file) menggunakan `glob()` ([functions.php:154-161](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/functions.php#L154-L161)).

### 2.1 File di `inc/` (12 File)
| File | Tanggung Jawab Aktual |
|---|---|
| [inc/makhadane.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/makhadane.php) | Helper URL aset, dynamic PWA Web App Manifest (`?ane_manifest=1`), enqueue CSS/JS front-end, excerpt, popup gallery (`the_content`), shortcode caption, helper embed & YouTube thumbnail, pagination, related & newest posts, prev/next post, `ane_time_ago()`, tombol social share, sosmed/about/contact ACF helper, marquee news (transient 1 menit), author bio box (`the_content`), breadcrumbs, attribution Yakaafi & Webane, copyright year (`$wpdb`), custom avatar (`gravatar_ane`), filter `upload_mimes`, penghitung post views (`musi_views` + AJAX `wp_ajax_postviews`) & kolom admin views. |
| [inc/custompost.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/custompost.php) | Mendaftarkan 4 Custom Post Type (`ustadz`, `ekstrakurikuler`, `testimoni`, `faq`), 2 Custom Taxonomy untuk `ustadz` (`pelajaran`, `kelas`), menyertakan CPT ke RSS feed, kustomisasi kolom judul admin CPT, dan fungsi `ane_tampilkan_ustadz_terkait()`. |
| [inc/acf.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/acf.php) | Deregister style ACF di front-end, set Google Maps API key (hardcoded — `GAP-01`) & enqueue script Google Maps di seluruh halaman, serta injeksi variabel warna CSS `:root` (`--ane-warna-*`) di `wp_head`. |
| [inc/admin.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/admin.php) | Pendaftaran halaman ACF Options (`ane-setup`, `ane-general-setting`, `ane-customer-care`), halaman panduan `ane-seo-news`, enqueue global `css/admin.min.css`, hero card admin, dan logo admin bar mobile. |
| [inc/images.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/images.php) | Helper gambar unggulan (`ane_get_featured_image`, `ane_get_featured_image_overlay`, `ane_get_featured_image_kotak`), ekstraksi gambar pertama dari konten via `DOMDocument`, dan dummy thumbnail. |
| [inc/media.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/media.php) | Dukungan upload MIME tambahan (SVG, WebP, AVIF, ICO), sanitasi dasar SVG via regex (`GAP-04`), perbaikan preview SVG di Media Library (`GAP-08`), dan kolom Format di Media Library. |
| [inc/post.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/post.php) | Hierarki heading judul (`ane_get_title()`), waktu relatif (`ane_load_times_ago()`), daftar kategori (`ane_display_post_categories()`), estimasi waktu baca (`ane_reading_time()`), meta single post (`ane_single_meta()`), dan `update_option( 'sticky_posts', array() )` di setiap `init` (`GAP-13`). |
| [inc/seo.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/seo.php) | Mesin SEO & Schema JSON-LD (Organization/EducationalOrganization, WebSite + SearchAction, SiteNavigationElement, NewsArticle, BreadcrumbList, CollectionPage, FAQPage, Ustadz/Person), Open Graph & Twitter Card (dengan deteksi Yoast SEO), Dublin Core & Citation meta, resource hints, dan Google News Sitemap (`/news-sitemap.xml`). |
| [inc/tracking-scripts.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/tracking-scripts.php) | Injeksi script pihak ketiga dari ACF Options (`ane_ga_header`, `ane_sc_header`, `ane_metapixel_header`, `ane_metasdk_body`, `ane_ga_footer`). |
| [inc/updater.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/updater.php) | Class `Makhadane_Updater` untuk cek & instalasi update dari GitHub Release `webaneid/makhadane` beserta handler `ane_force_check` / `ane_debug` (`GAP-05`). |
| [inc/widgets.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/widgets.php) | Class `Ane_Posts_Widget` (`ane-post-widget`). |
| [inc/handle.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/handle.php) | Menonaktifkan komentar di admin & me-redirect post yang memiliki komentar terbuka ke homepage (`GAP-10`), serta dequeue `jquery-ui-core`. |

### 2.2 File di `inc/admin/` (13 File — Hasil Porting dari `elemenane`)
| File | Tanggung Jawab Aktual |
|---|---|
| `inc/admin/acf-admin.php` | Mendaftarkan field group lokal `group_page_schema` (`ane_page_schema_type`) untuk `page`. |
| `inc/admin/content.php` | Helper HTML toggle switch `ane_toggle_switch()`. |
| `inc/admin/customizer.php` | Custom styling halaman `wp-login.php` (`css/admin.min.css`). |
| `inc/admin/dashboard.php` | Halaman custom dashboard `ane-dashboard` (statistik views, post, komentar, penulis, dan Chart.js) yang me-redirect `index.php` wp-admin. |
| `inc/admin/design-tokens.php` | Injeksi CSS variables `--ane-*` di `admin_head` dan enqueue `js/admin-animations.js`. |
| `inc/admin/facebook-comments.php` | SDK Facebook Comments, field group lokal `group_ane_facebook_comments` (masih ber-text domain `'elemenane'`), dan `ane_load_facebook_comment()`. |
| `inc/admin/footer-mobile-menu.php` | Bottom navigation bar di wp-admin mobile (`≤782px`). |
| `inc/admin/header.php` | Enqueue `js/admin-header.js` dan kustomisasi admin bar. |
| `inc/admin/linktree-core.php` | Pembuatan tabel kustom `{$wpdb->prefix}ane_linktree_clicks` (di `admin_init` & `after_switch_theme`), endpoint AJAX `ane_track_linktree_click`, dan enqueue `js/linktree-tracking.js`. |
| `inc/admin/linktree-analytics.php` | Submenu `ane-linktree-analytics` di bawah `ane-setup` untuk laporan klik Linktree. |
| `inc/admin/menu.php` | Enqueue `js/admin-menu.js` dan urutan menu admin. |
| `inc/admin/navigation.php` | Helper breadcrumb admin. |
| `inc/admin/user.php` | Enqueue `js/admin-user.js` + Chart.js di halaman profil user. |

---

## 3. Kontrak Data (Wajib Kompatibel Mundur)

> Semua slug/key di bawah ini sudah tersimpan di database situs pelanggan.
> **DILARANG mengubah nama/slug ini tanpa skrip migrasi.**

### 3.1 Custom Post Types ([inc/custompost.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/custompost.php))
| Slug CPT | Supports | Capability Type | Archive & REST |
|---|---|---|---|
| `ustadz` | `title`, `editor`, `thumbnail` | `post` | `has_archive: true`, `show_in_rest: true` |
| `ekstrakurikuler` | `title`, `editor`, `thumbnail` | `post` | `has_archive: true`, `show_in_rest: true` |
| `testimoni` | `title`, `thumbnail` | `post` | `has_archive: true`, `show_in_rest: true` |
| `faq` | `title`, `editor` | `post` | `has_archive: true`, `show_in_rest: true` |

### 3.2 Custom Taxonomies ([inc/custompost.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/inc/custompost.php))
| Slug Taxonomy | Post Type | Rewrite Slug | Catatan Penting |
|---|---|---|---|
| `pelajaran` | `ustadz` | `pelajaran` | Hierarchical |
| `kelas` | `ustadz` | `kelas` | ⚠️ Di `inc/custompost.php:112` terdaftar lowercase `'kelas'`, namun file template bernama `taxonomy-Kelas.php` (K kapital) dan `tp/single-ustadz.php` memanggil `'Kelas'` (`GAP-11`). |

### 3.3 Post Meta, User Meta, & Tabel Kustom
- Post meta `musi_views` — jumlah pembaca post (`ane_get_views()` / `ane_set_views()`).
- User meta `gravatar_ane` — ID attachment untuk avatar lokal user.
- Tabel kustom `{$wpdb->prefix}ane_linktree_clicks` & option `ane_linktree_db_version`.

### 3.4 ACF Options Pages & Field Utama
- Menu utama ACF (`inc/admin.php`): `ane-setup` dengan sub-page `ane-general-setting` dan `ane-customer-care` (serta submenu non-ACF `ane-seo-news` dan `ane-linktree-analytics`).
- **Options (`'option'`):**
  - Identitas & Kontak: `about_ane` (`company_name`, `company_sologan`, `company_about`), `contact_ane` (`kontak_alamat`, `kontak_kabupaten`, `kontak_provinsi`, `kontak_kodepos`, `kontak_telepon`, `kontak_handphone`, `kontak_email`, `kontak_website`), `sosmed_ane` (`sosmed_wa`, `sosmed_fb`, `sosmed_tw`, `sosmed_youtube`, `sosmed_ig`, `sosmed_tiktok`), `ane_gmap`, `ane_kontak_Waktu`, `cf7_id`.
  - Header & Navigasi Mobile: `ane_display_topheader`, `ane_link_top`, `ane_mobile_menu`, `ane_list_lembaga`, `ane_menu_kategori`.
  - Customer Care: `ane_cs_aktif`, `ane_cs_label`, `ane_cs_welcome`, repeater `ane_cs` (`ane_whatsapp`, `ane_image`, `ane_nama`, `ane_area`).
  - Warna (`--ane-warna-*`): `ane-warna-text`, `ane-warna-gelap`, `ane-warna-utama`, `ane-warna-terang`, `ane-warna-alternatif`, `ane-warna-utama-2`, `ane-warna-putih`.
  - Tracking & Komentar: `ane_ga_header`, `ane_sc_header`, `ane_metapixel_header`, `ane_metasdk_body`, `ane_ga_footer`, `ane_facebook_comments_enable`, `ane_facebook_app_id`, `ane_facebook_comments_locale`, `ane_facebook_comments_num_posts`.
  - Gambar Header Arsip: `ane_image_arsip`, `ane_image_arsip_ustadz`, `ane_image_arsip_ekstra`, `ane_image_arsip_testimoni`, `ane_image_arsip_faq`, `ane_image_author`.
  - Linktree: `ane_linktree_general`, `ane_linktree_custom_links`, `ane_linktree_Select_links`.
- **Flexible Content Halaman (`home_content`, `blog_content`):**
  - Layout section di `tp/section/*.php` (`big-slider`, `featured`, `about-us`, `angka`, `calltoaction`, `testimoni`, `ekstra`, `faq`, `guru`, `banner`, `content`, `image-below-text`, `image-side-listing`, `image-side-text`) dan `tp/news/*.php` (`default`, `sliding`, `column`, `classic`, `featured`, `home-gallery`, `home-news`).

---

## 4. Struktur Template & Routing

### 4.1 Custom Page Templates (Root)
| File | Template Name | Routing Mobile vs Desktop |
|---|---|---|
| [page-home.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/page-home.php) | `Landing Page` | `wp_is_mobile()` → `tp/pages/home-mobile.php` atau `tp/pages/home-desktop.php` |
| [page-newslist.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/page-newslist.php) | `Blog Type` | `wp_is_mobile()` → `tp/pages/news-mobile.php` atau `tp/pages/news-desktop.php` |
| [page-aneprofil.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/page-aneprofil.php) | `Profil Lembaga` | `tp/pages/profil.php` |
| [page-kelembagaan.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/page-kelembagaan.php) | `Kelembagaan` | `tp/pages/kelembagaan.php` |
| [page-kontak.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/page-kontak.php) | `Contact Us` | `tp/content-page-kontak.php` |
| [page-mcategory.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/page-mcategory.php) | `Mobile Category` | `tp/mobile/kategori.php` |
| [page-linktree.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/page-linktree.php) | `Linktree Landing Page` | `tp/content-linktree.php` |

### 4.2 Archive, Single, & Hierarchy Standar
- **Single:** [single.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/single.php), [single-ustadz.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/single-ustadz.php), [single-ekstrakurikuler.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/single-ekstrakurikuler.php). (Catatan: `tp/single.php` dan `tp/single-ustadz.php` tidak pernah dipanggil — `GAP-16`).
- **Archive CPT & Taxonomy:** [archive-ustadz.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/archive-ustadz.php), [archive-ekstrakurikuler.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/archive-ekstrakurikuler.php), [archive-testimoni.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/archive-testimoni.php), [archive-faq.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/archive-faq.php), [taxonomy-pelajaran.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/taxonomy-pelajaran.php), [taxonomy-Kelas.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/taxonomy-Kelas.php).
- **Archive Blog:** [index.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/index.php), [archive.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/archive.php), [category.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/category.php), [tag.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/tag.php), [author.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/author.php), [search.php](file:///Users/webane/sites/makhadane/wp-content/themes/makhadane/search.php).

---

## 5. Aset Front-End & Admin (`css/`, `js/`, `scss/`, `fonts/`)
- **CSS Front-End (`ane_load_css_and_js`):** `css/FontAne.css` (+ `fonts/FontAne.*` 256 KB), Google Fonts `Poppins`, Magnific Popup 1.1.0 (CDN, dimuat di semua halaman), OwlCarousel 2.2.1 CSS (CDN), dan `css/main.min.css` (versi hardcoded `'4.1.1'`).
- **JS Front-End:** OwlCarousel 2.3.4 JS (CDN — versi tidak sinkron dengan CSS 2.2.1), Magnific Popup 1.1.0 JS (CDN), `js/makhadane.js`, `js/postviews-cache.js` (single post), `js/linktree-tracking.js` (halaman linktree), serta Google Maps JS API (dimuat di semua halaman dari `inc/acf.php`).
- **CSS & JS Admin:** `css/admin.min.css` (121 KB), `js/admin-animations.js`, `js/admin-dashboard.js`, `js/admin-header.js`, `js/admin-linktree-analytics.js`, `js/admin-menu.js`, `js/admin-user.js`.
