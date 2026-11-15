# ADR-0002: Versioning & Rilis Otomatis

**Status:** Accepted
**Tanggal:** 2026-11-15

## Context
Makhadane sudah di versi `4.1.3` dan terpasang di situs pelanggan. Rilis manual
rawan lupa menaikkan `Version:` di `style.css` atau menyertakan file internal
(`.md`, `docs/`, `scss/`) ke dalam paket ZIP pelanggan.

## Decision
1. **SemVer**, melanjutkan dari `4.1.3`. Tag berformat `vX.Y.Z`.
2. **Conventional Commits wajib** (`docs/conventions.md`).
3. **semantic-release** berjalan di GitHub Actions setiap push ke `main`:
   - menentukan versi dari commit sejak tag terakhir (`fix`/`perf` → patch,
     `feat` → minor, `BREAKING CHANGE` → major);
   - menulis versi ke `style.css` (`Version:`), memperbarui `CHANGELOG.md`,
     commit `chore(release): X.Y.Z [skip ci]`, membuat tag;
   - build ZIP bersih berdasarkan `.distignore` dengan folder root bernama
     slug theme (`makhadane/`), lalu menjadikannya asset GitHub Release.
4. `MAKHADANE_VERSION` di PHP dibaca dari header `style.css`
   (`wp_get_theme()->get( 'Version' )`) — satu sumber kebenaran.
5. `.distignore` adalah satu-satunya daftar file yang tidak ikut ZIP.

## Alternatif yang Dipertimbangkan
- Tag manual — ditolak: rawan human error dan drift versi.

## Konsekuensi
- Merge ke `main` = rilis. Disiplin commit message menjadi krusial.
- Commit `docs:`/`chore:`/`refactor:` bisa di-merge tanpa memicu update di
  situs pelanggan.
