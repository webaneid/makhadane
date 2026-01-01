# Release Guide - Makhadane Theme

Panduan lengkap untuk membuat release baru theme Makhadane di GitHub.

## Persiapan Sebelum Release

### 1. Update Version Number

Edit file `style.css` line 4:

```css
Version: 4.1.2
```

### 2. Update CHANGELOG.md

Tambahkan entry baru di `CHANGELOG.md`:

```markdown
## [4.1.2] - 2026-01-15

### Added
- Feature baru yang ditambahkan

### Fixed
- Bug yang diperbaiki

### Changed
- Perubahan yang dilakukan
```

### 3. Commit Changes

```bash
git add style.css CHANGELOG.md
git commit -m "Bump version to 4.1.2"
git push origin main
```

## Membuat Release ZIP

### Method 1: Manual ZIP Creation

Dari parent directory theme:

```bash
cd /Users/webane/sites/makhadane/wp-content/themes/

# Create ZIP with correct structure
zip -r makhadane-4.1.2.zip makhadane/ \
  -x "makhadane/.git/*" \
  "makhadane/.gitignore" \
  "makhadane/node_modules/*" \
  "makhadane/.DS_Store" \
  "makhadane/scss/*.map" \
  "makhadane/css/*.map"

# Verify ZIP structure
unzip -l makhadane-4.1.2.zip | head -20
```

**PENTING:** ZIP harus contain folder `makhadane/` di root level!

### Method 2: Using Script

Buat file `create-release.sh`:

```bash
#!/bin/bash

VERSION=$1

if [ -z "$VERSION" ]; then
    echo "Usage: ./create-release.sh 4.1.2"
    exit 1
fi

cd /Users/webane/sites/makhadane/wp-content/themes/

# Create ZIP
zip -r "makhadane-${VERSION}.zip" makhadane/ \
  -x "makhadane/.git/*" \
  "makhadane/.gitignore" \
  "makhadane/node_modules/*" \
  "makhadane/.DS_Store" \
  "makhadane/scss/*.map" \
  "makhadane/css/*.map"

echo "✅ Created makhadane-${VERSION}.zip"
ls -lh "makhadane-${VERSION}.zip"
```

Jalankan:

```bash
chmod +x create-release.sh
./create-release.sh 4.1.2
```

## Membuat GitHub Release

### Method 1: Automatic via GitHub Actions (Recommended)

**Cara Tercepat dan Terotomasi!**

1. **Update version di style.css**
2. **Update CHANGELOG.md**
3. **Commit dan push:**
   ```bash
   git add style.css CHANGELOG.md
   git commit -m "Bump version to 4.1.2"
   git push origin main
   ```

4. **Create dan push tag:**
   ```bash
   git tag v4.1.2
   git push origin v4.1.2
   ```

**GitHub Actions akan otomatis:**
- ✅ Compile SCSS menjadi CSS
- ✅ Buat folder distribusi (exclude .git, node_modules, scss, dll)
- ✅ Create ZIP dengan nama `makhadane-4.1.2.zip`
- ✅ Buat GitHub Release dengan tag `v4.1.2`
- ✅ Upload ZIP sebagai release asset
- ✅ Link ke CHANGELOG.md

**Lihat progress di:**
```
https://github.com/webaneid/makhadane/actions
```

### Method 2: Via GitHub CLI

```bash
# Create release with ZIP
gh release create v4.1.2 \
  --title "Makhadane v4.1.2" \
  --notes-file RELEASE_NOTES.md \
  makhadane-4.1.2.zip

# Or with inline notes
gh release create v4.1.2 \
  --title "Makhadane v4.1.2" \
  --notes "Bug fixes and improvements. See CHANGELOG.md for details." \
  makhadane-4.1.2.zip
```

### Method 2: Via GitHub Web Interface

1. **Go to GitHub Repository**
   ```
   https://github.com/webaneid/makhadane/releases/new
   ```

2. **Fill Release Form:**
   - **Tag version:** `v4.1.2`
   - **Release title:** `Makhadane v4.1.2`
   - **Description:** Copy dari CHANGELOG.md

3. **Upload ZIP Asset:**
   - Click "Attach binaries"
   - Upload `makhadane-4.1.2.zip`
   - Verify filename exactly: `makhadane-4.1.2.zip`

4. **Publish Release**
   - Click "Publish release"

## Verifikasi Release

### 1. Cek GitHub Release Page

```
https://github.com/webaneid/makhadane/releases
```

Pastikan:
- ✅ Tag: `v4.1.2`
- ✅ Title: `Makhadane v4.1.2`
- ✅ Asset: `makhadane-4.1.2.zip` ada dan bisa didownload

### 2. Test GitHub API

```bash
curl -s "https://api.github.com/repos/webaneid/makhadane/releases/latest" | jq '.'
```

Response harus contain:
```json
{
  "tag_name": "v4.1.2",
  "assets": [
    {
      "name": "makhadane-4.1.2.zip",
      "browser_download_url": "https://github.com/webaneid/makhadane/releases/download/v4.1.2/makhadane-4.1.2.zip"
    }
  ]
}
```

### 3. Test di WordPress

1. **Force Check Update:**
   ```
   wp-admin/themes.php?ane_force_check=1
   ```

2. **Debug Mode:**
   ```
   wp-admin/themes.php?ane_debug=1
   ```

   Harus tampil:
   ```
   Current version: 4.1.1
   Update data: Array (
     [theme] => makhadane
     [new_version] => 4.1.2
     [package] => https://github.com/.../makhadane-4.1.2.zip
   )
   ```

3. **Check Themes Page:**
   - Notice: "Makhadane Theme Update Available!"
   - Link: "Update Available"

4. **Test Update Process:**
   - Click "Update now"
   - WordPress download ZIP
   - Extract dan install
   - Verify version di Appearance → Themes

## Release Checklist

### Pre-Release
- [ ] Version updated in `style.css`
- [ ] CHANGELOG.md updated
- [ ] All changes committed
- [ ] Code tested thoroughly
- [ ] Translation files up to date
- [ ] No console errors
- [ ] No PHP errors/warnings

### Creating Release
- [ ] ZIP created with correct structure
- [ ] ZIP tested by extracting locally
- [ ] Folder name is exactly `makhadane/`
- [ ] GitHub release created
- [ ] Tag format: `vX.Y.Z` (e.g., `v4.1.2`)
- [ ] ZIP uploaded as asset
- [ ] Asset name: `makhadane-X.Y.Z.zip`
- [ ] Release published (not draft)

### Post-Release
- [ ] GitHub API returns correct data
- [ ] WordPress detects update
- [ ] Force check works
- [ ] Debug mode shows update data
- [ ] Update process completes successfully
- [ ] Files updated correctly
- [ ] Site functionality intact
- [ ] Version shown correctly in admin

## Troubleshooting

### ZIP Structure Wrong

**Problem:** Update gagal karena folder name salah

**Solution:**
```bash
# Extract dan check structure
unzip -l makhadane-4.1.2.zip | head -5

# Should show:
# makhadane/style.css
# makhadane/functions.php
# makhadane/inc/...

# NOT:
# makhadane-4.1.2/style.css  ❌
# makhadane-main/style.css   ❌
```

### Update Not Detected

**Problem:** WordPress tidak detect update

**Solutions:**

1. **Clear cache:**
   ```
   themes.php?ane_force_check=1
   ```

2. **Check tag format:**
   - Must be `v4.1.2` not `4.1.2`

3. **Check asset name:**
   - Must be `makhadane-4.1.2.zip`
   - Must match pattern `makhadane-*.zip`

4. **Check version compare:**
   - Remote must be > Current
   - `4.1.2 > 4.1.1` ✅
   - `4.1.1 > 4.1.1` ❌

### GitHub API 404

**Problem:** API returns 404 Not Found

**Cause:** No releases exist yet

**Solution:**
1. Create first release
2. Must have at least one published release
3. Draft releases don't count

## Release Notes Template

File: `RELEASE_NOTES.md`

```markdown
# Makhadane v4.1.2

**Release Date:** 2026-01-15

## What's New

- Feature 1: Description
- Feature 2: Description
- Feature 3: Description

## Improvements

- Improvement 1
- Improvement 2

## Bug Fixes

- Fix 1: Description
- Fix 2: Description

## Breaking Changes

None

## Upgrade Instructions

Safe to update from v4.1.1. No manual steps required.

1. Go to wp-admin → Themes
2. Click "Update Available"
3. Wait for update to complete
4. Verify site functionality

## Requirements

- WordPress: 4.7+
- PHP: 7.4+
- ACF Pro: Latest version recommended

## Support

For issues or questions:
- Email: hello@webane.com
- GitHub: https://github.com/webaneid/makhadane/issues

---

**Full Changelog:** https://github.com/webaneid/makhadane/blob/main/CHANGELOG.md
```

## Quick Reference Commands

```bash
# Update version
sed -i '' 's/Version: 4.1.1/Version: 4.1.2/' style.css

# Create ZIP
cd /Users/webane/sites/makhadane/wp-content/themes/
zip -r makhadane-4.1.2.zip makhadane/ -x "makhadane/.git/*" "makhadane/node_modules/*" "makhadane/.DS_Store"

# Create GitHub release (requires gh CLI)
gh release create v4.1.2 \
  --title "Makhadane v4.1.2" \
  --notes "Release notes here" \
  makhadane-4.1.2.zip

# Test API
curl -s "https://api.github.com/repos/webaneid/makhadane/releases/latest" | jq '.tag_name, .assets[].name'

# WordPress force check
open "http://localhost/wp-admin/themes.php?ane_force_check=1"

# WordPress debug mode
open "http://localhost/wp-admin/themes.php?ane_debug=1"
```

## Best Practices

1. **Always test in staging first**
2. **Follow semantic versioning**
   - MAJOR.MINOR.PATCH (4.1.2)
   - MAJOR: Breaking changes
   - MINOR: New features
   - PATCH: Bug fixes

3. **Keep CHANGELOG.md updated**
4. **Write clear release notes**
5. **Test ZIP before uploading**
6. **Verify update process**
7. **Backup before major updates**

---

**Documentation Version:** 1.0
**Last Updated:** 2026-01-01
**Author:** Webane Indonesia
