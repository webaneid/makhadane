# Makhadane WordPress Theme

A premium WordPress theme designed specifically for Islamic schools, madrasahs, and pesantren (Islamic boarding schools). Built by Webane Indonesia with modern features, SEO optimization, and comprehensive customization options.

## Overview

**Theme Name:** Makhadane
**Version:** 4.1.1
**Text Domain:** `makhadane`
**Requires WordPress:** 4.7+
**Requires PHP:** 7.4+
**License:** Proprietary - Licensed for Webane clients only
**Author:** Webane Indonesia
**Author URI:** https://webane.com

## Features

### Core Features
- ✅ **Auto-Update System** - Automatic theme updates via GitHub releases
- ✅ **ACF Pro Integration** - Advanced Custom Fields for flexible content management
- ✅ **SEO Optimized** - Google News ready with NewsArticle schema
- ✅ **Mobile Responsive** - Device-specific templates for optimal performance
- ✅ **CDN Ready** - Built-in CDN support for static assets
- ✅ **Translation Ready** - Full Indonesian translation included

### Custom Post Types
- 👨‍🏫 **Ustadz** (Teachers/Instructors)
- 📚 **Ekstrakurikuler** (Extracurricular Activities)
- 💬 **Testimoni** (Testimonials)
- ❓ **FAQ** (Frequently Asked Questions)

### Custom Taxonomies
- 📖 **Pelajaran** (Subjects/Courses)
- 🎓 **Kelas** (Classes/Grades)

### Page Templates
- 🏠 Landing Page (Mobile/Desktop optimized)
- 👤 Profile Page
- 🏢 Institutional Page
- 📞 Contact Page
- 📰 News Listing
- 🔗 Linktree (Social media aggregator)
- 📱 Mobile Category Page

### SEO & Schema
- Google News sitemap (`/news-sitemap.xml`)
- NewsArticle schema markup
- Breadcrumb schema
- Publisher & Author schema
- AI-friendly metadata (Dublin Core, Citation)
- Facebook Open Graph & Twitter Cards

### Integration
- 💬 Facebook Comments
- 📊 Google Analytics
- 📈 Meta Pixel
- 🗺️ Google Maps
- 🎨 Custom Color System

## Installation

### Requirements
- WordPress 4.7 or higher
- PHP 7.4 or higher
- Advanced Custom Fields Pro plugin
- Yoast SEO (recommended)

### Quick Install

1. Download the latest release ZIP from [Releases](https://github.com/webaneid/makhadane/releases)
2. Go to WordPress admin → Appearance → Themes → Add New → Upload Theme
3. Upload the ZIP file and activate
4. Install and activate ACF Pro
5. Configure theme settings via **Makhad Ane** admin menu

## Auto-Update System

This theme includes automatic update functionality via GitHub releases.

### How It Works
1. Theme checks GitHub every 12 hours for new releases
2. Updates appear in WordPress admin → Themes page
3. Click "Update Available" to install new version
4. Theme automatically downloads and installs from GitHub

### Manual Update Check
Go to: `wp-admin/themes.php?ane_force_check=1`

### Debug Mode
Go to: `wp-admin/themes.php?ane_debug=1`

For complete documentation, see [auto-update-guide.md](auto-update-guide.md)

## Configuration

### Initial Setup

1. **Brand Identity**
   - Upload logo (400×97px recommended)
   - Set company name and tagline
   - Configure fallback images

2. **Color Customization**
   - Primary color (`--ane-warna-utama`)
   - Secondary color (`--ane-warna-utama-2`)
   - Dark/Light variants
   - All colors available as CSS variables

3. **Social Media**
   - WhatsApp, Facebook, Twitter/X
   - Instagram, TikTok, YouTube
   - Telegram, Threads, LinkedIn

4. **Custom Scripts**
   - Google Analytics (header/footer)
   - Meta Pixel
   - Facebook SDK
   - Custom scripts

### Menu Locations
- **Menu Utama** - Main navigation menu
- **Menu Footer** - Footer navigation menu

### Widget Areas
- **Default Sidebar** - Default posts/pages sidebar
- **Blog Type Sidebar** - Blog-specific sidebar

## Image Sizes

| Size | Dimensions | Crop | Usage |
|------|------------|------|-------|
| kotak | 400×400 | Yes | Square thumbnails |
| persegi | 800×1000 | Yes | Portrait images |
| medium | 700×394 | Yes | Medium posts |
| large | 1000×563 | Yes | Featured images |
| thumbnail | 400×225 | Yes | Post thumbnails |

## Development

### File Structure

```
makhadane/
├── inc/                    # Functionality modules
│   ├── updater.php        # Auto-update system
│   ├── admin.php          # Admin customizations
│   ├── acf.php            # ACF configuration
│   ├── custompost.php     # Custom post types
│   ├── makhadane.php      # Core functions
│   ├── post.php           # Post utilities
│   ├── seo.php            # SEO features
│   ├── images.php         # Image handling
│   ├── widgets.php        # Widget registration
│   └── admin/             # Admin submodules
├── tp/                    # Template parts
│   ├── content-*.php      # Content templates
│   ├── pages/             # Page-specific templates
│   └── mobile/            # Mobile templates
├── scss/                  # SCSS source files
├── css/                   # Compiled CSS
├── js/                    # JavaScript files
├── languages/             # Translation files
└── img/                   # Theme images
```

### SCSS Compilation

```bash
# Install Sass
npm install -g sass

# Compile styles
sass scss/main.scss css/main.css --style compressed
sass scss/admin.scss css/admin.css --style compressed

# Watch for changes
sass --watch scss/main.scss:css/main.css --style compressed
```

### Coding Standards
- WordPress Coding Standards
- PSR-4 autoloading for `/inc` files
- Text domain: `makhadane`
- Function prefix: `ane_`
- Escape all output: `esc_html()`, `esc_attr()`, `esc_url()`

## Changelog

### [4.1.1] - 2026-01-01

#### Added
- Auto-update system via GitHub releases
- Facebook Comments integration
- Linktree page template for social media
- Complete Indonesian translation (95% coverage)
- Auto-update documentation guide

#### Improved
- Heading hierarchy for SEO optimization
- Schema.org markup enhancement
- Translation file generation

#### Fixed
- Undefined function error in content-page.php
- Text domain consistency in linktree template
- Heading levels in related/newest posts

### Previous Versions
Template ini didesain dari tahun 2019 mulai dari versi 1.0.0 dan terus dikembangkan oleh Webane Indonesia.

## Support

### Documentation
- [Auto-Update Guide](auto-update-guide.md) - Complete auto-update system documentation
- [CLAUDE.md](CLAUDE.md) - Development guidelines for AI assistants

### Resources
- **Website:** https://webane.com
- **Email:** hello@webane.com
- **Issues:** https://github.com/webaneid/makhadane/issues

### Client Support
This theme is exclusively for Webane Indonesia clients. For support:
1. Contact Webane support team
2. Create an issue on GitHub (for licensed clients)
3. Email: hello@webane.com

## Credits

**Developed by:** Webane Indonesia
**Design & Development:** Webane Squad
**Copyright:** © 2019-2026 Webane Indonesia
**License:** Proprietary - Licensed for Webane clients only

## Third-Party Libraries

- **Owl Carousel** - Touch enabled jQuery plugin
- **Magnific Popup** - Responsive lightbox & dialog script
- **Google Fonts** - Poppins font family
- **FontAne** - Custom icon font by Webane

## License

This theme is proprietary software licensed exclusively for clients of Webane Indonesia. Unauthorized distribution, modification, or use is prohibited.

For licensing inquiries, contact: hello@webane.com

---

**Made with ❤️ by Webane Indonesia**
