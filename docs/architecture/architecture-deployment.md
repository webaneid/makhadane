# Architecture — Deployment, Versioning, & Auto-Update

> Mengatur bagaimana perubahan kode di repo ini berubah menjadi rilis versi baru
> dan terdistribusi ke seluruh situs pelanggan secara otomatis. Lihat juga
> [ADR-0002](../decisions/adr-0002-versioning-dan-rilis.md) dan
> [ADR-0003](../decisions/adr-0003-auto-update-github-private.md).

## 1. Alur End-to-End

```mermaid
flowchart LR
    A["Branch feat/* atau fix/*"] -->|PR + CI lolos| B["Merge ke main"]
    B --> C["GitHub Actions: release.yml"]
    C --> D["semantic-release: bump style.css + CHANGELOG.md + tag vX.Y.Z"]
    D --> E["Build makhadane-X.Y.Z.zip (via .distignore)"]
    E --> F["GitHub Release + Asset ZIP"]
    F --> G["Situs Pelanggan: inc/updater.php"]
    G --> H["Notifikasi Update di wp-admin → 1-Click Update"]
```

## 2. CI Validation (`.github/workflows/ci.yml`)
Setiap push ke branch apa pun dan setiap Pull Request ke `main` menjalankan:
1. `composer install`
2. `composer lint:syntax` — cek sintaks seluruh file PHP
3. `composer lint:changed` — cek WordPress Coding Standards pada file yang diubah terhadap `main`
4. `composer analyse` — cek PHPStan (level 3 + baseline)

PR tidak boleh di-merge jika salah satu langkah gagal.

## 3. Release Otomatis (`.github/workflows/release.yml` + `.releaserc.json`)
Berjalan otomatis setiap ada push/merge ke `main`:
1. **Analisis commit** sejak tag terakhir menggunakan Conventional Commits:
   - `fix(...)` / `perf(...)` → kenaikan **patch** (`4.1.3` → `4.1.4`)
   - `feat(...)` → kenaikan **minor** (`4.1.3` → `4.2.0`)
   - `BREAKING CHANGE:` → kenaikan **major** (`4.1.3` → `5.0.0`)
   - `docs` / `chore` / `refactor` / `style` / `test` → **tidak membuat rilis baru**.
2. **Update file versi:** skrip rilis memperbarui header `Version:` di `style.css` dan menambahkan catatan rilis ke `CHANGELOG.md`, lalu melakukan commit `chore(release): X.Y.Z [skip ci]`.
3. **Build paket distribusi (`makhadane-X.Y.Z.zip`):**
   - Menyalin isi repo ke folder `dist/makhadane/` dengan mengecualikan semua entri di `.distignore` (tanpa `docs/`, `vendor/`, `.github/`, `AGENTS.md`, dll).
   - Membungkus folder `makhadane/` ke dalam file ZIP sehingga ketika diekstrak oleh WordPress, struktur foldernya langsung `wp-content/themes/makhadane/`.
4. **Publish GitHub Release:** melampirkan `makhadane-X.Y.Z.zip` sebagai release asset.

## 4. Mekanisme Auto-Update di Situs Pelanggan (`inc/updater.php`)

### 4.1 Konfigurasi Konstanta
Di awal `inc/updater.php`:
- `MAKHADANE_UPDATER_REPO` = `'webaneid/makhadane'`
- `MAKHADANE_UPDATER_SLUG` = `'makhadane'`

### 4.2 Urutan Pembacaan Token GitHub
`makhadane_updater_get_token()` membaca token dengan prioritas:
1. Konstanta `MAKHADANE_UPDATER_TOKEN` di `wp-config.php` (direkomendasikan agar token tidak tersimpan di DB dan tidak terlihat di wp-admin).
2. ACF Option `ane_updater_token` di menu **Webane Settings → Update**.

Token menggunakan **GitHub Fine-grained Personal Access Token** dengan permission tunggal: **Contents: Read-only** hanya pada repo `webaneid/makhadane`.

### 4.3 Siklus Cek & Instalasi
1. **Cache 12 jam:** Data rilis disimpan di transient `makhadane_github_release` selama 12 jam (atau 1 jam jika respons error agar tidak membanjiri API GitHub).
2. **Hook `pre_set_site_transient_update_themes`:** Membandingkan versi rilis GitHub dengan versi `style.css` saat ini menggunakan `version_compare()`.
3. **Hook `http_request_args`:** Menyisipkan header `Authorization: token ...` dan `Accept: application/octet-stream` **hanya** untuk URL API/asset GitHub milik repo `MAKHADANE_UPDATER_REPO`.
4. **Hook `upgrader_source_selection`:** Memastikan nama folder hasil ekstraksi selalu `makhadane/`.
5. **Tombol "Cek Update Sekarang":** Menggunakan `wp_nonce_url()` + `check_admin_referer( 'makhadane_force_check' )` dan `current_user_can( 'update_themes' )`.
