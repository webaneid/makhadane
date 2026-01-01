# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

This is a custom WordPress theme called "Makhad Ane" (v4.1.1) developed by Webane Indonesia for Islamic schools, madrasahs, and pesantren (Islamic boarding schools). The theme is designed for specific clients and not distributed publicly.

**Text Domain:** `makhadane`
**WordPress Requirement:** 4.7+

## Development Workflow

### SCSS Compilation

The theme uses SCSS for styling. All SCSS files are located in [scss/](scss/) directory:

- Main entry point: [scss/main.scss](scss/main.scss)
- Admin styles: [scss/admin.scss](scss/admin.scss)
- Compiled CSS outputs to [css/](css/) directory

**To compile SCSS:**
```bash
# Install Sass if not available (requires npm/node)
npm install -g sass

# Compile main styles
sass scss/main.scss css/main.css --style compressed

# Compile admin styles
sass scss/admin.scss css/admin.css --style compressed

# Watch for changes during development
sass --watch scss/main.scss:css/main.css --style compressed
sass --watch scss/admin.scss:css/admin.css --style compressed
```

**SCSS Architecture:**
- [scss/_resets.scss](scss/_resets.scss) - CSS resets
- [scss/_kolom.scss](scss/_kolom.scss) - Grid/column system
- [scss/_header.scss](scss/_header.scss) - Header styles
- [scss/_landingpage.scss](scss/_landingpage.scss) - Landing page layouts
- [scss/_footer.scss](scss/_footer.scss) - Footer styles
- [scss/_button.scss](scss/_button.scss) - Button components
- Additional partials for specific features (fasilitas, guru, faq, etc.)

## Architecture & Code Structure

### Template Hierarchy

**Main Templates:**
- [index.php](index.php) - Default blog index
- [single.php](single.php) - Single post display
- [page.php](page.php) - Default page template
- [archive.php](archive.php) - Generic archive
- [search.php](search.php) - Search results
- [header.php](header.php) - Header template (includes ACF-based color system)
- [footer.php](footer.php) - Footer template

**Custom Page Templates:**
- [page-home.php](page-home.php) - Landing page template (Template Name: "Landing Page")
  - Detects mobile/desktop via `wp_is_mobile()`
  - Loads device-specific templates from `tp/pages/`
- [page-aneprofil.php](page-aneprofil.php) - Profile page
- [page-kelembagaan.php](page-kelembagaan.php) - Institutional page
- [page-kontak.php](page-kontak.php) - Contact page
- [page-newslist.php](page-newslist.php) - News listing
- [page-mcategory.php](page-mcategory.php) - Mobile category page

**Custom Post Type Templates:**
- [single-ustadz.php](single-ustadz.php) - Teacher/instructor single view
- [archive-ustadz.php](archive-ustadz.php) - Teachers archive
- [single-ekstrakurikuler.php](single-ekstrakurikuler.php) - Extracurricular single view
- [archive-ekstrakurikuler.php](archive-ekstrakurikuler.php) - Extracurricular archive
- [archive-testimoni.php](archive-testimoni.php) - Testimonials archive
- [archive-faq.php](archive-faq.php) - FAQ archive

**Taxonomy Templates:**
- [taxonomy-pelajaran.php](taxonomy-pelajaran.php) - Subject/course taxonomy
- [taxonomy-Kelas.php](taxonomy-Kelas.php) - Class/grade taxonomy (note: capitalized)

### Template Parts System

Template parts are located in [tp/](tp/) directory and loaded via `get_template_part()`:

**Content Templates:**
- [tp/content.php](tp/content.php) - Default post content
- [tp/content-archive.php](tp/content-archive.php) - Archive item layout
- [tp/content-ustadz.php](tp/content-ustadz.php) - Teacher card/listing
- [tp/content-ekstrakurikuler.php](tp/content-ekstrakurikuler.php) - Extracurricular item
- [tp/content-testimoni.php](tp/content-testimoni.php) - Testimonial item
- [tp/content-page.php](tp/content-page.php) - Page content wrapper
- [tp/content-page-kontak.php](tp/content-page-kontak.php) - Contact page content
- [tp/content-sosmed.php](tp/content-sosmed.php) - Social media links
- [tp/content-sliders.php](tp/content-sliders.php) - Slider components
- [tp/content-klasik.php](tp/content-klasik.php) - Classic layout
- [tp/content-overlay.php](tp/content-overlay.php) - Overlay layout
- [tp/content-list.php](tp/content-list.php) - List view
- [tp/content-list-title.php](tp/content-list-title.php) - List with title only

**Special Templates:**
- [tp/home-categories.php](tp/home-categories.php) - Homepage category display
- [tp/single.php](tp/single.php) - Single post template part
- [tp/single-ustadz.php](tp/single-ustadz.php) - Teacher single template part
- Subdirectories: `tp/mobile/` and `tp/news/` for specific contexts

### Functionality Modules

All functionality is modularized in [inc/](inc/) directory. Files are auto-loaded via glob pattern in [functions.php:109-123](functions.php#L109-L123):

**Core Files:**
- [inc/makhadane.php](inc/makhadane.php) - Main theme functions (enqueue scripts/styles, excerpts, galleries, CDN settings)
- [inc/custompost.php](inc/custompost.php) - Custom post types and taxonomies registration
- [inc/acf.php](inc/acf.php) - Advanced Custom Fields configuration
- [inc/admin.php](inc/admin.php) - WordPress admin customizations
- [inc/widgets.php](inc/widgets.php) - Widget registration and management
- [inc/post.php](inc/post.php) - Post-related functionality
- [inc/images.php](inc/images.php) - Image handling and processing

### Custom Post Types

Defined in [inc/custompost.php](inc/custompost.php):

1. **ustadz** (Teachers/Instructors)
   - Supports: title, editor, thumbnail
   - Icon: dashicons-businessman
   - Taxonomies: pelajaran (subjects), kelas (classes)

2. **ekstrakurikuler** (Extracurricular Activities)
   - Supports: title, editor, thumbnail
   - Icon: dashicons-groups

3. **testimoni** (Testimonials)
   - Supports: title, thumbnail
   - Icon: dashicons-admin-comments

4. **faq** (Frequently Asked Questions)
   - Supports: title, editor
   - Icon: dashicons-editor-help
   - Custom column: "Pertanyaan" (Question) instead of "Title"

**Related Function:** `ane_tampilkan_ustadz_terkait($taxonomy)` in [inc/custompost.php:189-224](inc/custompost.php#L189-L224) displays related teachers based on taxonomy terms.

### Advanced Custom Fields (ACF)

ACF is heavily integrated. Key configuration in [inc/acf.php](inc/acf.php):

**ACF Options Pages:**
- "Webane Settings" (main menu: `webane-theme-general-settings`)
  - Sub-page: "Your Banners" - Banner management
  - Sub-page: "Customer Care" - Contact information
  - Sub-page: "Mobile Menu" - Mobile navigation
  - Sub-page: "Warna" - Color customization
  - Sub-page: "Script" - Custom scripts (GA, Meta Pixel, etc.)

**Google Maps Integration:**
- API Key configured in [inc/acf.php:15](inc/acf.php#L15)
- Maps script enqueued in [inc/acf.php:19-22](inc/acf.php#L19-L22)
- Map functionality in [js/gmap.js](js/gmap.js)

**Color System:**
ACF-based CSS custom properties in [inc/acf.php:83-104](inc/acf.php#L83-L104). Available color variables:
- `--ane-warna-text`
- `--ane-warna-gelap` (dark)
- `--ane-warna-utama` (primary)
- `--ane-warna-terang` (light)
- `--ane-warna-alternatif` (alternative)
- `--ane-warna-utama-2` (primary-2)
- `--ane-warna-putih` (white)

Each color also has an RGB variant (e.g., `--ane-warna-utama-rgb`) via `hex2rgb()` function.

**Custom Scripts Integration:**
- Google Analytics header/footer: `ane_ga_header`, `ane_ga_footer`
- Meta Pixel header: `ane_metapixel_header`
- Meta SDK body: `ane_metasdk_body`
- Custom header scripts: `ane_sc_header`

### Asset Management

**CSS Loading (via [inc/makhadane.php:37-52](inc/makhadane.php#L37-L52)):**
- FontAne (custom icon font)
- Google Fonts (Poppins)
- Magnific Popup (lightbox)
- Owl Carousel (slider)
- Main compiled CSS: [css/main.min.css](css/main.min.css)

**JavaScript Loading:**
- jQuery 3.3.1 (CDN, deregistered default WP jQuery)
- Magnific Popup JS
- Owl Carousel JS
- [js/makhadane.js](js/makhadane.js) - Main theme JavaScript
- [js/postviews-cache.js](js/postviews-cache.js) - Post views tracking
- [js/gmap.js](js/gmap.js) - Google Maps functionality

**CDN Configuration:**
The theme can use CDN for assets via `WP_ANE_CDN` constant defined in [inc/makhadane.php:7-13](inc/makhadane.php#L7-L13):
- Production (HTTPS): `https://cdn.webane.net/themes/makhadane`
- Development/HTTP: Uses `get_template_directory_uri()`

**Image Sizes:**
Custom image sizes registered in [functions.php:14-28](functions.php#L14-L28):
- `kotak`: 400×400 (cropped) - square
- `persegi`: 800×1000 (cropped) - portrait
- `backend-persegi`: 100×125 (cropped) - admin thumbnail portrait
- `backend-kotak`: 100×100 (cropped) - admin thumbnail square
- `backend-default`: 100×56 (cropped) - admin thumbnail default
- `backend-banner`: 200×44 (cropped) - admin banner
- Medium: 700×394 (cropped)
- Large: 1000×563 (cropped)
- Thumbnail: 400×225 (cropped)

### Navigation & Widgets

**Registered Menus ([functions.php:30-33](functions.php#L30-L33)):**
- `menuutama` - "Menu Utama" (Main Menu)
- `menufooter` - "Menu Footer" (Footer Menu)

**Sidebar Areas ([functions.php:69-88](functions.php#L69-L88)):**
- `default-sidebar` - "Default Sidebar"
- `blog-sidebar` - "Blog Type Sidebar"

### Multilingual Support

The theme is translation-ready with Indonesian (id_ID) as the primary language:
- POT file: [languages/makhadane.pot](languages/makhadane.pot)
- Indonesian translation: [languages/id_ID.po](languages/id_ID.po), [languages/id_ID.mo](languages/id_ID.mo)
- PHP translation (WP 6.5+): [languages/id_ID.l10n.php](languages/id_ID.l10n.php)

Use `__('text', 'makhadane')` or `esc_html__('text', 'makhadane')` for translatable strings.

## Common Coding Patterns

### Loading Template Parts

```php
// Basic template part
get_template_part('tp/content', 'ustadz');

// Device-specific loading (see page-home.php)
if (wp_is_mobile()) {
    get_template_part('tp/pages/home-mobile');
} else {
    get_template_part('tp/pages/home-desktop');
}
```

### ACF Field Retrieval

```php
// Options page field
$color = get_field('ane-warna-utama', 'option');
$banner = get_field('your_banner_field', 'option');

// Post meta field
$field_value = get_field('field_name');
$field_value = get_field('field_name', $post_id);
```

### Custom Post Type Queries

```php
$args = array(
    'post_type' => 'ustadz',
    'posts_per_page' => 10,
    'tax_query' => array(
        array(
            'taxonomy' => 'pelajaran',
            'field' => 'slug',
            'terms' => 'matematika'
        )
    )
);
$query = new WP_Query($args);
```

### Related Content

Use the built-in function for displaying related teachers:

```php
// Display teachers related by taxonomy
ane_tampilkan_ustadz_terkait('pelajaran'); // By subject
ane_tampilkan_ustadz_terkait('kelas');     // By class
```

## WordPress Features Supported

- Title tag (via `add_theme_support('title-tag')`)
- Post thumbnails/featured images
- Custom logo (400×97px, not flexible)
- HTML5 markup (search, comments, gallery, caption)
- Post formats: video, gallery
- Automatic feed links
- Yoast SEO breadcrumbs

## Naming Conventions

- Function prefix: `ane_` or `Ane_` (e.g., `ane_theme_setup()`)
- ACF color fields: `ane-warna-{name}` (e.g., `ane-warna-utama`)
- ACF script fields: `ane_ga_header`, `ane_metapixel_header`, etc.
- Template part prefix: `content-{type}.php`
- Custom post types: lowercase, Indonesian names where appropriate
- CSS classes: `ane-` prefix (e.g., `ane-container`, `ane-sidebar`, `ane-landingpage`)

## Performance & Security Notes

- WordPress version strings removed from assets
- Generator meta tag removed
- ACF admin styles deregistered on frontend
- Version query parameters stripped from scripts/styles
- Excerpt length limited to 15 words
- Image lazy loading via theme structure

## Admin Customizations

Located in [inc/admin.php](inc/admin.php):

- Featured image column added to all post types (80×80px thumbnails)
- Custom column headers for custom post types:
  - ustadz: "Nama Ustadz"
  - testimoni: "Pemberi Testimoni"
  - ekstrakurikuler: "Ekstrakurikuler"
  - faq: "Pertanyaan"

## Schema Markup

The theme includes structured data (JSON-LD):
- Landing page schema in [page-home.php:20-32](page-home.php#L20-L32)
- Additional schemas may be in individual templates

## Important Implementation Notes

1. **Always use text domain `makhadane`** for translatable strings
2. **Escape all output** - use `esc_html()`, `esc_attr()`, `esc_url()` as appropriate
3. **Mobile detection** - Use `wp_is_mobile()` when creating device-specific features
4. **ACF dependency** - Theme expects ACF Pro to be active
5. **SCSS compilation required** - CSS files in [css/](css/) are generated, edit SCSS sources instead
6. **Custom icon font** - FontAne provides custom icons, see [css/FontAne.css](css/FontAne.css)
7. **Google Maps API** - Update key in [inc/acf.php:15,20](inc/acf.php#L15) if needed
8. **CDN configuration** - Production uses `cdn.webane.net`, configure via `WP_ANE_CDN` constant

## File Modification Checklist

When modifying this theme:

- [ ] If changing styles: Edit SCSS files in [scss/](scss/), then compile to CSS
- [ ] If adding custom post types: Add to [inc/custompost.php](inc/custompost.php)
- [ ] If adding theme functionality: Create new file in [inc/](inc/) (auto-loaded)
- [ ] If creating new templates: Follow naming convention and use template parts from [tp/](tp/)
- [ ] If using ACF fields: Define fields in WordPress admin under "Webane Settings"
- [ ] If adding translatable strings: Use `makhadane` text domain and update [languages/makhadane.pot](languages/makhadane.pot)
- [ ] If modifying asset loading: Update [inc/makhadane.php](inc/makhadane.php) `ane_load_css_and_js()`
