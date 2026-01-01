# SEO Module Adaptation for Makhad Ane Theme

**Date:** 2026-01-01
**Theme Version:** 4.1.1
**File:** `inc/seo.php`

## Overview

File `inc/seo.php` telah disesuaikan dari template Elemen Ane untuk mengikuti struktur ACF fields yang sudah ada di Makhad Ane theme.

## Changes Summary

### 1. Package Information Updated

**Before:**
```php
@package elemenane
@since 1.0.4
```

**After:**
```php
@package makhadane
@since 4.1.1
```

### 2. ACF Field Structure Mapping

Semua helper functions telah disesuaikan untuk menggunakan struktur field Makhad Ane:

#### Company Name
**Old:** `get_field( 'ane_com_nama', 'option' )`
**New:** `get_field( 'about_ane', 'option' )['company_name']`

#### Company Description/Slogan
**Old:** `get_field( 'ane_com_des', 'option' )`
**New:** `get_field( 'about_ane', 'option' )['company_sologan']`

#### Company URL
**Old:** `get_field( 'ane_com_link', 'option' )`
**New:** `get_field( 'contact_ane', 'option' )['kontak_website']`

#### Company Address
**Old:** `get_field( 'ane_com_alamat', 'option' )`
**New:** Uses existing `ane_get_alamat()` function from `inc/makhadane.php` to avoid duplication

Full implementation:
```php
if ( function_exists( 'ane_get_alamat' ) ) {
    return ane_get_alamat();
}
// Fallback: build from contact_ane fields
```

#### Company Phone
**Old:** `get_field( 'ane_telepon', 'option' )`
**New:** `get_field( 'contact_ane', 'option' )['kontak_telepon']`

#### Company Mobile
**Old:** `get_field( 'ane_kontak', 'option' )['ane_handphone']`
**New:** `get_field( 'contact_ane', 'option' )['kontak_handphone']`

#### Company Email
**Old:** `get_field( 'ane_kontak', 'option' )['ane_email']`
**New:** `get_field( 'contact_ane', 'option' )['kontak_email']`

#### Company Website
**Old:** `get_field( 'ane_kontak', 'option' )['ane_website']`
**New:** `get_field( 'contact_ane', 'option' )['kontak_website']`

### 3. Schema Output Comments

All schema output comments updated from "Elemen Ane" to "Makhad Ane":

- `<!-- NewsArticle Schema by Makhad Ane -->`
- `<!-- CollectionPage Schema by Makhad Ane -->`
- `<!-- Page Schema ({$schema_type}) by Makhad Ane -->`
- `<!-- Product Schema by Makhad Ane -->`

### 4. Functions Reusing Existing Code

To avoid code duplication, the following approach is used:

**Company Address:**
```php
function ane_get_company_address() : string {
    // Reuse existing function to avoid duplication
    if ( function_exists( 'ane_get_alamat' ) ) {
        return ane_get_alamat();
    }

    // Fallback implementation using contact_ane
    // ...
}
```

## ACF Field Structure Reference

### Makhad Ane ACF Options Structure

**Group: `about_ane`**
- `company_name` - Company Name (text)
- `company_sologan` - Company Slogan/Description (textarea)

**Group: `contact_ane`**
- `kontak_alamat` - Street Address (text)
- `kontak_kabupaten` - City/Regency (text)
- `kontak_provinsi` - Province (text)
- `kontak_kodepos` - Postal Code (text)
- `kontak_telepon` - Phone Number (text)
- `kontak_handphone` - Mobile Number (text)
- `kontak_email` - Email Address (email)
- `kontak_website` - Website URL (url)

**Group: `sosmed_ane`** (Social Media)
- `sosmed_wa` - WhatsApp
- `sosmed_fb` - Facebook
- `sosmed_tw` - Twitter
- `sosmed_youtube` - YouTube
- `sosmed_ig` - Instagram
- `sosmed_tiktok` - TikTok

## Features Not Yet Mapped

The following ACF fields are referenced but may not exist in Makhad Ane:

### 1. Google Maps Location (`ane_gmap`)
**Location in code:** `inc/seo.php` line 139
**Function:** `ane_get_company_location()`
**Used in:** ContactPage schema, Organization schema (if implemented)

**Action Required:**
- [ ] Check if `ane_gmap` field exists in ACF options
- [ ] If not exists, either:
  - Create new ACF field `ane_gmap` (type: Google Map)
  - OR remove/comment out `ane_get_company_location()` function
  - OR create fallback using address coordinates

**Implementation Note:**
```php
// Current implementation
return get_field( 'ane_gmap', 'option' ) ?: false;

// If field doesn't exist, consider removing this function
// or implement alternative location data source
```

### 2. Page Schema Type Selector (`ane_page_schema_type`)
**Location in code:** `inc/seo.php` line 482
**Function:** `ane_output_page_schema()`
**Used in:** Dynamic page schema selection

**Action Required:**
- [ ] Check if pages need different schema types (AboutPage, ContactPage, FAQPage, etc.)
- [ ] If yes, create ACF field `ane_page_schema_type` for each page
- [ ] If no, set default to 'WebPage' (already set as fallback)

**Implementation Note:**
```php
// Current implementation with fallback
$schema_type = get_field( 'ane_page_schema_type', $page_id ) ?: 'WebPage';

// This will work even if field doesn't exist (defaults to 'WebPage')
// But for advanced schema types, field is recommended
```

**Recommended ACF Field Setup:**
```
Field Name: ane_page_schema_type
Field Type: Select
Choices:
- WebPage (Default)
- AboutPage
- ContactPage
- FAQPage
- ProfilePage
- CollectionPage
- ItemPage
Location: Post Type = Page
```

## Custom Post Types Schema Support

### 1. Product Schema (Optional)

**Location in code:** `inc/seo.php` lines 1061-1189
**Function:** `ane_output_product_schema()`
**Triggered on:** `is_singular( 'product' )`

Product schema still uses original field names from Elemen Ane:

| ACF Field Name | Purpose | Type | Location in Code |
|----------------|---------|------|------------------|
| `ane_harga_normal` | Regular Price | Number | Line 1084 |
| `ane_harga_diskon` | Sale/Discount Price | Number | Line 1085 |
| `ane_stok` | Stock Status (yes/no) | Select | Line 1094 |
| `ane_product_sku` | Product SKU | Text | Line 1130 |

**Action Required:**
- [ ] Check if Makhad Ane uses `product` custom post type
- [ ] If YES and field names are different, update lines 1084-1130
- [ ] If NO product CPT, consider removing `ane_output_product_schema()` function
- [ ] Verify product category taxonomy name (currently `product-category` at line 1138)

**Example Update:**
```php
// If Makhad Ane uses different field names:
// OLD:
$regular_price = get_field( 'ane_harga_normal', $product_id );

// NEW (example):
$regular_price = get_field( 'product_price', $product_id );
```

### 2. Makhad Ane Custom Post Types

**From `inc/custompost.php` review:**

Makhad Ane memiliki custom post types berikut:
- `ustadz` (Teachers/Instructors)
- `ekstrakurikuler` (Extracurricular Activities)
- `testimoni` (Testimonials)
- `faq` (FAQ)

**Current SEO Schema Support:**
- ✅ `post` → NewsArticle schema
- ✅ `page` → WebPage schema (with dynamic types)
- ⚠️ `product` → Product schema (may not be used)
- ❌ `ustadz` → No dedicated schema yet
- ❌ `ekstrakurikuler` → No dedicated schema yet
- ❌ `testimoni` → No dedicated schema yet
- ❌ `faq` → No dedicated schema yet

**Recommendations for Future Enhancement:**

**Option 1: Add Schema for Custom Post Types**

Create schemas for Makhad Ane CPTs:

```php
// In ane_add_seo_metadata() function, add:

// Schema for Ustadz (Teachers)
if ( is_singular( 'ustadz' ) ) {
    ane_output_person_schema(); // New function needed
}

// Schema for FAQ
if ( is_singular( 'faq' ) ) {
    ane_output_faq_item_schema(); // New function needed
}

// Schema for Testimonials
if ( is_singular( 'testimoni' ) ) {
    ane_output_review_schema(); // New function needed
}
```

**Option 2: Use Generic Article Schema**

Treat custom post types as regular articles:

```php
// Modify line 165-167:
// OLD:
if ( is_single() && get_post_type() === 'post' ) {
    ane_output_newsarticle_schema();
}

// NEW:
$article_types = array( 'post', 'ustadz', 'ekstrakurikuler' );
if ( is_single() && in_array( get_post_type(), $article_types, true ) ) {
    ane_output_newsarticle_schema();
}
```

**Decision Required:**
- [ ] Decide if custom post types need dedicated schemas
- [ ] Or use generic Article schema for all
- [ ] Document decision for future reference

## Known Issues & Limitations

### 1. Duplicate Breadcrumb Schema
**Issue:** Theme has two breadcrumb implementations:
- `ane_breadcrumbs()` in `inc/makhadane.php` (lines 844-976) - outputs HTML breadcrumb with schema
- `ane_output_breadcrumb_schema()` in `inc/seo.php` (lines 864-956) - outputs JSON-LD breadcrumb schema

**Impact:** May cause duplicate breadcrumb schema in page source

**Status:** ⚠️ Needs Review

**Solutions:**
1. **Option A (Recommended):** Remove HTML schema from `ane_breadcrumbs()`, keep JSON-LD
2. **Option B:** Remove `ane_output_breadcrumb_schema()` from SEO module
3. **Option C:** Add conditional to prevent both from running

**Action Required:**
- [ ] Check page source for duplicate breadcrumb schemas
- [ ] Choose solution and implement
- [ ] Document decision

### 2. Navigation Schema Dependency
**Issue:** `ane_output_navigation_schema()` (line 1012) requires menu location `menuutama`

**Verification:**
```php
// Check in functions.php if registered:
register_nav_menus( array(
    'menuutama' => 'Menu Utama',
    // ...
) );
```

**Status:** ✅ OK - Menu registered in `functions.php` line 30

### 3. Logo Fallback Path
**Issue:** Logo fallback uses hardcoded path (line 124):
```php
return get_template_directory_uri() . '/img/logo-webane.png';
```

**Action Required:**
- [ ] Verify if `/img/logo-webane.png` exists in theme
- [ ] If not, update to correct default logo path
- [ ] Or use site icon as fallback

**Recommended Fix:**
```php
// Better fallback
if ( has_site_icon() ) {
    return get_site_icon_url( 512 );
}
return get_template_directory_uri() . '/img/default-logo.png';
```

### 4. Archive Schema for Custom Taxonomies
**Issue:** `ane_output_collectionpage_schema()` only handles:
- Homepage
- Category archives
- Tag archives
- Author archives
- Date archives

**Missing:** Custom taxonomy archives (e.g., `pelajaran`, `Kelas`)

**Status:** ⚠️ Limited Support

**Action Required:**
- [ ] Test schema output on `pelajaran` taxonomy archive
- [ ] Test schema output on `Kelas` taxonomy archive
- [ ] Add support if needed

**Example Enhancement:**
```php
// In ane_output_collectionpage_schema(), add:
elseif ( is_tax() ) {
    $term = get_queried_object();
    $page_title = $term->name;
    $page_description = $term->description ?: 'Artikel dengan ' . $term->name;
    $page_url = get_term_link( $term );
}
```

### 5. Product Schema Without WooCommerce
**Issue:** Product schema implemented but Makhad Ane doesn't use WooCommerce or `product` CPT

**Impact:** Function `ane_output_product_schema()` is unused (150+ lines of code)

**Status:** ⚠️ Dead Code

**Action Required:**
- [ ] Confirm no `product` post type used
- [ ] If confirmed, remove function or comment out for future use
- [ ] Update `ane_add_seo_metadata()` to remove product check (line 180-182)

### 6. Social Media Schema
**Issue:** Social media links exist in `sosmed_ane` but not integrated into Organization schema

**Enhancement Opportunity:**
```php
// Could add to Organization schema:
'sameAs' => array(
    'https://facebook.com/...',
    'https://twitter.com/...',
    // etc
)
```

**Status:** 📝 Enhancement Suggestion

**Action Required:**
- [ ] Decide if social profiles should be in Organization schema
- [ ] Implement if needed

## Testing Checklist

### Pre-Launch Testing
- [ ] Verify all company info appears correctly in schema output
- [ ] Check NewsArticle schema on single posts
- [ ] Check CollectionPage schema on archives
- [ ] Check WebSite schema on homepage
- [ ] Validate schemas with Google Rich Results Test
- [ ] Validate with Schema.org Validator
- [ ] Test fallbacks when ACF fields are empty
- [ ] Verify breadcrumb schema generation (check for duplicates!)
- [ ] Check Open Graph tags (if Yoast disabled)
- [ ] Check Twitter Card tags (if Yoast disabled)

### Custom Post Type Testing
- [ ] Test schema on `ustadz` single pages
- [ ] Test schema on `ekstrakurikuler` single pages
- [ ] Test schema on `testimoni` single pages
- [ ] Test schema on `faq` single pages
- [ ] Test archive pages for custom post types

### Taxonomy Archive Testing
- [ ] Test schema on `pelajaran` taxonomy archive
- [ ] Test schema on `Kelas` taxonomy archive

### Edge Cases
- [ ] Test with empty ACF fields (should use fallbacks)
- [ ] Test with no featured image (should use logo)
- [ ] Test with Yoast SEO active (should not duplicate)
- [ ] Test with Yoast SEO inactive (should add OG/Twitter)
- [ ] Test on page with no parent (breadcrumb)
- [ ] Test on page with multiple parents (breadcrumb)

## Compatibility

**WordPress:** 4.7+
**PHP:** 7.4+ (uses type hints)
**ACF:** Pro version required
**Yoast SEO:** Compatible (Free/Premium)

## Next Steps

1. **Add ACF Fields** (if not exist):
   - Create `about_ane` field group with `company_name` and `company_sologan`
   - Create `contact_ane` field group with all contact sub-fields
   - Optional: Add `ane_gmap` for Google Maps integration
   - Optional: Add `ane_page_schema_type` for page schema customization

2. **Test Implementation:**
   - View source on single post to verify NewsArticle schema
   - View source on homepage to verify WebSite schema
   - Use Google Rich Results Test to validate

3. **Update SEO-IMPLEMENTATION-GUIDE.md:**
   - Document Makhad Ane specific field mappings
   - Add screenshots of ACF field structure
   - Update migration examples

## Benefits of This Approach

✅ **No Breaking Changes** - Uses existing Makhad Ane ACF structure
✅ **Code Reuse** - Leverages existing functions like `ane_get_alamat()`
✅ **Fallback Safe** - All functions have WordPress default fallbacks
✅ **Type Safe** - Maintains PHP type hints for better code quality
✅ **Documentation** - Clear comments explain field mapping
✅ **Maintainable** - Easy to understand and modify

## Migration from Other Themes

When migrating SEO module to another theme:

1. Copy `inc/seo.php` to new theme
2. Update helper functions (lines 32-140) with new ACF field names
3. Update package name in header
4. Test all schemas with validation tools
5. Update `SEO-MAKHADANE-CHANGES.md` with new field mappings

Estimated time: **30-45 minutes**

## Support

For questions or issues with SEO implementation:
- Check `SEO-IMPLEMENTATION-GUIDE.md` for detailed documentation
- Validate schemas at https://search.google.com/test/rich-results
- Review ACF field structure in WordPress admin

## Quick Reference: File Locations

### Core SEO Files
```
inc/seo.php                          ← Main SEO module (1189 lines)
SEO-MAKHADANE-CHANGES.md             ← This file (implementation notes)
SEO-IMPLEMENTATION-GUIDE.md          ← Detailed guide & migration docs
```

### Related Files
```
inc/makhadane.php                    ← Helper functions (including ane_get_alamat)
inc/custompost.php                   ← Custom post type definitions
inc/acf.php                          ← ACF configuration
functions.php                        ← Auto-loads inc/*.php files
```

### ACF Options Pages
```
WordPress Admin → Custom Fields → Field Groups
- Look for: about_ane (company info)
- Look for: contact_ane (contact details)
- Look for: sosmed_ane (social media)
```

## Quick Reference: Key Functions

### Helper Functions (Lines 32-145)
```php
ane_get_company_name()        // Company name from about_ane
ane_get_company_description() // Slogan from about_ane
ane_get_company_url()         // Website from contact_ane
ane_get_company_address()     // Full address (uses ane_get_alamat)
ane_get_company_phone()       // Phone from contact_ane
ane_get_company_mobile()      // Mobile from contact_ane
ane_get_company_email()       // Email from contact_ane
ane_get_company_website()     // Website from contact_ane
ane_get_company_location()    // Google Maps (may not exist)
ane_get_company_logo()        // Logo from WP Customizer
ane_get_company_info()        // All above as array
```

### Schema Output Functions
```php
ane_output_newsarticle_schema()      // Line 240 - Blog posts
ane_output_collectionpage_schema()   // Line 347 - Archives
ane_output_page_schema()             // Line 474 - Pages
ane_output_product_schema()          // Line 1061 - Products (unused)
ane_output_dublin_core_metadata()    // Line 647 - Academic metadata
ane_output_citation_metadata()       // Line 682 - Citation tags
ane_output_opengraph_tags()          // Line 710 - Facebook/LinkedIn
ane_output_twitter_card_tags()       // Line 785 - Twitter
ane_output_breadcrumb_schema()       // Line 864 - Breadcrumbs
ane_output_website_schema()          // Line 963 - Website + SearchAction
ane_output_navigation_schema()       // Line 1012 - Site navigation
```

### Yoast Detection
```php
ane_yoast_handles_opengraph()  // Line 213 - Check if Yoast handles OG
ane_yoast_handles_twitter()    // Line 227 - Check if Yoast handles Twitter
```

## Quick Reference: Common Tasks

### How to Disable Product Schema
```php
// In inc/seo.php, comment out lines 180-182:
/*
if ( is_singular( 'product' ) ) {
    ane_output_product_schema();
}
*/
```

### How to Add Schema for Custom Post Type
```php
// In inc/seo.php, in ane_add_seo_metadata() function, add:
if ( is_singular( 'ustadz' ) ) {
    ane_output_newsarticle_schema(); // Reuse article schema
}
```

### How to Fix Logo Fallback
```php
// In inc/seo.php, line 124, replace:
return get_template_directory_uri() . '/img/logo-webane.png';

// With:
if ( has_site_icon() ) {
    return get_site_icon_url( 512 );
}
return get_template_directory_uri() . '/img/default-logo.png';
```

### How to Check Schema Output
```bash
# 1. View page source (Ctrl+U or Cmd+Option+U)
# 2. Search for: <script type="application/ld+json">
# 3. Or use browser DevTools → Elements → Search

# Online validators:
# https://search.google.com/test/rich-results
# https://validator.schema.org/
```

### How to Debug ACF Fields
```php
// Temporarily add to template:
echo '<pre>';
var_dump( get_field( 'about_ane', 'option' ) );
var_dump( get_field( 'contact_ane', 'option' ) );
echo '</pre>';
```

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 4.1.1 | 2026-01-01 | Initial adaptation from Elemen Ane to Makhad Ane structure |

## Future Enhancements Roadmap

### Priority 1 (High Impact)
- [ ] Resolve duplicate breadcrumb schema issue
- [ ] Add schema support for custom post types (ustadz, ekstrakurikuler, etc.)
- [ ] Fix logo fallback path
- [ ] Create/verify required ACF field groups

### Priority 2 (SEO Improvements)
- [ ] Add custom taxonomy archive schema support
- [ ] Integrate social media profiles into Organization schema
- [ ] Add LocalBusiness schema (if applicable)
- [ ] Implement FAQ schema for faq post type

### Priority 3 (Code Quality)
- [ ] Remove unused product schema (if not needed)
- [ ] Add schema caching for high-traffic sites
- [ ] Add unit tests for helper functions
- [ ] Create ACF field import/export JSON

### Priority 4 (Documentation)
- [ ] Add screenshots to implementation guide
- [ ] Create video tutorial for ACF setup
- [ ] Document all schema types with examples
- [ ] Create troubleshooting FAQ

---

**Last Updated:** 2026-01-01
**Developer:** Webane Indonesia
**Theme:** Makhad Ane v4.1.1
**SEO Module Version:** 1.0.0 (adapted from Elemen Ane 1.0.6)

---

## Support & Resources

**Documentation:**
- [SEO-IMPLEMENTATION-GUIDE.md](SEO-IMPLEMENTATION-GUIDE.md) - Detailed implementation guide
- [CLAUDE.md](CLAUDE.md) - Theme development guidelines

**Validation Tools:**
- Google Rich Results Test: https://search.google.com/test/rich-results
- Schema.org Validator: https://validator.schema.org/
- Facebook Debugger: https://developers.facebook.com/tools/debug/
- Twitter Card Validator: https://cards-dev.twitter.com/validator

**Schema.org References:**
- NewsArticle: https://schema.org/NewsArticle
- Organization: https://schema.org/Organization
- WebSite: https://schema.org/WebSite
- BreadcrumbList: https://schema.org/BreadcrumbList

**Contact:**
- Developer: Webane Indonesia
- Theme Support: Check theme documentation
- SEO Questions: Refer to SEO-IMPLEMENTATION-GUIDE.md
