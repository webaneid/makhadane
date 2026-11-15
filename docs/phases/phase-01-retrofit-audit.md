# Fase 01 — Retrofit Audit: Architecture Overview & Gap Analysis

**Status:** Done
**Jenis:** Normal (murni dokumentasi & audit, tidak mengubah kode runtime)
**Mulai:** 2026-11-15
**Selesai:** 2026-11-15

## Tujuan
Mengikuti prinsip `retrofit-adopt` standar Webane (`sekolahane`, `newsane`,
`elemenane`, `jalawarta`): sebelum mengubah kode runtime apa pun di theme yang
sudah berjalan di production (`v4.1.3`), dokumentasikan arsitektur aktual
(`docs/architecture/architecture-overview.md`) berdasarkan sumber kode primer,
dan daftar seluruh kesenjangan antara kondisi kode saat ini dengan standar baru
(`docs/RETROFIT-GAPS.md`) beserta tingkat keparahan dan alokasi fase
perbaikannya.

## Scope
- [x] Audit menyeluruh seluruh file PHP (`functions.php`, `inc/*.php`, `inc/admin/*.php`, template root, `tp/**/*.php`), JS, CSS, dan SCSS
- [x] Tulis `docs/architecture/architecture-overview.md` (siklus muat PHP, kontrak DB, CPT/taxonomy, ACF, template routing, aset)
- [x] Tulis `docs/RETROFIT-GAPS.md` (`GAP-01` s/d `GAP-22`: 2 Critical, 4 High, 10 Medium, 6 Low)
- [x] Susun peta fase retrofit di `docs/PROGRESS.md` dan tutup Fase 01

## Di Luar Scope
- Mengubah kode PHP/JS/CSS runtime apa pun (dikerjakan mulai Fase 02).

## Referensi
- `AGENTS.md`
- `docs/architecture/architecture-security.md`
- `phpstan-baseline.neon`

## Keputusan Kecil Selama Eksekusi
- Subfolder `inc/admin/*.php` (13 file hasil porting dari `elemenane`: custom dashboard, design tokens, facebook comments, linktree analytics, user profile chart) didokumentasikan secara eksplisit di `architecture-overview.md`, termasuk bug nama hook `elemen-ane_page_ane-linktree-analytics` dan text domain `'elemenane'` yang akan dibersihkan di Fase 03–04.
- Inkonsistensi case antara `register_taxonomy( 'kelas', ... )` (lowercase) di `inc/custompost.php` dan `taxonomy-Kelas.php` / `tp/single-ustadz.php` (`'Kelas'`) dicatat sebagai `GAP-11` agar aman di server Linux case-sensitive.

## Checklist Penutupan (SOP)
- [x] `composer lint:changed` & `composer analyse` lolos
- [x] Tidak ada perubahan pada file runtime theme
- [x] `PROGRESS.md` diupdate ke `Done`
- [x] Commit `docs(retrofit): ...`

## Known Limitations
- Definisi field group ACF utama belum disimpan di repo (`inc/acf-json/` akan dibundel di Fase 10); daftar field di `architecture-overview.md` diekstrak dari pemanggilan `get_field()`/`get_sub_field()`/`have_rows()` di seluruh template.

## Ringkasan Hasil
Arsitektur aktual `makhadane` (`v4.1.3`) telah didokumentasikan di `docs/architecture/architecture-overview.md` dan 22 gap teridentifikasi di `docs/RETROFIT-GAPS.md` (2 Critical, 4 High, 10 Medium, 6 Low) lengkap dengan lokasi baris dan peta fase perbaikannya.
