# Auto-Update System Guide for Makhadane Theme

Complete guide for implementing GitHub-based automatic theme updates system, based on Elemen Ane theme architecture.

## Overview

The auto-update system allows WordPress themes to check for updates from GitHub releases and provide automatic update functionality. This is essential for premium theme distribution outside WordPress.org repository.

## Architecture Components

### 1. Core Updater Class (`inc/updater.php`)

The main updater class that handles all update logic.

**Key Features:**
- Checks GitHub releases via API
- Compares versions
- Integrates with WordPress update system
- Fixes folder naming after update
- Provides admin notices
- Caches API responses for 24 hours

**Class Properties:**

```php
private $github_owner = 'webaneid';           // GitHub username/organization
private $github_repo = 'makhadane';           // Repository name
private $current_version;                      // From style.css
private $theme_slug = 'makhadane';            // Theme folder name
private $github_api_url;                      // API endpoint
```

### 2. WordPress Hooks Integration

**Filter Hooks:**
- `pre_set_site_transient_update_themes` - Inject update data
- `upgrader_source_selection` - Fix downloaded folder name
- `theme_action_links_{theme_slug}` - Add update link to theme row

**Action Hooks:**
- `admin_footer` - Show update notices on themes page
- `admin_init` - Register manual update checker

### 3. GitHub Release Requirements

**Release Asset Naming:**
- ZIP file must be named: `makhadane-{version}.zip` (e.g., `makhadane-4.1.2.zip`)
- Tag format: `v{version}` (e.g., `v4.1.2`)
- The ZIP must contain a folder named exactly `makhadane/` at root level

**Why this matters:**
- GitHub's automatic zipball creates folders with commit hash suffixes
- Custom ZIP ensures correct folder name after extraction
- WordPress requires exact theme slug match

## Implementation Steps

### Step 1: Create Updater Class File

**File:** `inc/updater.php`

```php
<?php
/**
 * Theme Auto-Updater
 *
 * Checks for theme updates from GitHub releases and provides
 * automatic update functionality for premium theme distribution.
 *
 * @package makhadane
 * @since 4.1.1
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Makhadane Theme Updater Class
 */
class Makhadane_Updater {

    /**
     * GitHub repository owner.
     *
     * @var string
     */
    private $github_owner = 'webaneid';

    /**
     * GitHub repository name.
     *
     * @var string
     */
    private $github_repo = 'makhadane';

    /**
     * Current theme version.
     *
     * @var string
     */
    private $current_version;

    /**
     * Theme slug.
     *
     * @var string
     */
    private $theme_slug = 'makhadane';

    /**
     * GitHub API URL for releases.
     *
     * @var string
     */
    private $github_api_url;

    /**
     * Constructor.
     */
    public function __construct() {
        $theme = wp_get_theme();
        $this->current_version = $theme->get( 'Version' );
        $this->github_api_url = "https://api.github.com/repos/{$this->github_owner}/{$this->github_repo}/releases/latest";

        // Hook into WordPress update system.
        add_filter( 'pre_set_site_transient_update_themes', array( $this, 'check_for_update' ) );
        add_filter( 'upgrader_source_selection', array( $this, 'fix_source_folder' ), 10, 3 );

        // Add update checker to admin footer.
        add_action( 'admin_footer', array( $this, 'add_update_checker_notice' ) );

        // Add custom update link to theme actions.
        add_filter( 'theme_action_links_' . $this->theme_slug, array( $this, 'add_update_action_link' ), 10, 2 );
    }

    /**
     * Check for theme updates.
     *
     * @param object $transient Update transient.
     * @return object Modified transient.
     */
    public function check_for_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        // Get latest release from GitHub.
        $remote_version = $this->get_remote_version();

        if ( ! $remote_version ) {
            return $transient;
        }

        // Compare versions.
        if ( version_compare( $this->current_version, $remote_version['version'], '<' ) ) {
            $theme_data = wp_get_theme( $this->theme_slug );

            $transient->response[ $this->theme_slug ] = array(
                'theme'        => $this->theme_slug,
                'new_version'  => $remote_version['version'],
                'url'          => $remote_version['url'],
                'package'      => $remote_version['package'],
                'requires'     => '4.7',
                'requires_php' => '7.4',
            );
        }

        return $transient;
    }

    /**
     * Get remote version info from GitHub.
     *
     * @return array|false Version info or false on failure.
     */
    private function get_remote_version() {
        // Check cache first (24 hours).
        $cache_key = 'makhadane_update_check';
        $cached = get_transient( $cache_key );

        if ( false !== $cached ) {
            return $cached;
        }

        // Fetch from GitHub API.
        $response = wp_remote_get(
            $this->github_api_url,
            array(
                'timeout' => 10,
                'headers' => array(
                    'Accept' => 'application/vnd.github.v3+json',
                ),
            )
        );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( empty( $data['tag_name'] ) ) {
            return false;
        }

        // Remove 'v' prefix from version.
        $version = ltrim( $data['tag_name'], 'v' );

        // Get download URL from assets (makhadane-x.x.x.zip).
        $package_url = '';
        if ( ! empty( $data['assets'] ) && is_array( $data['assets'] ) ) {
            foreach ( $data['assets'] as $asset ) {
                if ( isset( $asset['name'] ) && strpos( $asset['name'], 'makhadane-' ) === 0 && strpos( $asset['name'], '.zip' ) !== false ) {
                    $package_url = $asset['browser_download_url'];
                    break;
                }
            }
        }

        // If no asset found, return false (don't use zipball as it creates wrong folder names).
        if ( empty( $package_url ) ) {
            return false;
        }

        $version_info = array(
            'version' => $version,
            'url'     => $data['html_url'],
            'package' => $package_url,
        );

        // Cache for 24 hours.
        set_transient( $cache_key, $version_info, DAY_IN_SECONDS );

        return $version_info;
    }

    /**
     * Fix source folder name after update.
     *
     * GitHub downloads come with weird folder names, we need to rename to theme slug.
     *
     * @param string $source        Source folder.
     * @param string $remote_source Remote source.
     * @param object $upgrader      WP_Upgrader instance.
     * @return string Fixed source folder.
     */
    public function fix_source_folder( $source, $remote_source, $upgrader ) {
        global $wp_filesystem;

        // Only for theme updates.
        if ( ! isset( $upgrader->skin->theme ) || $upgrader->skin->theme !== $this->theme_slug ) {
            return $source;
        }

        // Fix the folder name.
        $corrected_source = trailingslashit( $remote_source ) . $this->theme_slug . '/';

        if ( $wp_filesystem->move( $source, $corrected_source, true ) ) {
            return $corrected_source;
        }

        return $source;
    }

    /**
     * Add update checker notice in admin.
     */
    public function add_update_checker_notice() {
        $screen = get_current_screen();

        // Only show on themes page.
        if ( 'themes' !== $screen->base ) {
            return;
        }

        $remote_version = $this->get_remote_version();

        if ( ! $remote_version ) {
            return;
        }

        if ( version_compare( $this->current_version, $remote_version['version'], '<' ) ) {
            $update_url = wp_nonce_url(
                admin_url( 'update.php?action=upgrade-theme&theme=' . $this->theme_slug ),
                'upgrade-theme_' . $this->theme_slug
            );
            ?>
            <div class="notice notice-info is-dismissible">
                <p>
                    <strong><?php esc_html_e( 'Makhadane Theme Update Available!', 'makhadane' ); ?></strong><br>
                    <?php
                    printf(
                        /* translators: 1: new version, 2: current version, 3: update URL */
                        esc_html__( 'Version %1$s is available. You have version %2$s.', 'makhadane' ) . ' <a href="%3$s" class="update-link">' . esc_html__( 'Update now', 'makhadane' ) . '</a>',
                        esc_html( $remote_version['version'] ),
                        esc_html( $this->current_version ),
                        esc_url( $update_url )
                    );
                    ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Add update link to theme action links.
     *
     * @param array  $actions Theme action links.
     * @param object $theme   Theme object.
     * @return array Modified action links.
     */
    public function add_update_action_link( $actions, $theme ) {
        $remote_version = $this->get_remote_version();

        if ( ! $remote_version ) {
            return $actions;
        }

        if ( version_compare( $this->current_version, $remote_version['version'], '<' ) ) {
            $update_url = wp_nonce_url(
                admin_url( 'update.php?action=upgrade-theme&theme=' . $this->theme_slug ),
                'upgrade-theme_' . $this->theme_slug
            );

            $actions['update'] = sprintf(
                '<a href="%s" class="update-link" aria-label="%s">%s</a>',
                esc_url( $update_url ),
                /* translators: %s: theme name */
                esc_attr( sprintf( __( 'Update %s now', 'makhadane' ), 'Makhadane' ) ),
                __( 'Update Available', 'makhadane' )
            );
        }

        return $actions;
    }

    /**
     * Force update check (for manual trigger).
     */
    public static function force_update_check() {
        delete_transient( 'makhadane_update_check' );
        delete_site_transient( 'update_themes' );
    }
}

// Initialize updater.
new Makhadane_Updater();

/**
 * Add manual update check button to admin.
 */
function makhadane_add_manual_update_check() {
    add_action( 'admin_notices', function() {
        $screen = get_current_screen();

        if ( 'themes' === $screen->base && isset( $_GET['ane_force_check'] ) ) {
            Makhadane_Updater::force_update_check();
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e( 'Update check completed!', 'makhadane' ); ?></p>
            </div>
            <?php
        }

        // Debug mode (add ?ane_debug=1 to themes.php URL).
        if ( 'themes' === $screen->base && isset( $_GET['ane_debug'] ) ) {
            $updates = get_site_transient( 'update_themes' );
            echo '<div class="notice notice-info"><pre>';
            echo 'Current version: ' . wp_get_theme()->get( 'Version' ) . "\n";
            echo 'Update data: ';
            print_r( isset( $updates->response['makhadane'] ) ? $updates->response['makhadane'] : 'No update data' );
            echo '</pre></div>';
        }
    });
}
add_action( 'admin_init', 'makhadane_add_manual_update_check' );
```

### Step 2: Auto-Load Updater

The updater file is automatically loaded via the glob pattern in `functions.php` (lines 109-123):

```php
$root_files = glob( get_template_directory() . '/inc/*.php' );
$subdirectory_files = glob( get_template_directory() . '/inc/**/*.php' );
$all_files = array_merge( $root_files ?: array(), $subdirectory_files ?: array() );

foreach ( $all_files as $file ) {
    if ( is_file( $file ) && pathinfo( $file, PATHINFO_EXTENSION ) === 'php' ) {
        include_once $file;
    }
}
```

No additional `require` needed - just create the file in `/inc/` directory.

### Step 3: Add Admin Menu Link

Add update checker link to admin dashboard in `inc/admin.php`:

```php
array(
    'label'       => __( 'Theme Updates', 'makhadane' ),
    'title'       => __( 'Check for Updates', 'makhadane' ),
    'description' => __( 'Check GitHub for latest Makhad Ane theme version and update automatically.', 'makhadane' ),
    'link'        => add_query_arg( 'ane_force_check', '1', admin_url( 'themes.php' ) ),
    'link_label'  => __( 'Check Updates Now', 'makhadane' ),
),
```

This creates a dashboard card that links to themes page with force update check.

### Step 4: Add Translation Strings

Add these strings to `languages/makhadane.pot`:

```pot
msgid "Makhadane Theme Update Available!"
msgstr ""

msgid "Version %1$s is available. You have version %2$s."
msgstr ""

msgid "Update now"
msgstr ""

msgid "Update Available"
msgstr ""

msgid "Update %s now"
msgstr ""

msgid "Update check completed!"
msgstr ""

msgid "Check for Updates"
msgstr ""

msgid "Check GitHub for latest Makhad Ane theme version and update automatically."
msgstr ""

msgid "Check Updates Now"
msgstr ""

msgid "Theme Updates"
msgstr ""
```

## GitHub Release Workflow

### Creating a Release

1. **Prepare Theme Files**
   - Update version in `style.css`
   - Update changelog if exists
   - Test thoroughly

2. **Create Release ZIP**

```bash
# From theme parent directory
cd /Users/webane/sites/makhadane/wp-content/themes/

# Create ZIP with correct structure
zip -r makhadane-4.1.2.zip makhadane/ -x "makhadane/.git/*" "makhadane/node_modules/*" "makhadane/.DS_Store"

# Verify structure (should show makhadane/ at root)
unzip -l makhadane-4.1.2.zip | head -20
```

**CRITICAL:** The ZIP must contain `makhadane/` folder at root level, not nested folders.

3. **Create GitHub Release**

```bash
# Using GitHub CLI
gh release create v4.1.2 \
  --title "Makhadane v4.1.2" \
  --notes "Release notes here" \
  makhadane-4.1.2.zip

# Or manually via GitHub web interface:
# 1. Go to repository → Releases → Draft a new release
# 2. Tag: v4.1.2
# 3. Title: Makhadane v4.1.2
# 4. Upload makhadane-4.1.2.zip as asset
# 5. Publish release
```

4. **Verify Release**
   - Check that asset is named `makhadane-4.1.2.zip`
   - Tag is `v4.1.2`
   - Asset is downloadable
   - ZIP structure is correct

## How It Works

### Update Check Flow

1. **Automatic Check:**
   - WordPress checks for updates every 12 hours
   - `pre_set_site_transient_update_themes` filter fires
   - Updater class queries GitHub API
   - Response cached for 24 hours

2. **Version Comparison:**
   ```php
   Current: 4.1.1 (from style.css)
   Remote:  4.1.2 (from GitHub tag v4.1.2)
   Result:  Update available
   ```

3. **Update Display:**
   - Notice shows on themes page
   - "Update Available" link appears in theme row
   - Click triggers WordPress native updater

4. **Download & Install:**
   - WordPress downloads ZIP from GitHub
   - `upgrader_source_selection` filter fixes folder name
   - Theme updated in place
   - Cache cleared

### Manual Force Check

Users can force update check:

```
Admin Dashboard → Check Updates Now
  ↓
themes.php?ane_force_check=1
  ↓
Delete transients
  ↓
Fresh API call
  ↓
Show "Update check completed!" notice
```

### Debug Mode

Add `?ane_debug=1` to themes page URL to see:
- Current version
- Update transient data
- Remote version info
- Package URL

## Security Considerations

### API Rate Limits

GitHub API allows 60 requests/hour for unauthenticated requests.

**Protection:**
- 24-hour transient cache
- Only checks when WordPress triggers update check
- Manual check available but rate limited by user action

**For higher limits (optional):**

```php
$response = wp_remote_get(
    $this->github_api_url,
    array(
        'timeout' => 10,
        'headers' => array(
            'Accept' => 'application/vnd.github.v3+json',
            'Authorization' => 'token YOUR_GITHUB_PAT', // Personal Access Token
        ),
    )
);
```

### Private Repositories

For private repos, use GitHub Personal Access Token:

1. Generate token: Settings → Developer settings → Personal access tokens
2. Permissions: `repo` (full control)
3. Add to headers:

```php
'Authorization' => 'token ghp_xxxxxxxxxxxx'
```

**Security:** Store token in wp-config.php, not in theme files:

```php
// In wp-config.php
define( 'MAKHADANE_GITHUB_TOKEN', 'ghp_xxxxxxxxxxxx' );

// In updater.php
'Authorization' => 'token ' . MAKHADANE_GITHUB_TOKEN
```

### ZIP File Verification

Currently no checksum verification. For enhanced security, add SHA256 verification:

1. **Generate checksum when creating release:**
```bash
shasum -a 256 makhadane-4.1.2.zip > makhadane-4.1.2.zip.sha256
```

2. **Upload checksum file as asset**

3. **Verify in updater:**
```php
private function verify_package( $package_url, $checksum_url ) {
    // Download checksum
    $checksum_response = wp_remote_get( $checksum_url );
    $expected_hash = trim( wp_remote_retrieve_body( $checksum_response ) );

    // Download package
    $package_response = wp_remote_get( $package_url );
    $package_data = wp_remote_retrieve_body( $package_response );

    // Verify
    $actual_hash = hash( 'sha256', $package_data );

    return hash_equals( $expected_hash, $actual_hash );
}
```

## Testing Checklist

### Before Release

- [ ] Version updated in style.css
- [ ] All changes committed to GitHub
- [ ] ZIP created with correct folder structure
- [ ] ZIP tested by extracting locally
- [ ] GitHub release created with correct tag format
- [ ] Asset uploaded with correct naming
- [ ] Release published (not draft)

### After Release

- [ ] Visit WordPress admin → Themes
- [ ] Check for update notice
- [ ] Click "Check Updates Now" from dashboard
- [ ] Verify update appears
- [ ] Test update process
- [ ] Verify files updated correctly
- [ ] Test site functionality
- [ ] Check version in Appearance → Themes

### Debug Process

1. **Enable debug mode:**
   ```
   themes.php?ane_debug=1
   ```

2. **Check transient data:**
   ```php
   get_transient( 'makhadane_update_check' );
   get_site_transient( 'update_themes' );
   ```

3. **Force fresh check:**
   ```
   themes.php?ane_force_check=1
   ```

4. **Clear all caches:**
   ```php
   delete_transient( 'makhadane_update_check' );
   delete_site_transient( 'update_themes' );
   ```

## Troubleshooting

### Update Not Showing

**Problem:** New release created but no update notification

**Solutions:**
1. Clear cache: `themes.php?ane_force_check=1`
2. Check tag format: Must be `v4.1.2` not `4.1.2`
3. Verify asset naming: Must be `makhadane-4.1.2.zip`
4. Check version comparison: Remote > Current
5. Enable debug mode: `themes.php?ane_debug=1`

### Wrong Folder Name After Update

**Problem:** Theme folder becomes `makhadane-v4.1.2/` or `makhadane-abc123/`

**Cause:** Using GitHub's automatic zipball instead of custom ZIP asset

**Solution:**
- Always upload custom ZIP with `makhadane/` at root
- `fix_source_folder()` method handles renaming
- Never use `zipball_url` from GitHub API

### API Rate Limit Hit

**Problem:** `X-RateLimit-Remaining: 0` in API response

**Solutions:**
1. Add GitHub Personal Access Token
2. Increase cache duration (currently 24 hours)
3. Wait 1 hour for rate limit reset
4. Use authenticated requests (5000/hour limit)

### Private Repository Access

**Problem:** 404 error when accessing private repo releases

**Solution:**
```php
// In wp-config.php
define( 'MAKHADANE_GITHUB_TOKEN', 'ghp_xxxxxxxxxxxx' );

// In updater.php constructor
private function __construct() {
    // ... existing code ...

    // Check for private repo token
    if ( defined( 'MAKHADANE_GITHUB_TOKEN' ) ) {
        add_filter( 'http_request_args', array( $this, 'add_github_auth' ), 10, 2 );
    }
}

public function add_github_auth( $args, $url ) {
    if ( strpos( $url, 'api.github.com' ) !== false ) {
        $args['headers']['Authorization'] = 'token ' . MAKHADANE_GITHUB_TOKEN;
    }
    return $args;
}
```

## Best Practices

### Version Numbering

Follow Semantic Versioning (semver.org):

- `MAJOR.MINOR.PATCH` (e.g., 4.1.2)
- MAJOR: Breaking changes
- MINOR: New features, backward compatible
- PATCH: Bug fixes

**Examples:**
- `4.1.1` → `4.1.2` - Bug fix
- `4.1.2` → `4.2.0` - New features
- `4.2.0` → `5.0.0` - Breaking changes

### Release Notes

Always include:
- Version number
- Date
- What's new
- What's fixed
- Breaking changes (if any)
- Upgrade instructions (if needed)

**Example:**
```markdown
# Makhadane v4.1.2 - 2026-01-15

## What's New
- Added Facebook Comments integration
- New linktree page template
- Auto-update system via GitHub

## Improvements
- Updated translation files (95% coverage)
- Improved heading hierarchy for SEO
- Enhanced schema.org markup

## Bug Fixes
- Fixed undefined function error in content-page.php
- Corrected text domain in linktree template

## Upgrade Notes
- No breaking changes
- Safe to update from 4.1.1
```

### Changelog Management

Maintain `CHANGELOG.md` in theme root:

```markdown
# Changelog

All notable changes to Makhadane theme will be documented in this file.

## [4.1.2] - 2026-01-15
### Added
- Facebook Comments integration
- Auto-update system

### Fixed
- Heading hierarchy in related posts

## [4.1.1] - 2026-01-01
### Initial Release
```

### Pre-Release Testing

Always test on staging environment:

1. Install current version
2. Create test content
3. Upload new release to GitHub
4. Force update check
5. Install update
6. Verify all functionality
7. Check for errors
8. Test rollback if needed

## Deployment Checklist

### Development → Production

- [ ] Code review completed
- [ ] All features tested
- [ ] No console errors
- [ ] No PHP errors/warnings
- [ ] Translation strings added
- [ ] Documentation updated
- [ ] Version bumped in style.css
- [ ] Git commit with version tag
- [ ] GitHub release created
- [ ] ZIP uploaded with correct naming
- [ ] Release notes written
- [ ] Changelog updated
- [ ] Staging environment tested
- [ ] Production update verified

## Support & Maintenance

### Regular Maintenance

**Monthly:**
- Check for WordPress compatibility
- Review GitHub API usage
- Monitor error logs
- Update dependencies if any

**Per Release:**
- Test update process
- Verify API endpoints
- Check cache behavior
- Review security

### Monitoring

**Log API Errors:**
```php
private function get_remote_version() {
    // ... existing code ...

    if ( is_wp_error( $response ) ) {
        error_log( 'Makhadane Update Check Failed: ' . $response->get_error_message() );
        return false;
    }

    // ... rest of code ...
}
```

**Track Update Success:**
```php
add_action( 'upgrader_process_complete', function( $upgrader, $options ) {
    if ( $options['type'] === 'theme' && in_array( 'makhadane', $options['themes'] ) ) {
        error_log( 'Makhadane theme updated to version ' . wp_get_theme()->get( 'Version' ) );
    }
}, 10, 2 );
```

## Advanced Features (Optional)

### Beta Channel

Support beta releases for testing:

```php
private $beta_mode = false; // Set via constant or option

private function get_remote_version() {
    // Use different API endpoint for beta
    $api_url = $this->beta_mode
        ? "https://api.github.com/repos/{$this->github_owner}/{$this->github_repo}/releases"
        : $this->github_api_url;

    // Filter for pre-release if beta mode
    // ... implementation ...
}
```

### Rollback Feature

Allow reverting to previous version:

```php
public function add_rollback_link( $actions, $theme ) {
    $previous_version = get_option( 'makhadane_previous_version' );

    if ( $previous_version ) {
        $actions['rollback'] = sprintf(
            '<a href="%s">%s</a>',
            wp_nonce_url( admin_url( 'admin.php?page=makhadane-rollback' ), 'makhadane-rollback' ),
            sprintf( __( 'Rollback to %s', 'makhadane' ), $previous_version )
        );
    }

    return $actions;
}
```

### Update Notification Email

Notify admin when update available:

```php
public function send_update_notification( $remote_version ) {
    $last_notification = get_option( 'makhadane_last_update_notification' );

    // Only send once per version
    if ( $last_notification === $remote_version['version'] ) {
        return;
    }

    wp_mail(
        get_option( 'admin_email' ),
        sprintf( __( 'Makhadane Theme Update Available: v%s', 'makhadane' ), $remote_version['version'] ),
        sprintf( __( 'A new version of Makhadane theme is available. Current: %s, New: %s', 'makhadane' ),
            $this->current_version,
            $remote_version['version']
        )
    );

    update_option( 'makhadane_last_update_notification', $remote_version['version'] );
}
```

## Quick Reference

### Key Files
```
makhadane/
├── inc/updater.php          # Main updater class
├── inc/admin.php            # Admin dashboard card
├── functions.php            # Auto-loads updater
├── style.css                # Version source
└── languages/makhadane.pot  # Translation strings
```

### GitHub Repository Structure
```
webaneid/makhadane/
├── releases/
│   └── v4.1.2/
│       └── makhadane-4.1.2.zip  # Release asset
└── tags/
    └── v4.1.2                    # Git tag
```

### Important URLs

- **API Endpoint:** `https://api.github.com/repos/webaneid/makhadane/releases/latest`
- **Update Check:** `wp-admin/themes.php?ane_force_check=1`
- **Debug Mode:** `wp-admin/themes.php?ane_debug=1`
- **GitHub Releases:** `https://github.com/webaneid/makhadane/releases`

### WordPress Hooks

```php
// Check for updates
add_filter( 'pre_set_site_transient_update_themes', ... );

// Fix folder name
add_filter( 'upgrader_source_selection', ... );

// Add action links
add_filter( 'theme_action_links_makhadane', ... );

// Admin notices
add_action( 'admin_footer', ... );
add_action( 'admin_init', ... );
```

### Transient Keys

```php
'makhadane_update_check'  // Cache for GitHub API response (24h)
'update_themes'           // WordPress core update check data
```

---

**Created:** 2026-01-01
**Theme:** Makhadane v4.1.1
**Based on:** Elemen Ane updater system
**Author:** Webane Indonesia
