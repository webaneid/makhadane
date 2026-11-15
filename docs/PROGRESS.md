# Progress

> Satu baris per fase. Status: `Planned` → `In Progress` → `Done`.
> Jangan mulai fase baru sebelum fase sebelumnya `Done` (lihat `SOP.md`).

| Fase | Nama | Status | Doc |
|---|---|---|---|
| 00 | Fondasi: git, SOP, aturan, tooling lint | Done | `phases/phase-00-fondasi.md` |
| 01 | Retrofit audit: overview arsitektur + RETROFIT-GAPS | Done | `phases/phase-01-retrofit-audit.md` |
| 02 | CI/CD Semantic Release, `.distignore`, & penguatan GitHub Auto-Updater (`inc/updater.php`) | Done | `phases/phase-02-updater-dan-ci.md` |
| 03 | Security fix Critical/High, perbaikan 13 error PHPStan (0 baseline), & hapus DB write per-request | Done | `phases/phase-03-security-dan-bugfix.md` |
| 04 | Pembersihan kode mati/duplikat, sinkronisasi taxonomy `kelas`, & penyeragaman text domain `makhadane` | Done | `phases/phase-04-pembersihan-dan-optimasi.md` |
| 05 | Auto-convert upload gambar ke WebP & auto-crop sub-sizes (`inc/webp-converter.php`) | Done | `phases/phase-05-auto-convert-webp.md` |
| 06 | Optimasi & konsolidasi JavaScript (1 file `js/makhadane.js` & conditional enqueue) | Done | `phases/phase-06-optimasi-javascript.md` |
| 07 | Optimasi CSS: struktur 12 bagian `css/main.css` & konsolidasi 1 file `css/main.min.css` | Done | `phases/phase-07-optimasi-css-12-bagian.md` |
| 08 | Migrasi icon `FontAne` & FontAwesome ke Inline SVG (`makhadane_icon()`) + hapus `fonts/` | Done | `phases/phase-08-migrasi-icon-svg.md` |
| 09 | Audit, pembersihan dead CSS (`0 dead CSS rules`), & penghapusan folder `scss/` | Done | `phases/phase-09-pembersihan-dan-deduplikasi-css.md` |
| 10 | Bundling & importer ACF JSON (`inc/acf-importer.php`) + verifikasi `composer dist:check` | Done | `phases/phase-10-acf-json-importer.md` |
| 11 | Penerapan Standar `webane-pagespeed-cwv` (Preload LCP, Zero-CLS Slider, Async CSS, Defer JS, & Aksesibilitas 100) | Done | `phases/phase-11-standar-pagespeed-cwv.md` |
