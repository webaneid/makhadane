# Fase 11 — Penerapan Standar `webane-pagespeed-cwv`

- **Status:** `Done`
- **Prasyarat:** Fase 10 (`Done`)
- **Standar Acuan:** Skill `webane-pagespeed-cwv`

## Tujuan
Menerapkan seluruh standar optimasi PageSpeed Insights & Core Web Vitals (`LCP`, `FCP`, `CLS`, `INP`) serta checklist aksesibilitas (`100`) pada theme `makhadane`.

## Perubahan yang Dilakukan
1. **Gambar Hero, Overlay, & CTA (`<img>` + DPR-Aware `sizes` + `fetchpriority`):**
   - Menambahkan helper ber-cache `makhadane_get_attachment_id_from_url()` di `inc/makhadane.php`.
   - `tp/section/big-slider.php`: Mengganti `style="background-image:url(...)"` dengan `<img class="home-slider-bg">` (`sizes="(max-width: 767px) 240px, 100vw"`), `fetchpriority="high" decoding="async"` khusus slide ke-1 (LCP) dan `loading="lazy" fetchpriority="low" decoding="async"` untuk slide ke-2 dst., serta memperbaiki markup tombol `<a>` (menghapus `<button>` bersarang di dalam `<a>`).
   - `tp/section/calltoaction.php`: Mengganti inline `background-image` dengan `<img class="ane-cta-bg">` (`sizes="(max-width: 767px) 400px, 100vw"`, `loading="lazy"`, `fetchpriority="low"`, `decoding="async"`).
   - `inc/images.php` & `tp/content-ekstrakurikuler.php`: Mengganti inline `background-image` di `ane_get_featured_image_overlay()` dengan `<img class="ane-overlay-bg">` (`sizes="(max-width: 767px) 400px, 700px"`) dan `<a class="ane-overlay-permalink" aria-label="...">`, melengkapi `width`/`height`/`srcset`/`sizes` + `fetchpriority="high"` pada `ane_get_featured_image()` di halaman single, serta menggunakan ukuran `'thumbnail'` (`400x225`, `sizes="(max-width: 767px) 86px, 98px"`) pada `ane_get_featured_image_kotak()`.
2. **Preload LCP (`wp_head` Prioritas `1`) & Resource Hints Terpusat:**
   - Menghapus preload statis `$logoimg` di `header.php` yang tidak sesuai elemen LCP.
   - Menambahkan `makhadane_preload_lcp_image()` pada `wp_head` prioritas `1` dengan `imagesrcset` dan `imagesizes` yang identik dengan tag `<img>` LCP di `<body>` (baik untuk beranda/landing page maupun single post/CPT).
   - Menambahkan `makhadane_resource_hints()` pada `wp_resource_hints` (maksimal 2 `preconnect` ke Google Fonts + `dns-prefetch` ke `cdnjs.cloudflare.com`).
3. **Eliminasi Render-Blocking CSS/JS & Zero-CLS Slider:**
   - Menghapus request eksternal `owl.carousel.min.css` dan menggabungkan CSS struktural `.owl-carousel` beserta aturan pre-init anti-CLS (`.home-big-slider.owl-carousel { display: block; min-height: 85vh; }` & `:not(.owl-loaded) > *:not(:first-child) { display: none; }`) ke `css/main.css`.
   - Menambahkan `makhadane_async_non_critical_styles()` (`style_loader_tag`) untuk memuat `ane-google-fonts` dan `magnific-popup` secara non-blocking (`media="print" onload="this.media='all'"`).
   - Menambahkan `makhadane_defer_frontend_scripts()` (`script_loader_tag`) untuk men-`defer` seluruh script frontend (`jquery-core`, `jquery-migrate`, `owl-carousel`, `magnific-popup`, `ane-main-script`).
4. **`content-visibility: auto`, Passive Scroll Listener, & Aksesibilitas (`100`):**
   - Menambahkan `content-visibility: auto; contain-intrinsic-size: auto 500px;` pada seksi bawah lipatan layar (`.home-default, .home-cta, .ane-footer`).
   - Mengoptimalkan listener scroll sticky sidebar di `js/makhadane.js` menggunakan `{ passive: true }` dan `requestAnimationFrame`.
   - Menambahkan `<h2 class="screen-reader-text">Informasi Situs</h2>` di awal `footer.php` dan aturan global `.screen-reader-text` serta target sentuh minimal `24px` pada `.ane-meta ul li a` di `css/main.css`.

## Verifikasi & Cek Kualitas
- `composer css:check` → Lolos (`6751 baris -> css/main.min.css (126.4 KB), 0 dead CSS rules`).
- `composer lint:changed` → Lolos (`php -l` & PHPCS 0 error).
- `composer analyse` → Lolos (PHPStan 0 error).
- `composer security:scan` → Lolos.
- `composer dist:check` → Lolos.
