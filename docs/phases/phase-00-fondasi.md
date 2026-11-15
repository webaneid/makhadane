# Fase 00 — Fondasi: Git, SOP, Aturan, Tooling

**Status:** Done
**Jenis:** Normal (tidak mengubah perilaku runtime theme)
**Mulai:** 2026-11-15
**Selesai:** 2026-11-15

## Tujuan
Menyiapkan fondasi kerja sebelum menyentuh kode runtime: aturan tunggal
(`AGENTS.md`), SOP 6 langkah (`docs/SOP.md`), ADR-0001..0004, dan alat cek
otomatis (`composer.json`, PHPCS WPCS, PHPStan level 3 + baseline, skrip
`bin/`).

## Scope
- [x] Commit perubahan baseline yang belum di-stage (`517b583`)
- [x] `.gitignore`, `.gitattributes`, `.distignore`
- [x] `AGENTS.md` (sumber aturan tunggal) + `CLAUDE.md` (`@AGENTS.md`) + `README.md`
- [x] Hapus dokumen `.md` terpisah warisan lama di root (`ADMIN-DASHBOARD-GUIDE.md`, `RELEASE-GUIDE.md`, `SEO-IMPLEMENTATION-GUIDE.md`, `SEO-MAKHADANE-CHANGES.md`, `auto-update-guide.md`) dan konsolidasikan ke `docs/`
- [x] `docs/SOP.md`, `conventions.md`, `PROGRESS.md`, `lessons-learned.md`, `glossary.md`
- [x] `docs/architecture/architecture-security.md`, `architecture-css.md`, `architecture-deployment.md`
- [x] ADR-0001..0004 di `docs/decisions/`
- [x] `composer.json` + PHPCS (WPCS) + PHPStan level 3 + baseline (`phpstan-baseline.neon`)
- [x] Skrip `bin/lint-changed.sh`, `bin/security-scan.sh`, `bin/css-audit.py`, `bin/check-dist.sh`

## Di Luar Scope
- Mengubah kode PHP/JS/CSS runtime apa pun (dikerjakan di Fase 02+).
- Audit arsitektur lengkap & daftar gap (dikerjakan di Fase 01).

## Referensi
- `docs/decisions/adr-0001..0004`
- Standar referensi: `~/sites/sekolahane/wp-content/themes/sekolahane`, `~/sites/newsane/wp-content/themes/newsane`

## Keputusan Kecil Selama Eksekusi
- PHPStan mulai di **level 3** + baseline (`phpstan-baseline.neon`): kode lama
  di-freeze agar tidak memblokir, sedangkan kode baru tidak boleh menambah
  error. Target Fase 03 adalah mengosongkan baseline hingga 0 error.
- PHPCS memakai ruleset `WordPress` penuh + `PHPCompatibilityWP` (target PHP `8.0-`, WP `6.0+`), dengan pengecualian nama file template hierarchy dan Yoda condition.
- Prefix baru ditetapkan `makhadane_` (konstanta `MAKHADANE_`) dan text domain tunggal `'makhadane'`.

## Checklist Penutupan (SOP)
- [x] `composer lint:syntax` & `composer analyse` berjalan normal
- [x] Tidak ada perubahan perilaku runtime theme
- [x] `PROGRESS.md` diupdate
- [x] Commit `chore(foundation): ...` (tidak memicu rilis)

## Known Limitations
- Kode warisan saat ini memiliki 3.485 error + 423 warning WPCS (2.915 dapat di-autofix via `phpcbf`) dan 13 error PHPStan di `phpstan-baseline.neon` — seluruhnya diinventarisasi di Fase 01 (`docs/RETROFIT-GAPS.md`) dan diperbaiki bertahap di Fase 02–04.

## Ringkasan Hasil
Fondasi aturan tunggal `AGENTS.md`/`CLAUDE.md`, dokumentasi `docs/`, SOP 6 langkah, ADR-0001..0004, serta PHPCS (WPCS) + PHPStan level 3 dengan baseline telah aktif tanpa menyentuh kode runtime theme.
