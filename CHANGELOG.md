# Changelog

All notable changes to Makhadane theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.1.3] - 2026-02-22

### Improved
- **Single Post Heading Hierarchy**
  - Ensured H1 is only used for the main single post title in main query
  - Related posts and newest posts consistently render item titles as H3 in single context
  - Excluded current post from newest posts query to avoid duplicate/self listing in single page
- **Archive/Taxonomy Heading Consistency**
  - Normalized testimonial item title rendering to use contextual title helper
  - Updated no-results heading level to avoid duplicate H1 on archive/listing pages
- **Mobile Post Meta Presentation**
  - Refined mobile layout for `.ane-meta` in single posts to improve readability and spacing

### Fixed
- **Home Mobile Newest Posts Section Rendering**
  - Section now fully hidden when no posts are available
  - Section title is no longer displayed when post query is empty
- **Mobile Meta Separator Noise**
  - Removed leading dash separators from reading-time/meta text on mobile
- **Mobile View Count Visibility**
  - Hidden `interactionStatistic` (view count) on mobile output for cleaner presentation

## [4.1.2] - 2026-01-01

### Added
- **GitHub Actions Workflow** - Automatic release creation when pushing tags
- **Extended Media Upload Support** - Enable upload for additional image formats:
  - SVG (Scalable Vector Graphics) with sanitization
  - WebP (modern image format)
  - AVIF (next-gen image format)
  - GIF (animated images)
  - ICO (favicon files)
- **Media Library Enhancements**
  - SVG thumbnail display in media library
  - Format badge column showing file type (SVG, WebP, GIF, etc.)
  - ACF image field SVG support
  - Automatic SVG sanitization for security
- Auto-compile SCSS to CSS via GitHub Actions
- Auto-create distribution ZIP
- Auto-upload release asset
- Complete with CHANGELOG.md, RELEASE-GUIDE.md

## [4.1.1] - 2026-01-01

### Added
- **Auto-Update System** - Automatic theme updates via GitHub releases
- **Facebook Comments Integration** - Replace WordPress native comments with Facebook Comments
- **Linktree Page Template** - Social media link aggregator page for bio links
- **Complete Indonesian Translation** - 95% translation coverage (144 of 151 strings)
- **Auto-Update Documentation** - Comprehensive guide in `auto-update-guide.md`
- **SEO Enhancements**
  - Google News sitemap (`/news-sitemap.xml`)
  - NewsArticle schema markup
  - AI-friendly metadata (Dublin Core, Citation)
  - Enhanced RSS feed

### Improved
- **Heading Hierarchy for SEO** - Proper H1/H2/H3 structure for better SEO
  - H1: Single post main content only
  - H2: Archive pages
  - H3: Homepage, related posts, newest posts
- **Schema.org Markup** - Enhanced structured data for all post types
- **Translation System** - Complete POT/PO/MO regeneration with all theme strings

### Fixed
- Undefined function error in `tp/content-page.php`
- Text domain consistency in linktree template (changed from 'elemenane' to 'makhadane')
- Heading levels in related/newest posts sections

### Security
- Added `.gitignore` for proper version control
- Sanitized all output in debug mode
- Version control best practices

### Developer
- Complete codebase documentation in `CLAUDE.md`
- Admin dashboard guide in `ADMIN-DASHBOARD-GUIDE.md`
- SEO implementation guide in `SEO-IMPLEMENTATION-GUIDE.md`

---

## [4.1.0] - 2025-12-15

### Added
- Initial release for Makhadane (Islamic schools theme)
- Custom post types: Ustadz, Ekstrakurikuler, Testimoni, FAQ
- Custom taxonomies: Pelajaran (Subjects), Kelas (Classes)
- ACF Pro integration
- Mobile-responsive design with device-specific templates
- CDN-ready architecture
- Custom color system via ACF
- Google Maps integration
- Multiple page templates (Profile, Contact, News, etc.)

### Features
- **Landing Page System**
  - Desktop and mobile optimized layouts
  - Hero sections with customizable content
  - Feature highlights
  - Testimonials showcase
  - FAQ accordion
  - Teacher profiles
  - Extracurricular activities

- **SEO & Schema**
  - Breadcrumb navigation
  - Publisher & Author schema
  - Yoast SEO integration
  - Social media meta tags

- **Integrations**
  - Google Analytics
  - Meta Pixel (Facebook)
  - Custom tracking scripts
  - Social media platforms (9 networks)

---

## Previous Versions

Theme development started in 2019 at version 1.0.0 by Webane Indonesia.
Continuously developed and improved for Islamic educational institutions.

---

**Note:** For detailed update instructions and GitHub release workflow, see [auto-update-guide.md](auto-update-guide.md)

**License:** Proprietary - Licensed for Webane Indonesia clients only
**Author:** Webane Indonesia - https://webane.com
